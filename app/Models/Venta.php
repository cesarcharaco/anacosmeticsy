<?php
// app/Models/Venta.php

require_once BASE_PATH . 'app/Config/Database.php';

class Venta {

    /**
     * Obtener todas las ventas registradas con datos de cliente y usuario
     */
    public static function obtenerTodas() {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT v.*, cl.nombre AS cliente_nombre, cl.telefono AS cliente_telefono,
                   u.nombre AS usuario_nombre
            FROM ventas v
            INNER JOIN clientes cl ON v.cliente_id = cl.id
            INNER JOIN usuarios u ON v.usuario_id = u.id
            ORDER BY v.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener ventas filtradas por fecha, cliente y servicio para el Historial
     */
    public static function obtenerFiltradas($fechaInicio = null, $fechaFin = null, $clienteId = null, $servicioId = null) {
        $db = Database::connect();
        
        $sql = "
            SELECT DISTINCT v.*, cl.nombre AS cliente_nombre, cl.telefono AS cliente_telefono,
                   u.nombre AS usuario_nombre
            FROM ventas v
            INNER JOIN clientes cl ON v.cliente_id = cl.id
            INNER JOIN usuarios u ON v.usuario_id = u.id
        ";

        if (!empty($servicioId)) {
            $sql .= " INNER JOIN ventas_detalles vd ON v.id = vd.venta_id AND vd.tipo_item = 'servicio' AND vd.item_id = :servicio_id";
        }

        $sql .= " WHERE 1=1";
        
        $params = [];

        if (!empty($servicioId)) {
            $params['servicio_id'] = $servicioId;
        }

        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(v.created_at) >= :fecha_inicio";
            $params['fecha_inicio'] = $fechaInicio;
        }

        if (!empty($fechaFin)) {
            $sql .= " AND DATE(v.created_at) <= :fecha_fin";
            $params['fecha_fin'] = $fechaFin;
        }

        if (!empty($clienteId)) {
            $sql .= " AND v.cliente_id = :cliente_id";
            $params['cliente_id'] = $clienteId;
        }

        $sql .= " ORDER BY v.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una venta por su ID con sus detalles y pagos realizados
     */
    public static function obtenerPorId($id) {
        $db = Database::connect();
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) return null;

        $stmt = $db->prepare("
            SELECT v.*, cl.nombre AS cliente_nombre, cl.telefono AS cliente_telefono, cl.direccion AS cliente_direccion,
                   u.nombre AS usuario_nombre
            FROM ventas v
            INNER JOIN clientes cl ON v.cliente_id = cl.id
            INNER JOIN usuarios u ON v.usuario_id = u.id
            WHERE v.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $venta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($venta) {
            $stmtDet = $db->prepare("
                SELECT vd.*, 
                       CASE 
                           WHEN vd.tipo_item = 'producto' THEN p.nombre 
                           WHEN vd.tipo_item = 'servicio' THEN s.nombre 
                       END AS item_nombre
                FROM ventas_detalles vd
                LEFT JOIN productos p ON vd.tipo_item = 'producto' AND vd.item_id = p.id
                LEFT JOIN servicios s ON vd.tipo_item = 'servicio' AND vd.item_id = s.id
                WHERE vd.venta_id = :venta_id
            ");
            $stmtDet->execute(['venta_id' => $id]);
            $venta['detalles'] = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

            $stmtPagos = $db->prepare("SELECT * FROM ventas_pagos WHERE venta_id = :venta_id");
            $stmtPagos->execute(['venta_id' => $id]);
            $venta['pagos'] = $stmtPagos->fetchAll(PDO::FETCH_ASSOC);
        }

        return $venta;
    }

    /**
     * Registrar procesando la Venta completa en Caja con Adaptación al Inventario Real
     */
    public static function registrarVenta($data) {
        $db = Database::connect();

        try {
            $db->beginTransaction();

            $clienteId   = filter_var($data['cliente_id'] ?? null, FILTER_VALIDATE_INT);
            $usuarioId   = $_SESSION['user_id'] ?? 1;
            $citaId      = filter_var($data['cita_id'] ?? null, FILTER_VALIDATE_INT) ?: null;
            $tipoVenta   = trim($data['tipo_venta'] ?? 'regular'); // 'regular', 'cashea' o 'credito'
            $porcentajeDescuento = filter_var($data['porcentaje_descuento'] ?? 0, FILTER_VALIDATE_FLOAT) ?: 0;
            $tasaBcv     = filter_var($data['tasa_bcv'] ?? 0, FILTER_VALIDATE_FLOAT);

            $itemsData   = $data['items'] ?? [];
            $pagosData   = $data['pagos'] ?? [];

            if (!$clienteId) throw new Exception("Debe seleccionar un cliente válido.");
            if (!$tasaBcv || $tasaBcv <= 0) throw new Exception("La tasa BCV es requerida para el cálculo contable.");
            if (empty($itemsData) || !is_array($itemsData)) throw new Exception("El carrito de la venta no posee ítems.");
            if (!in_array($tipoVenta, ['regular', 'cashea', 'credito'])) throw new Exception("Modalidad de venta no válida.");

            $stmtCli = $db->prepare("SELECT id, saldo_favor_usd, deuda_actual_usd FROM clientes WHERE id = :id FOR UPDATE");
            $stmtCli->execute(['id' => $clienteId]);
            $cliente = $stmtCli->fetch();
            if (!$cliente) throw new Exception("El cliente seleccionado no existe.");

            $subtotalGeneralUsd = 0.00;
            $detallesProcesados = [];

            foreach ($itemsData as $index => $item) {
                $tipoItem = trim($item['tipo'] ?? '');
                $itemId   = filter_var($item['id'] ?? null, FILTER_VALIDATE_INT);
                $cantidad = filter_var($item['cantidad'] ?? 1, FILTER_VALIDATE_INT);
                $precioUnitarioUsd = filter_var($item['precio_unitario_usd'] ?? 0, FILTER_VALIDATE_FLOAT);

                if (!in_array($tipoItem, ['producto', 'servicio'])) {
                    throw new Exception("Tipo de ítem inválido en la línea " . ($index + 1));
                }
                if (!$itemId || $cantidad <= 0 || $precioUnitarioUsd < 0) {
                    throw new Exception("Datos o montos inválidos en el ítem de la línea " . ($index + 1));
                }

                // Control estricto de Stock consultando precio_venta_usd (campo real de la tabla productos)[cite: 10]
                if ($tipoItem === 'producto') {
                    $stmtProd = $db->prepare("SELECT id, nombre, stock, precio_venta_usd FROM productos WHERE id = :id FOR UPDATE");
                    $stmtProd->execute(['id' => $itemId]);
                    $producto = $stmtProd->fetch();

                    if (!$producto) {
                        throw new Exception("El producto especificado en el ítem #" . ($index + 1) . " no existe.");
                    }
                    if (intval($producto['stock']) < $cantidad) {
                        throw new Exception("Stock insuficiente para '{$producto['nombre']}'. Disponible: {$producto['stock']}, Solicitado: {$cantidad}.");
                    }

                    $stmtStock = $db->prepare("UPDATE productos SET stock = stock - :cantidad WHERE id = :id");
                    $stmtStock->execute(['cantidad' => $cantidad, 'id' => $itemId]);
                }

                $subtotalLineaUsd = round($cantidad * $precioUnitarioUsd, 2);
                $subtotalGeneralUsd += $subtotalLineaUsd;

                $detallesProcesados[] = [
                    'tipo_item'           => $tipoItem,
                    'item_id'             => $itemId,
                    'cantidad'            => $cantidad,
                    'precio_unitario_usd' => $precioUnitarioUsd,
                    'subtotal_usd'        => $subtotalLineaUsd
                ];
            }

            if ($porcentajeDescuento < 0 || $porcentajeDescuento > 100) {
                throw new Exception("El porcentaje de descuento ingresado está fuera de rango.");
            }
            $montoDescuentoUsd = round(($subtotalGeneralUsd * $porcentajeDescuento) / 100, 2);
            $totalGeneralUsd   = round($subtotalGeneralUsd - $montoDescuentoUsd, 2);

            if ($totalGeneralUsd < 0) $totalGeneralUsd = 0.00;

            $inicialCasheaUsd    = 0.00;
            $financiadoCasheaUsd = 0.00;
            $abonadoCreditoUsd   = 0.00;
            $pendienteCreditoUsd = 0.00;
            $montoACobrarEnCajaUsd = 0.00;

            if ($tipoVenta === 'cashea') {
                $inicialCasheaUsd = filter_var($data['monto_inicial_cashea_usd'] ?? 0, FILTER_VALIDATE_FLOAT);
                if ($inicialCasheaUsd <= 0 || $inicialCasheaUsd >= $totalGeneralUsd) {
                    throw new Exception("Para la modalidad Cashea, el monto inicial debe ser mayor a 0 y menor al total de la venta.");
                }
                $financiadoCasheaUsd = round($totalGeneralUsd - $inicialCasheaUsd, 2);
                $montoACobrarEnCajaUsd = $inicialCasheaUsd;

            } elseif ($tipoVenta === 'credito') {
                $abonadoCreditoUsd = filter_var($data['monto_abonado_credito_usd'] ?? 0, FILTER_VALIDATE_FLOAT);
                if ($abonadoCreditoUsd < 0 || $abonadoCreditoUsd >= $totalGeneralUsd) {
                    throw new Exception("El monto abonado para el crédito debe ser menor al total de la venta.");
                }
                $pendienteCreditoUsd = round($totalGeneralUsd - $abonadoCreditoUsd, 2);
                $montoACobrarEnCajaUsd = $abonadoCreditoUsd;

            } else {
                $montoACobrarEnCajaUsd = $totalGeneralUsd;
            }

            $totalPagadoEnCajaUsd = 0.00;
            $pagosProcesados = [];

            if ($montoACobrarEnCajaUsd > 0) {
                if (empty($pagosData) || !is_array($pagosData)) {
                    throw new Exception("Debe registrar los métodos de pago correspondientes para cubrir el monto en caja.");
                }

                foreach ($pagosData as $pago) {
                    $metodo = trim($pago['metodo'] ?? '');
                    $montoIngresado = filter_var($pago['monto'] ?? 0, FILTER_VALIDATE_FLOAT);
                    $referencia = trim($pago['referencia'] ?? '');

                    if ($montoIngresado <= 0) {
                        throw new Exception("El monto ingresado en uno de los métodos de pago es inválido.");
                    }

                    $metodosValidos = ['efectivo_usd', 'efectivo_bs', 'pago_movil', 'punto_venta', 'zelle', 'usdt'];
                    if (!in_array($metodo, $metodosValidos)) {
                        throw new Exception("Método de pago no reconocido: " . htmlspecialchars($metodo));
                    }

                    $metodosEnBs = ['efectivo_bs', 'pago_movil', 'punto_venta'];
                    if (in_array($metodo, $metodosEnBs)) {
                        $montoEquivalenteUsd = round($montoIngresado / $tasaBcv, 2);
                    } else {
                        $montoEquivalenteUsd = round($montoIngresado, 2);
                    }

                    $totalPagadoEnCajaUsd += $montoEquivalenteUsd;

                    $pagosProcesados[] = [
                        'metodo_pago'           => $metodo,
                        'monto_ingresado'       => $montoIngresado,
                        'monto_equivalente_usd' => $montoEquivalenteUsd,
                        'tasa_bcv'              => $tasaBcv,
                        'referencia'            => $referencia
                    ];
                }

                if (round($totalPagadoEnCajaUsd, 2) < round($montoACobrarEnCajaUsd, 2)) {
                    $faltante = round($montoACobrarEnCajaUsd - $totalPagadoEnCajaUsd, 2);
                    throw new Exception("El monto ingresado en los pagos ($" . number_format($totalPagadoEnCajaUsd, 2) . ") no cubre el total requerido en caja ($" . number_format($montoACobrarEnCajaUsd, 2) . "). Faltan $" . number_format($faltante, 2));
                }

                $excedenteUsd = round($totalPagadoEnCajaUsd - $montoACobrarEnCajaUsd, 2);
                if ($excedenteUsd > 0) {
                    $stmtSaldoFav = $db->prepare("UPDATE clientes SET saldo_favor_usd = saldo_favor_usd + :exceso WHERE id = :id");
                    $stmtSaldoFav->execute(['exceso' => $excedenteUsd, 'id' => $clienteId]);
                }
            }

            $stmtVenta = $db->prepare("
                INSERT INTO ventas (
                    cliente_id, usuario_id, cita_id, subtotal_usd, descuento_usd,
                    total_general_usd, tipo_venta, monto_inicial_cashea_usd,
                    monto_financiado_cashea_usd, monto_abonado_credito_usd, 
                    monto_pendiente_credito_usd, tasa_bcv
                ) VALUES (
                    :cliente_id, :usuario_id, :cita_id, :subtotal_usd, :descuento_usd,
                    :total_general_usd, :tipo_venta, :inicial_cashea,
                    :financiado_cashea, :abonado_credito, :pendiente_credito, :tasa_bcv
                )
            ");
            $stmtVenta->execute([
                'cliente_id'        => $clienteId,
                'usuario_id'        => $usuarioId,
                'cita_id'           => $citaId,
                'subtotal_usd'      => $subtotalGeneralUsd,
                'descuento_usd'     => $montoDescuentoUsd,
                'total_general_usd' => $totalGeneralUsd,
                'tipo_venta'        => $tipoVenta,
                'inicial_cashea'    => $inicialCasheaUsd,
                'financiado_cashea' => $financiadoCasheaUsd,
                'abonado_credito'   => $abonadoCreditoUsd,
                'pendiente_credito' => $pendienteCreditoUsd,
                'tasa_bcv'          => $tasaBcv
            ]);
            $ventaId = $db->lastInsertId();

            // Si es crédito y quedó saldo pendiente, incrementar la deuda actual del cliente
            if ($tipoVenta === 'credito' && $pendienteCreditoUsd > 0) {
                $stmtDeuda = $db->prepare("UPDATE clientes SET deuda_actual_usd = deuda_actual_usd + :pendiente WHERE id = :id");
                $stmtDeuda->execute(['pendiente' => $pendienteCreditoUsd, 'id' => $clienteId]);
            }

            $stmtDet = $db->prepare("
                INSERT INTO ventas_detalles (venta_id, tipo_item, item_id, cantidad, precio_unitario_usd, subtotal_usd)
                VALUES (:venta_id, :tipo_item, :item_id, :cantidad, :precio_unitario, :subtotal)
            ");
            foreach ($detallesProcesados as $det) {
                $stmtDet->execute([
                    'venta_id'        => $ventaId,
                    'tipo_item'       => $det['tipo_item'],
                    'item_id'         => $det['item_id'],
                    'cantidad'        => $det['cantidad'],
                    'precio_unitario' => $det['precio_unitario_usd'],
                    'subtotal'        => $det['subtotal_usd']
                ]);
            }

            if (!empty($pagosProcesados)) {
                $stmtPago = $db->prepare("
                    INSERT INTO ventas_pagos (venta_id, metodo_pago, monto_ingresado, monto_equivalente_usd, tasa_bcv, referencia)
                    VALUES (:venta_id, :metodo_pago, :monto_ingresado, :monto_equivalente_usd, :tasa_bcv, :referencia)
                ");
                foreach ($pagosProcesados as $pago) {
                    $stmtPago->execute([
                        'venta_id'              => $ventaId,
                        'metodo_pago'           => $pago['metodo_pago'],
                        'monto_ingresado'       => $pago['monto_ingresado'],
                        'monto_equivalente_usd' => $pago['monto_equivalente_usd'],
                        'tasa_bcv'              => $pago['tasa_bcv'],
                        'referencia'            => $pago['referencia']
                    ]);
                }
            }

            if ($citaId) {
                $stmtCita = $db->prepare("UPDATE citas SET estado = 'realizada' WHERE id = :cita_id");
                $stmtCita->execute(['cita_id' => $citaId]);
            }

            $db->commit();
            return $ventaId;

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Anular una venta dentro del límite de 24 horas, revirtiendo stock, deudas y citas.
     */
    public static function anularVenta($ventaId, $motivo, $usuarioId) {
        $db = Database::connect();

        try {
            $db->beginTransaction();

            $ventaId = filter_var($ventaId, FILTER_VALIDATE_INT);
            if (!$ventaId) {
                throw new Exception("ID de venta inválido.");
            }

            // 1. Obtener y bloquear la venta
            $stmt = $db->prepare("SELECT * FROM ventas WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $ventaId]);
            $venta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$venta) {
                throw new Exception("La venta especificada no existe.");
            }

            if ($venta['estado'] === 'anulada') {
                throw new Exception("Esta venta ya se encuentra anulada.");
            }

            // 2. Validar límite de tiempo de 24 horas
            $fechaVenta = strtotime($venta['created_at']);
            $ahora = time();
            $diferenciaHoras = ($ahora - $fechaVenta) / 3600;

            if ($diferenciaHoras > 24) {
                throw new Exception("No se puede anular la venta. Ha superado el límite de seguridad de 24 horas.");
            }

            // 3. Revertir el Inventario (Stock de Productos)
            $stmtDet = $db->prepare("SELECT * FROM ventas_detalles WHERE venta_id = :venta_id");
            $stmtDet->execute(['venta_id' => $ventaId]);
            $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

            foreach ($detalles as $det) {
                if ($det['tipo_item'] === 'producto') {
                    $stmtStock = $db->prepare("UPDATE productos SET stock = stock + :cantidad WHERE id = :producto_id");
                    $stmtStock->execute([
                        'cantidad'   => $det['cantidad'],
                        'producto_id' => $det['item_id']
                    ]);
                }
            }

            // 4. Revertir Deuda si fue por Crédito Directo
            if ($venta['tipo_venta'] === 'credito') {
                $pendienteCredito = floatval($venta['monto_pendiente_credito_usd']);
                if ($pendienteCredito > 0) {
                    $stmtDeuda = $db->prepare("UPDATE clientes SET deuda_actual_usd = GREATEST(0, deuda_actual_usd - :pendiente) WHERE id = :cliente_id");
                    $stmtDeuda->execute([
                        'pendiente'  => $pendienteCredito,
                        'cliente_id' => $venta['cliente_id']
                    ]);
                }
            }

            // 5. Revertir Estado de la Cita asociada (si aplica)
            if (!empty($venta['cita_id'])) {
                $stmtCita = $db->prepare("UPDATE citas SET estado = 'pendiente' WHERE id = :cita_id");
                $stmtCita->execute(['cita_id' => $venta['cita_id']]);
            }

            // 6. Marcar la venta como anulada en la base de datos
            $stmtAnular = $db->prepare("
                UPDATE ventas 
                SET estado = 'anulada', 
                    anulado_por = :usuario_id, 
                    motivo_anulacion = :motivo,
                    monto_pendiente_credito_usd = 0.00
                WHERE id = :id
            ");
            $stmtAnular->execute([
                'usuario_id' => $usuarioId,
                'motivo'     => trim($motivo),
                'id'         => $ventaId
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
}