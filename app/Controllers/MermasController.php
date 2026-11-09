<?php
// app/Controllers/MermasController.php

require_once BASE_PATH . 'app/Models/Merma.php';
require_once BASE_PATH . 'app/Models/Producto.php';

class MermasController {

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /anacosmeticsy/public/auth');
            exit;
        }
    }

    /**
     * Cargar la vista principal de incidencias/mermas con su historial y catálogo de productos.
     */
    public function index() {
        $mermas = Merma::obtenerTodas();
        $productos = Producto::obtenerTodos(); // Para poblar el selector de ítems a retirar
        
        require_once BASE_PATH . 'views/mermas/index.php';
    }

    /**
     * Procesar la creación de una nueva salida por incidencia/merma mediante POST AJAX.
     */
    public function registrar() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }

            $mermaId = Merma::registrar($input);

            echo json_encode([
                'success' => true,
                'message' => '¡Salida extraordinaria por incidencia registrada exitosamente!',
                'merma_id' => $mermaId
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Obtener mediante AJAX los detalles de un registro de merma/incidencia específico para el modal.
     */
    public function obtenerDetalleAjax($id) {
        header('Content-Type: application/json');
        try {
            $mermaId = filter_var($id, FILTER_VALIDATE_INT);
            if (!$mermaId) {
                throw new Exception("ID de salida inválido.");
            }

            $detalles = Merma::obtenerDetalles($mermaId);

            echo json_encode([
                'success'  => true,
                'detalles' => $detalles
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Procesar la anulación de una salida extraordinaria / merma
     */
    public function anular() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }

            $mermaId       = filter_var($input['merma_id'] ?? null, FILTER_VALIDATE_INT);
            $motivoAnulacion = trim($input['motivo_anulacion'] ?? '');
            $usuarioId     = $_SESSION['user_id'] ?? 1;

            if (!$mermaId) {
                throw new Exception("ID de salida inválido.");
            }

            if (empty($motivoAnulacion)) {
                throw new Exception("Debe especificar un motivo detallado para la anulación.");
            }

            Merma::anular($mermaId, $motivoAnulacion, $usuarioId);

            echo json_encode([
                'success' => true,
                'message' => '¡Salida extraordinaria anulada exitosamente y stock revertido al inventario!'
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
}