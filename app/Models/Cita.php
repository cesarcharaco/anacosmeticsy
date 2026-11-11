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

    public static function obtenerServiciosDeCita($citaId) {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT s.nombre, s.precio_usd 
            FROM cita_servicios cs
            INNER JOIN servicios s ON cs.servicio_id = s.id
            WHERE cs.cita_id = :cita_id
        ");
        $stmt->execute(['cita_id' => $citaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function cancelar($id) {
        $db = Database::connect();
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) throw new Exception("ID de cita no válido.");

        $stmt = $db->prepare("UPDATE citas SET estado = 'cancelada' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function reagendar($id, $nuevaFecha) {
        $db = Database::connect();
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) throw new Exception("ID de cita no válido.");

        if (empty($nuevaFecha) || strtotime($nuevaFecha) < time()) {
            throw new Exception("La nueva fecha y hora de la cita no pueden ser en el pasado.");
        }

        $stmt = $db->prepare("UPDATE citas SET fecha_cita = :fecha_cita, estado = 'pendiente' WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'fecha_cita' => $nuevaFecha
        ]);
    }

    public static function crearConServicios($data, $serviciosIds) {
        $db = Database::connect();
        
        try {
            $db->beginTransaction();

            $stmtCli = $db->prepare("SELECT id FROM clientes WHERE id = :id");
            $stmtCli->execute(['id' => $data['cliente_id']]);
            if (!$stmtCli->fetch()) {
                throw new Exception("El cliente seleccionado no es válido o no existe en el sistema.");
            }

            $fechaCita = $data['fecha_cita'];
            if (strtotime($fechaCita) < time()) {
                throw new Exception("No se pueden programar citas en fechas u horas pasadas.");
            }

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

            $db->commit();
            return $citaId;

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public static function obtenerPendientesParaCaja() {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT c.*, 
                   DATE_FORMAT(c.fecha_cita, '%H:%i') AS hora_cita,
                   cl.nombre AS cliente_nombre, cl.telefono AS cliente_telefono,
                   s.id AS servicio_id, s.nombre AS servicio_nombre, 
                   IF(cs.precio_unitario_usd > 0, cs.precio_unitario_usd, s.precio_usd) AS precio_usd
            FROM citas c
            INNER JOIN clientes cl ON c.cliente_id = cl.id
            INNER JOIN cita_servicios cs ON c.id = cs.cita_id
            INNER JOIN servicios s ON cs.servicio_id = s.id
            WHERE c.estado = 'pendiente' AND DATE(c.fecha_cita) = CURDATE()
            ORDER BY c.fecha_cita ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}