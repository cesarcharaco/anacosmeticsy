<?php
// app/Controllers/CategoriaController.php

require_once BASE_PATH . 'app/Models/Categoria.php';

class CategoriaController {

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /anacosmeticsy/public/auth');
            exit;
        }
    }

    public function index() {
        $categorias = Categoria::obtenerTodas();
        require_once BASE_PATH . 'views/categorias/index.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');

            if (!empty($nombre)) {
                if (!empty($_POST['id'])) {
                    Categoria::actualizar($_POST['id'], $nombre, $descripcion);
                    $_SESSION['mensaje'] = "Categoría actualizada correctamente.";
                } else {
                    Categoria::crear($nombre, $descripcion);
                    $_SESSION['mensaje'] = "Categoría creada con éxito.";
                }
            }

            header('Location: /anacosmeticsy/public/categoria');
            exit;
        }
    }

    public function eliminar($id) {
        if ($id) {
            try {
                Categoria::eliminar($id);
                $_SESSION['mensaje'] = "Categoría eliminada correctamente.";
            } catch (\Exception $e) {
                $_SESSION['mensaje'] = "No se puede eliminar la categoría porque hay productos asociados.";
            }
        }
        header('Location: /anacosmeticsy/public/categoria');
        exit;
    }
}