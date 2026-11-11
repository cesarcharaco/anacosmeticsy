<?php
// app/Controllers/ClientesController.php

require_once BASE_PATH . 'app/Models/Cliente.php';
require_once BASE_PATH . 'app/Services/BcvRateService.php';

class ClientesController {

    private $bcvService;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /anacosmeticsy/public/auth');
            exit;
        }
        $this->bcvService = new BcvRateService();
    }

    public function index() {
        $tasaUsd = $this->bcvService->getCurrentRate('USD');
        
        // Paginación
        $porPagina = 10;
        $paginaActual = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
        $offset = ($paginaActual - 1) * $porPagina;

        $totalClientes = Cliente::contarTotal();
        $totalPaginas = ceil($totalClientes / $porPagina);

        $clientes = Cliente::obtenerTodos($porPagina, $offset);
        
        require_once BASE_PATH . 'views/clientes/index.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = trim($_POST['nombre'] ?? '');
                if (empty($nombre)) {
                    throw new Exception("El nombre del cliente es obligatorio.");
                }

                $data = [
                    'nombre'          => $nombre,
                    'telefono'        => trim($_POST['telefono'] ?? ''),
                    'direccion'       => trim($_POST['direccion'] ?? ''),
                    'saldo_favor_usd' => $_POST['saldo_favor_usd'] ?? 0.00
                ];

                Cliente::crear($data);
                $_SESSION['mensaje'] = "Cliente registrado exitosamente en el directorio.";
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            header('Location: /anacosmeticsy/public/clientes');
            exit;
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'] ?? null;
                $nombre = trim($_POST['nombre'] ?? '');

                if (!$id) throw new Exception("ID de cliente inválido.");
                if (empty($nombre)) throw new Exception("El nombre del cliente es obligatorio.");

                $data = [
                    'nombre'          => $nombre,
                    'telefono'        => trim($_POST['telefono'] ?? ''),
                    'direccion'       => trim($_POST['direccion'] ?? ''),
                    'saldo_favor_usd' => $_POST['saldo_favor_usd'] ?? 0.00
                ];

                Cliente::actualizar($id, $data);
                $_SESSION['mensaje'] = "Datos del cliente actualizados correctamente.";
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            header('Location: /anacosmeticsy/public/clientes');
            exit;
        }
    }

    public function eliminar($id) {
        try {
            if ($id) {
                Cliente::eliminar($id);
                $_SESSION['mensaje'] = "Cliente eliminado del sistema.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /anacosmeticsy/public/clientes');
        exit;
    }

    public function guardarAjax() {
        header('Content-Type: application/json');
        
        // Verificar si la petición es por POST y JSON
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!empty($input)) {
            $_POST = $input;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = trim($_POST['nombre'] ?? '');
                if (empty($nombre)) {
                    throw new Exception("El nombre del cliente es obligatorio.");
                }

                $data = [
                    'nombre'          => $nombre,
                    'telefono'        => trim($_POST['telefono'] ?? ''),
                    'direccion'       => trim($_POST['direccion'] ?? ''),
                    'saldo_favor_usd' => $_POST['saldo_favor_usd'] ?? 0.00
                ];

                $nuevoId = Cliente::crear($data);

                if ($nuevoId) {
                    echo json_encode([
                        'success'    => true,
                        'cliente_id' => $nuevoId,
                        'message'    => 'Cliente registrado exitosamente.'
                    ]);
                    exit;
                } else {
                    throw new Exception("No se pudo registrar el cliente en la base de datos.");
                }

            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            }
        }

        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido.'
        ]);
        exit;
    }
}