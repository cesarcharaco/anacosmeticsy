<?php
// app/Models/Credito.php

require_once BASE_PATH . 'app/Config/Database.php';

class Credito {

    public static function obtenerTodos() {
        $db = Database::connect();
        // Consultamos las ventas que fueron procesadas a crédito directo
        $stmt = $db->query("
            SELECT v.id, v.cliente_id, v.usuario_id, v.total_general_usd, 
                   v.monto_abonado_credito_usd AS abono_inicial_usd, 
                   v.monto_pendiente_credito_usd AS saldo_pendiente_usd,
                   CASE WHEN v.monto_pendiente_credito_usd <= 0.01 THEN 'pagado' ELSE 'pendiente' END AS estado,
                   v.created_at, cl.nombre AS cliente_nombre, cl.telefono AS cliente_telefono,
                   u.nombre AS usuario_nombre
            FROM ventas v
            INNER JOIN clientes cl ON v.cliente_id = cl.id
            INNER JOIN usuarios u ON v.usuario_id = u.id
            WHERE v.tipo_venta = 'credito'
            ORDER BY v.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorCliente($clienteId) {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT v.id, v.cliente_id, v.usuario_id, v.total_general_usd, 
                   v.monto_abonado_credito_usd AS abono_inicial_usd, 
                   v.monto_pendiente_credito_usd AS saldo_pendiente_usd,
                   CASE WHEN v.monto_pendiente_credito_usd <= 0.01 THEN 'pagado' ELSE 'pendiente' END AS estado,
                   v.created_at, u.nombre AS usuario_nombre
            FROM ventas v
            INNER JOIN usuarios u ON v.usuario_id = u.id
            WHERE v.cliente_id = :cliente_id AND v.tipo_venta = 'credito'
            ORDER BY v.created_at DESC
        ");
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id) {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT v.id, v.cliente_id, v.usuario_id, v.total_general_usd, 
                   v.monto_abonado_credito_usd AS abono_inicial_usd, 
                   v.monto_pendiente_credito_usd AS saldo_pendiente_usd,
                   CASE WHEN v.monto_pendiente_credito_usd <= 0.01 THEN 'pagado' ELSE 'pendiente' END AS estado,
                   v.created_at, cl.nombre AS cliente_nombre, cl.telefono AS cliente_telefono, cl.saldo_favor_usd
            FROM ventas v
            INNER JOIN clientes cl ON v.cliente_id = cl.id
            WHERE v.id = :id AND v.tipo_venta = 'credito'
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerDetalles($creditoId) {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT vd.*, 
                   CASE 
                       WHEN vd.tipo_item = 'producto' THEN p.nombre 
                       WHEN vd.tipo_item = 'servicio' THEN s.nombre 
                   END AS item_nombre
            FROM ventas_detalles vd
            LEFT JOIN productos p ON vd.tipo_item = 'producto' AND vd.item_id = p.id
            LEFT JOIN servicios s ON vd.tipo_item = 'servicio' AND vd.item_id = s.id
            WHERE vd.venta_id = :credito_id
        ");
        $stmt->execute(['credito_id' => $creditoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerAbonos($creditoId) {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT ca.*, u.nombre AS usuario_nombre
            FROM creditos_abonos ca
            INNER JOIN usuarios u ON ca.usuario_id = u.id
            WHERE ca.credito_id = :credito_id
            ORDER BY ca.created_at DESC
        ");
        $stmt->execute(['credito_id' => $creditoId]);
        $abonos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($abonos as &$abono) {
            $stmtDet = $db->prepare("
                SELECT * FROM creditos_abono_detalles WHERE creditos_abono_id = :abono_id
            ");
            $stmtDet->execute(['abono_id' => $abono['id']]);
            $abono['detalles_pagos'] = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
        }

        return $abonos;
    }

    /**
     * Registrar abono a una venta a crédito, actualizando el saldo pendiente en la venta
     * y descontando la deuda actual del cliente.
     */
    public static function registrarAbono($data) {
        $db = Database::connect();
        
        try {
            $db->beginTransaction();

            $creditoId = filter_var($data['credito_id'], FILTER_VALIDATE_INT);
            $tasaBcv = filter_var($data['tasa_bcv'], FILTER_VALIDATE_FLOAT);
            $pagos = $data['pagos'] ?? []; 
            $usuarioId = $_SESSION['user_id'] ?? 1;

            if (!$creditoId || empty($pagos) || !is_array($pagos)) {
                throw new Exception("Datos de abono inválidos o estructura de pagos vacía.");
            }

            if (!$tasaBcv || $tasaBcv <= 0) {
                throw new Exception("Tasa BCV inválida para procesar conversiones.");
            }

            // 1. Obtener y bloquear la venta a crédito
            $stmtCred = $db->prepare("SELECT * FROM ventas WHERE id = :id AND tipo_venta = 'credito' FOR UPDATE");
            $stmtCred->execute(['id' => $creditoId]);
            $credito = $stmtCred->fetch();

            if (!$credito) {
                throw new Exception("El registro de crédito especificado no existe.");
            }

            $saldoPendiente = floatval($credito['monto_pendiente_credito_usd']);
            if ($saldoPendiente <= 0.01) {
                throw new Exception("Este crédito ya se encuentra totalmente pagado.");
            }

            $montoTotalAbonadoUsd = 0.00;
            $detallesParaInsertar = [];

            // 2. Procesar métodos de pago múltiples
            foreach ($pagos as $pago) {
                $montoIngresado = filter_var($pago['monto'] ?? 0, FILTER_VALIDATE_FLOAT);
                $metodoPago = trim($pago['metodo'] ?? '');
                $referencia = trim($pago['referencia'] ?? '');

                if ($montoIngresado === false || $montoIngresado <= 0) {
                    throw new Exception("Hay un monto de abono inválido en los métodos de pago ingresados.");
                }

                $metodosValidos = ['efectivo_usd', 'efectivo_bs', 'pago_movil', 'punto_venta', 'zelle', 'usdt'];
                if (!in_array($metodoPago, $metodosValidos)) {
                    throw new Exception("Método de pago no reconocido: " . htmlspecialchars($metodoPago));
                }

                $metodosEnBs = ['efectivo_bs', 'pago_movil', 'punto_venta'];
                $esBolivares = in_array($metodoPago, $metodosEnBs);

                $montoEquivalenteUsd = $esBolivares ? round($montoIngresado / $tasaBcv, 2) : round($montoIngresado, 2);
                $montoTotalAbonadoUsd += $montoEquivalenteUsd;

                $detallesParaInsertar[] = [
                    'metodo_pago'           => $metodoPago,
                    'monto_ingresado'       => $montoIngresado,
                    'monto_equivalente_usd' => $montoEquivalenteUsd,
                    'tasa_bcv'              => $tasaBcv,
                    'referencia'            => $referencia
                ];
            }

            if ($montoTotalAbonadoUsd <= 0) {
                throw new Exception("El monto total abonado debe ser mayor a cero.");
            }

            // 3. Registrar cabecera del abono
            $metodoPrincipal = $detallesParaInsertar[0]['metodo_pago'];
            $referenciaPrincipal = $detallesParaInsertar[0]['referencia'];

            $stmtAbono = $db->prepare("
                INSERT INTO creditos_abonos (credito_id, usuario_id, monto_abono_usd, metodo_pago, tasa_bcv, referencia)
                VALUES (:credito_id, :usuario_id, :monto_abono_usd, :metodo_pago, :tasa_bcv, :referencia)
            ");
            $stmtAbono->execute([
                'credito_id'       => $creditoId,
                'usuario_id'       => $usuarioId,
                'monto_abono_usd'  => $montoTotalAbonadoUsd,
                'metodo_pago'      => $metodoPrincipal,
                'tasa_bcv'         => $tasaBcv,
                'referencia'       => $referenciaPrincipal
            ]);
            $creditosAbonoId = $db->lastInsertId();

            // 4. Registrar detalles de abono
            $stmtDetalle = $db->prepare("
                INSERT INTO creditos_abono_detalles (creditos_abono_id, metodo_pago, monto_ingresado, monto_equivalente_usd, tasa_bcv, referencia)
                VALUES (:abono_id, :metodo_pago, :monto_ingresado, :monto_equivalente_usd, :tasa_bcv, :referencia)
            ");

            foreach ($detallesParaInsertar as $det) {
                $stmtDetalle->execute([
                    'abono_id'              => $creditosAbonoId,
                    'metodo_pago'           => $det['metodo_pago'],
                    'monto_ingresado'       => $det['monto_ingresado'],
                    'monto_equivalente_usd' => $det['monto_equivalente_usd'],
                    'tasa_bcv'              => $det['tasa_bcv'],
                    'referencia'            => $det['referencia']
                ]);
            }

            // 5. Calcular nuevo saldo pendiente y excedentes
            $exceso = 0.00;
            if ($montoTotalAbonadoUsd >= $saldoPendiente) {
                $exceso = round($montoTotalAbonadoUsd - $saldoPendiente, 2);
                $nuevoSaldoPendiente = 0.00;
                $montoDescontarDeuda = $saldoPendiente;
            } else {
                $nuevoSaldoPendiente = round($saldoPendiente - $montoTotalAbonadoUsd, 2);
                $montoDescontarDeuda = $montoTotalAbonadoUsd;
            }

            // 6. Actualizar la venta a crédito (saldo pendiente)
            $stmtUpd = $db->prepare("
                UPDATE ventas 
                SET monto_pendiente_credito_usd = :saldo_pendiente 
                WHERE id = :id
            ");
            $stmtUpd->execute([
                'saldo_pendiente' => $nuevoSaldoPendiente,
                'id'              => $creditoId
            ]);

            // 7. Actualizar el perfil del cliente (reducir deuda y sumar excedente a saldo a favor si aplica)
            if ($montoDescontarDeuda > 0) {
                $stmtCliDeuda = $db->prepare("UPDATE clientes SET deuda_actual_usd = GREATEST(0, deuda_actual_usd - :descuento) WHERE id = :cliente_id");
                $stmtCliDeuda->execute(['descuento' => $montoDescontarDeuda, 'cliente_id' => $credito['cliente_id']]);
            }

            if ($exceso > 0) {
                $stmtCliSaldo = $db->prepare("UPDATE clientes SET saldo_favor_usd = saldo_favor_usd + :exceso WHERE id = :cliente_id");
                $stmtCliSaldo->execute(['exceso' => $exceso, 'cliente_id' => $credito['cliente_id']]);
            }

            $db->commit();
            return true;

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public static function anularAbono($abonoId) {
        $db = Database::connect();

        try {
            $db->beginTransaction();

            $abonoId = filter_var($abonoId, FILTER_VALIDATE_INT);
            if (!$abonoId) {
                throw new Exception("ID de abono inválido.");
            }

            // 1. Obtener datos del abono
            $stmtAbono = $db->prepare("SELECT * FROM creditos_abonos WHERE id = :id FOR UPDATE");
            $stmtAbono->execute(['id' => $abonoId]);
            $abono = $stmtAbono->fetch();

            if (!$abono) {
                throw new Exception("El abono especificado no existe.");
            }

            $creditoId = $abono['credito_id'];
            $montoAbonoUsd = floatval($abono['monto_abono_usd']);

            // 2. Obtener venta asociada
            $stmtCred = $db->prepare("SELECT * FROM ventas WHERE id = :id AND tipo_venta = 'credito' FOR UPDATE");
            $stmtCred->execute(['id' => $creditoId]);
            $credito = $stmtCred->fetch();

            if (!$credito) {
                throw new Exception("La venta a crédito asociada no existe.");
            }

            $clienteId = $credito['cliente_id'];
            $saldoPendienteActual = floatval($credito['monto_pendiente_credito_usd']);
            $totalGeneral = floatval($credito['total_general_usd']);

            // 3. Calcular reversión de saldos
            $nuevoSaldoPendiente = $saldoPendienteActual + $montoAbonoUsd;
            $excesoARevertir = 0.00;

            if ($nuevoSaldoPendiente > $totalGeneral) {
                $excesoARevertir = $nuevoSaldoPendiente - $totalGeneral;
                $nuevoSaldoPendiente = $totalGeneral;
            }

            $montoDeudaARestaurar = $montoAbonoUsd - $excesoARevertir;

            // 4. Revertir saldo a favor del cliente si hubo excedente acreditado previamente
            if ($excesoARevertir > 0) {
                $stmtCli = $db->prepare("UPDATE clientes SET saldo_favor_usd = GREATEST(0, saldo_favor_usd - :exceso) WHERE id = :cliente_id");
                $stmtCli->execute(['exceso' => $excesoARevertir, 'cliente_id' => $clienteId]);
            }

            // 5. Restaurar la deuda actual del cliente
            if ($montoDeudaARestaurar > 0) {
                $stmtCliDeuda = $db->prepare("UPDATE clientes SET deuda_actual_usd = deuda_actual_usd + :restaurar WHERE id = :cliente_id");
                $stmtCliDeuda->execute(['restaurar' => $montoDeudaARestaurar, 'cliente_id' => $clienteId]);
            }

            // 6. Actualizar la venta a crédito con su saldo pendiente anterior
            $stmtUpdCred = $db->prepare("
                UPDATE ventas 
                SET monto_pendiente_credito_usd = :saldo_pendiente 
                WHERE id = :id
            ");
            $stmtUpdCred->execute([
                'saldo_pendiente' => $nuevoSaldoPendiente,
                'id'              => $creditoId
            ]);

            // 7. Eliminar registros del abono
            $stmtDelDet = $db->prepare("DELETE FROM creditos_abono_detalles WHERE creditos_abono_id = :abono_id");
            $stmtDelDet->execute(['abono_id' => $abonoId]);

            $stmtDelAbono = $db->prepare("DELETE FROM creditos_abonos WHERE id = :abono_id");
            $stmtDelAbono->execute(['abono_id' => $abonoId]);

            $db->commit();
            return true;

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
}