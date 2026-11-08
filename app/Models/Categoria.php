<?php
// app/Models/Categoria.php

require_once BASE_PATH . 'app/Config/Database.php';

class Categoria {

    public static function obtenerTodas() {
        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM categorias ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    public static function crear($nombre, $descripcion = null) {
        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (:nombre, :descripcion)");
        return $stmt->execute(['nombre' => $nombre, 'descripcion' => $descripcion]);
    }

    public static function actualizar($id, $nombre, $descripcion = null) {
        $db = Database::connect();
        $stmt = $db->prepare("UPDATE categorias SET nombre = :nombre, descripcion = :descripcion WHERE id = :id");
        return $stmt->execute(['id' => $id, 'nombre' => $nombre, 'descripcion' => $descripcion]);
    }

    public static function eliminar($id) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM categorias WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}