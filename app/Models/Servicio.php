<?php
// app/Models/Servicio.php

require_once BASE_PATH . 'app/Config/Database.php';

class Servicio {

    public static function obtenerTodos() {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT * FROM servicios 
            ORDER BY nombre ASC
        ");
        return $stmt->fetchAll();
    }

    public static function crear($data) {
        $db = Database::connect();
        
        $nombre = trim($data['nombre'] ?? '');
        $precioUsd = filter_var($data['precio_usd'], FILTER_VALIDATE_FLOAT);

        if (empty($nombre)) {
            throw new Exception("El nombre del servicio es obligatorio.");
        }
        if ($precioUsd === false || $precioUsd < 0) {
            throw new Exception("El precio en USD debe ser un número válido mayor o igual a cero.");
        }

        // Nota: Ajustado a los campos exactos de la tabla `servicios` en la BD
        $stmt = $db->prepare("
            INSERT INTO servicios (nombre, descripcion, precio_usd)
            VALUES (:nombre, :descripcion, :precio_usd)
        ");

        return $stmt->execute([
            'nombre'      => $nombre,
            'descripcion' => trim($data['descripcion'] ?? ''),
            'precio_usd'  => $precioUsd
        ]);
    }

    public static function actualizar($id, $data) {
        $db = Database::connect();
        
        $nombre = trim($data['nombre'] ?? '');
        $precioUsd = filter_var($data['precio_usd'], FILTER_VALIDATE_FLOAT);

        if (empty($nombre)) {
            throw new Exception("El nombre del servicio es obligatorio.");
        }
        if ($precioUsd === false || $precioUsd < 0) {
            throw new Exception("El precio en USD no es válido.");
        }

        $stmt = $db->prepare("
            UPDATE servicios 
            SET nombre = :nombre, descripcion = :descripcion, precio_usd = :precio_usd
            WHERE id = :id
        ");

        return $stmt->execute([
            'id'          => $id,
            'nombre'      => $nombre,
            'descripcion' => trim($data['descripcion'] ?? ''),
            'precio_usd'  => $precioUsd
        ]);
    }

    public static function eliminar($id) {
        $db = Database::connect();
        
        // Verificar si está asociado a alguna cita antes de eliminar
        $stmtCheck = $db->prepare("SELECT COUNT(*) FROM cita_servicios WHERE servicio_id = :id");
        $stmtCheck->execute(['id' => $id]);
        if ($stmtCheck->fetchColumn() > 0) {
            throw new Exception("No se puede eliminar el servicio porque está asociado a citas registradas.");
        }

        $stmt = $db->prepare("DELETE FROM servicios WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}