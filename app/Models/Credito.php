<?php
// app/Models/Credito.php

require_once BASE_PATH . 'app/Config/Database.php';

class Credito {

    public static function obtenerTodos() {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT cr.*, cl.nombre AS cliente_nombre, cl.telefono AS cliente_telefono,
                   u.nombre AS usuario_nombre
            FROM creditos cr
            INNER JOIN clientes cl ON cr.cliente_id = cl.id
            INNER JOIN usuarios u ON cr.usuario_id = u.id
            ORDER BY cr.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function obtenerPorCliente($clienteId) {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT cr.*, u.nombre AS usuario_nombre
            FROM creditos cr
            INNER JOIN usuarios u ON cr.usuario_id = u.id
            WHERE cr.cliente_id = :cliente_id
            ORDER BY cr.created_at DESC
        ");
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetchAll();
    }

    public static function obtenerPorId($id) {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT cr.*, cl.nombre AS cliente_nombre, cl.telefono AS cliente_telefono, cl.saldo_favor_usd
            FROM creditos cr
            INNER JOIN clientes cl ON cr.cliente_id = cl.id
            WHERE cr.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public static function obtenerDetalles($creditoId) {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT cd.*, 
                   CASE 
                       WHEN cd.tipo_item = 'producto' THEN p.nombre 
                       WHEN cd.tipo_item = 'servicio' THEN s.nombre 
                   END AS item_nombre
            FROM credito_detalles cd
            LEFT JOIN productos p ON cd.tipo_item = 'producto' AND cd.item_id = p.id
            LEFT JOIN servicios s ON cd.tipo_item = 'servicio' AND cd.item_id = s.id
            WHERE cd.credito_id = :credito_id
        ");
        $stmt->execute(['credito_id' => $creditoId]);
        return $stmt->fetchAll();
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

        // Adjuntar los detalles de los métodos de pago múltiples para cada abono
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
     * Método profesional para registrar un abono con múltiples métodos de pago mixtos,
     * conversión BCV y distribución automática de excedentes a saldo a favor.
     */
    public static function registrarAbono($data) {
        $db = Database::connect();
        
        try {
            $db->beginTransaction();

            $creditoId = filter_var($data['credito_id'], FILTER_VALIDATE_INT);
            $tasaBcv = filter_var($data['tasa_bcv'], FILTER_VALIDATE_FLOAT);
            $pagos = $data['pagos'] ?? []; // Arreglo de pagos múltiples
            $usuarioId = $_SESSION['user_id'] ?? 1;

            if (!$creditoId || empty($pagos) || !is_array($pagos)) {
                throw new Exception("Datos de abono inválidos o estructura de pagos vacía.");
            }

            if (!$tasaBcv || $tasaBcv <= 0) {
                throw new Exception("Tasa BCV inválida para procesar conversiones.");
            }

            // 1. Obtener y bloquear el crédito actual para evitar condiciones de carrera (Race Conditions)
            $stmtCred = $db->prepare("SELECT * FROM creditos WHERE id = :id FOR UPDATE");
            $stmtCred->execute(['id' => $creditoId]);
            $credito = $stmtCred->fetch();

            if (!$credito) {
                throw new Exception("El crédito especificado no existe.");
            }

            if ($credito['estado'] === 'pagado') {
                throw new Exception("Este crédito ya se encuentra totalmente pagado.");
            }

            $saldoPendiente = floatval($credito['saldo_pendiente_usd']);
            $montoTotalAbonadoUsd = 0.00;
            $detallesParaInsertar = [];

            // 2. Validar y procesar cada línea de pago del arreglo
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

                // Determinar si el método es en Bolívares o en Dólares para su conversión contable
                $metodosEnBs = ['efectivo_bs', 'pago_movil', 'punto_venta'];
                $esBolivares = in_array($metodoPago, $metodosEnBs);

                if ($esBolivares) {
                    $montoEquivalenteUsd = round($montoIngresado / $tasaBcv, 2);
                } else {
                    $montoEquivalenteUsd = round($montoIngresado, 2);
                }

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

            // 3. Registrar la cabecera del abono en creditos_abonos[cite: 8]
            // Usamos un método genérico o el primer método como principal para la tabla cabecera
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

            // 4. Registrar los detalles de los múltiples métodos de pago en creditos_abono_detalles
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

            // 5. Calcular distribución contable (Deuda vs Excedente a Saldo a Favor)
            if ($montoTotalAbonadoUsd >= $saldoPendiente) {
                $exceso = round($montoTotalAbonadoUsd - $saldoPendiente, 2);
                $nuevoSaldo = 0.00;
                $nuevoEstado = 'pagado';

                // Si el pago total supera la deuda, el excedente se suma al saldo a favor del cliente
                if ($exceso > 0) {
                    $stmtCli = $db->prepare("UPDATE clientes SET saldo_favor_usd = saldo_favor_usd + :exceso WHERE id = :cliente_id");
                    $stmtCli->execute(['exceso' => $exceso, 'cliente_id' => $credito['cliente_id']]);
                }
            } else {
                $nuevoSaldo = round($saldoPendiente - $montoTotalAbonadoUsd, 2);
                $nuevoEstado = 'pendiente';
            }

            // 6. Actualizar el saldo pendiente y estado en la tabla creditos[cite: 8]
            $stmtUpd = $db->prepare("
                UPDATE creditos 
                SET saldo_pendiente_usd = :saldo_pendiente, estado = :estado 
                WHERE id = :id
            ");
            $stmtUpd->execute([
                'saldo_pendiente' => $nuevoSaldo,
                'estado'          => $nuevoEstado,
                'id'              => $creditoId
            ]);

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

            // 2. Obtener el crédito asociado
            $stmtCred = $db->prepare("SELECT * FROM creditos WHERE id = :id FOR UPDATE");
            $stmtCred->execute(['id' => $creditoId]);
            $credito = $stmtCred->fetch();

            if (!$credito) {
                throw new Exception("El crédito asociado no existe.");
            }

            $clienteId = $credito['cliente_id'];
            $saldoPendienteActual = floatval($credito['saldo_pendiente_usd']);
            $totalGeneral = floatval($credito['total_general_usd']);

            // 3. Revertir lógicamente el impacto del abono
            // Si el crédito estaba totalmente pagado o hubo excedente que fue a saldo a favor, debemos calcularlo
            // Evaluamos cuánto de este abono se fue a deuda y cuánto se fue a saldo a favor del cliente.
            // Una forma robusta es verificar el estado anterior o recalcular el saldo pendiente sumando el abono.
            
            $nuevoSaldoPendiente = $saldoPendienteActual + $montoAbonoUsd;
            $excesoARevertir = 0.00;

            if ($nuevoSaldoPendiente > $totalGeneral) {
                $excesoARevertir = $nuevoSaldoPendiente - $totalGeneral;
                $nuevoSaldoPendiente = $totalGeneral;
            }

            // 4. Si hubo excedente acreditado a saldo a favor del cliente, lo descontamos de su cuenta
            if ($excesoARevertir > 0) {
                $stmtCli = $db->prepare("UPDATE clientes SET saldo_favor_usd = GREATEST(0, saldo_favor_usd - :exceso) WHERE id = :cliente_id");
                $stmtCli->execute(['exceso' => $excesoARevertir, 'cliente_id' => $clienteId]);
            }

            // 5. Actualizar el crédito (vuelve a estar pendiente o ajusta su saldo)
            $nuevoEstado = ($nuevoSaldoPendiente >= 0.01) ? 'pendiente' : 'pagado';
            $stmtUpdCred = $db->prepare("
                UPDATE creditos 
                SET saldo_pendiente_usd = :saldo_pendiente, estado = :estado 
                WHERE id = :id
            ");
            $stmtUpdCred->execute([
                'saldo_pendiente' => $nuevoSaldoPendiente,
                'estado'          => $nuevoEstado,
                'id'              => $creditoId
            ]);

            // 6. Eliminar los detalles de pagos y el abono cabecera (o marcarlo como anulado)
            // Borramos los registros para mantener la integridad contable estricta
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