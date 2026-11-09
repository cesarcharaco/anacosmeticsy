<?php
// app/Models/Merma.php

require_once BASE_PATH . 'app/Config/Database.php';

class Merma {

    /**
     * Obtener el historial completo de mermas y salidas extraordinarias con sus detalles.
     */
    public static function obtenerTodas() {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT m.*, u.nombre AS usuario_nombre,
                   COUNT(md.id) AS total_items,
                   COALESCE(SUM(md.subtotal_perdida_usd), 0.00) AS total_perdida_usd
            FROM mermas_salidas m
            INNER JOIN usuarios u ON m.usuario_id = u.id
            LEFT JOIN merma_detalles md ON m.id = md.merma_id
            GROUP BY m.id
            ORDER BY m.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener los detalles de los productos incluidos en una salida extraordinaria específica.
     */
    public static function obtenerDetalles($mermaId) {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT md.*, p.nombre AS producto_nombre, p.codigo AS producto_codigo
            FROM merma_detalles md
            INNER JOIN productos p ON md.producto_id = p.id
            WHERE md.merma_id = :merma_id
        ");
        $stmt->execute(['merma_id' => $mermaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Registrar una salida extraordinaria por incidencia/merma/donación en una sola transacción PDO.
     */
    public static function registrar($data) {
        $db = Database::connect();

        try {
            $db->beginTransaction();

            $usuarioId     = $_SESSION['user_id'] ?? 1;
            $motivo        = trim($data['motivo'] ?? '');
            $observaciones = trim($data['observaciones'] ?? '');
            $items         = $data['items'] ?? [];

            $motivosValidos = ['dañado', 'vencido', 'defectuoso_fabrica', 'extravio', 'donacion_regalo', 'consumo_interno'];
            if (!in_array($motivo, $motivosValidos)) {
                throw new Exception("El motivo de la salida no es válido.");
            }

            if (empty($items) || !is_array($items)) {
                throw new Exception("Debe agregar al menos un producto a la lista de salida.");
            }

            // 1. Insertar Cabecera de la Merma
            $stmtCab = $db->prepare("
                INSERT INTO mermas_salidas (usuario_id, motivo, observaciones)
                VALUES (:usuario_id, :motivo, :observaciones)
            ");
            $stmtCab->execute([
                'usuario_id'    => $usuarioId,
                'motivo'        => $motivo,
                'observaciones' => $observaciones
            ]);
            $mermaId = $db->lastInsertId();

            // 2. Preparar sentencias de consulta y actualización de inventario
            $stmtProd = $db->prepare("SELECT id, nombre, stock, costo_usd FROM productos WHERE id = :id FOR UPDATE");
            $stmtDet = $db->prepare("
                INSERT INTO merma_detalles (merma_id, producto_id, cantidad, costo_unitario_usd, subtotal_perdida_usd)
                VALUES (:merma_id, :producto_id, :cantidad, :costo_unitario_usd, :subtotal_perdida_usd)
            ");
            $stmtUpdateStock = $db->prepare("UPDATE productos SET stock = stock - :cantidad WHERE id = :id");

            // 3. Procesar cada ítem retirado
            foreach ($items as $item) {
                $productoId = filter_var($item['producto_id'] ?? null, FILTER_VALIDATE_INT);
                $cantidad   = filter_var($item['cantidad'] ?? 0, FILTER_VALIDATE_INT);

                if (!$productoId || $cantidad <= 0) {
                    throw new Exception("Uno de los productos seleccionados posee una cantidad o ID inválido.");
                }

                // Bloquear producto para validar stock disponible en tiempo real
                $stmtProd->execute(['id' => $productoId]);
                $producto = $stmtProd->fetch(PDO::FETCH_ASSOC);

                if (!$producto) {
                    throw new Exception("El producto seleccionado (ID: $productoId) no existe.");
                }

                if ($producto['stock'] < $cantidad) {
                    throw new Exception("Stock insuficiente para '{$producto['nombre']}'. Disponible: {$producto['stock']}, Solicitado: {$cantidad}.");
                }

                $costoUnitario = floatval($producto['costo_usd']);
                $subtotalPerdida = round($costoUnitario * $cantidad, 2);

                // Insertar detalle
                $stmtDet->execute([
                    'merma_id'           => $mermaId,
                    'producto_id'        => $productoId,
                    'cantidad'           => $cantidad,
                    'costo_unitario_usd' => $costoUnitario,
                    'subtotal_perdida_usd' => $subtotalPerdida
                ]);

                // Descontar del inventario
                $stmtUpdateStock->execute([
                    'cantidad' => $cantidad,
                    'id'       => $productoId
                ]);
            }

            $db->commit();
            return $mermaId;

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Anular una salida extraordinaria, revirtiendo el stock al inventario de forma atómica.
     */
    public static function anular($mermaId, $motivoAnulacion, $usuarioId) {
        $db = Database::connect();

        try {
            $db->beginTransaction();

            // 1. Obtener y bloquear la cabecera de la merma
            $stmtCab = $db->prepare("SELECT * FROM mermas_salidas WHERE id = :id FOR UPDATE");
            $stmtCab->execute(['id' => $mermaId]);
            $merma = $stmtCab->fetch(PDO::FETCH_ASSOC);

            if (!$merma) {
                throw new Exception("El registro de salida especificado no existe.");
            }

            if ($merma['estado'] === 'anulada') {
                throw new Exception("Esta salida extraordinaria ya se encontraba anulada.");
            }

            // 2. Obtener los detalles de los productos afectados
            $stmtDet = $db->prepare("SELECT * FROM merma_detalles WHERE merma_id = :merma_id");
            $stmtDet->execute(['merma_id' => $mermaId]);
            $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

            // 3. Revertir el stock en la tabla productos
            $stmtRevertir = $db->prepare("UPDATE productos SET stock = stock + :cantidad WHERE id = :producto_id");
            foreach ($detalles as $det) {
                $stmtRevertir->execute([
                    'cantidad'    => $det['cantidad'],
                    'producto_id' => $det['producto_id']
                ]);
            }

            // 4. Actualizar el estatus de la merma a anulada
            $stmtUpdate = $db->prepare("
                UPDATE mermas_salidas 
                SET estado = 'anulada', anulado_por = :usuario_id, motivo_anulacion = :motivo 
                WHERE id = :id
            ");
            $stmtUpdate->execute([
                'usuario_id' => $usuarioId,
                'motivo'     => trim($motivoAnulacion),
                'id'         => $mermaId
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