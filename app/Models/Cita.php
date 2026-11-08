<?php
// app/Models/Cita.php

require_once BASE_PATH . 'app/Config/Database.php';

class Cita {

    public static function obtenerTodas() {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT c.*, cl.nombre AS cliente_nombre, cl.telefono AS cliente_telefono,
                   u.nombre AS usuario_nombre
            FROM citas c
            LEFT JOIN clientes cl ON c.cliente_id = cl.id
            LEFT JOIN usuarios u ON c.usuario_id = u.id
            ORDER BY c.fecha_cita DESC
        ");
        return $stmt->fetchAll();
    }

    public static function obtenerPorFecha($fecha) {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT c.*, cl.nombre AS cliente_nombre, cl.telefono AS cliente_telefono,
                   u.nombre AS usuario_nombre
            FROM citas c
            LEFT JOIN clientes cl ON c.cliente_id = cl.id
            LEFT JOIN usuarios u ON c.usuario_id = u.id
            WHERE DATE(c.fecha_cita) = :fecha_cita
            ORDER BY c.fecha_cita ASC
        ");
        $stmt->execute(['fecha_cita' => $fecha]);
        return $stmt->fetchAll();
    }

    public static function crearConServicios($data, $serviciosIds) {
        $db = Database::connect();
        
        try {
            // Iniciamos transacción para asegurar consistencia atómica
            $db->beginTransaction();

            // 1. Validar que el cliente exista
            $stmtCli = $db->prepare("SELECT id FROM clientes WHERE id = :id");
            $stmtCli->execute(['id' => $data['cliente_id']]);
            if (!$stmtCli->fetch()) {
                throw new Exception("El cliente seleccionado no es válido o no existe en el sistema.");
            }

            // 2. Validar fecha posterior al momento actual
            $fechaCita = $data['fecha_cita'];
            if (strtotime($fechaCita) < time()) {
                throw new Exception("No se pueden programar citas en fechas u horas pasadas.");
            }

            // 3. Calcular el total real consultando los servicios desde la BD (evita manipulación de precios en frontend)
            $totalRealUsd = 0;
            $serviciosValidos = [];

            if (!empty($serviciosIds) && is_array($serviciosIds)) {
                $placeholders = implode(',', array_fill(0, count($serviciosIds), '?'));
                $stmtServ = $db->prepare("SELECT id, precio_usd FROM servicios WHERE id IN ($placeholders)");
                $stmtServ->execute($serviciosIds);
                $serviciosEnBD = $stmtServ->fetchAll(PDO::FETCH_ASSOC);

                foreach ($serviciosEnBD as $s) {
                    $totalRealUsd += floatval($s['precio_usd']);
                    $serviciosValidos[] = $s['id'];
                }
            }

            if (empty($serviciosValidos)) {
                throw new Exception("Debe seleccionar al menos un servicio válido para la cita.");
            }

            // 4. Insertar la Cita Principal
            $stmtCita = $db->prepare("
                INSERT INTO citas (cliente_id, usuario_id, fecha_cita, estado, total_estimado_usd, observaciones)
                VALUES (:cliente_id, :usuario_id, :fecha_cita, 'pendiente', :total_estimado_usd, :observaciones)
            ");

            $stmtCita->execute([
                'cliente_id'         => $data['cliente_id'],
                'usuario_id'         => $data['usuario_id'] ?? ($_SESSION['user_id'] ?? 1),
                'fecha_cita'         => $fechaCita,
                'total_estimado_usd' => round($totalRealUsd, 2),
                'observaciones'      => trim($data['observaciones'] ?? '')
            ]);

            $citaId = $db->lastInsertId();

            // 5. Insertar la relación en la tabla pivote (cita_servicios)
            $stmtPivote = $db->prepare("
                INSERT INTO cita_servicios (cita_id, servicio_id) 
                VALUES (:cita_id, :servicio_id)
            ");

            foreach ($serviciosValidos as $sId) {
                $stmtPivote->execute([
                    'cita_id'    => $citaId,
                    'servicio_id'=> $sId
                ]);
            }

            // Confirmar transacción
            $db->commit();
            return $citaId;

        } catch (Exception $e) {
            // Revertir cambios si algo falla
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
}