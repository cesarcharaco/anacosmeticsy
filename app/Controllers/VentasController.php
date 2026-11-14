<?php
// app/Controllers/VentasController.php

require_once BASE_PATH . 'app/Models/Venta.php';
require_once BASE_PATH . 'app/Models/Cliente.php';
require_once BASE_PATH . 'app/Models/Producto.php';
require_once BASE_PATH . 'app/Models/Servicio.php';
require_once BASE_PATH . 'app/Models/Cita.php';
require_once BASE_PATH . 'app/Services/BcvRateService.php';

class VentasController {

    private $bcvService;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth');
            exit;
        }
        $this->bcvService = new BcvRateService();
    }

    /**
     * Vista principal del POS / Caja
     */
    public function index() {
        $tasaUsd = $this->bcvService->getCurrentRate('USD');
        
        // Cargar listas iniciales útiles para el POS
        $clientes = Cliente::obtenerTodos(100, 0);
        $productos = Producto::obtenerTodos();
        $servicios = Servicio::obtenerTodos();
        
        // Obtener citas pendientes del día para carga rápida en caja
        $citasPendientes = Cita::obtenerPendientesParaCaja() ?? [];

        require_once BASE_PATH . 'views/ventas/index.php';
    }

    /**
     * Procesar y registrar la venta desde el POS (Soporta AJAX o POST tradicional)
     */
    public function procesarVenta() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            try {
                $tasaUsd = $this->bcvService->getCurrentRate('USD');

                // Recibir y decodificar datos JSON o POST estándar
                $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
                if (stripos($contentType, 'application/json') !== false) {
                    $inputJSON = file_get_contents('php://input');
                    $data = json_decode($inputJSON, true) ?? [];
                } else {
                    $data = $_POST;
                }

                // Inyectar la tasa BCV oficial actual al arreglo de datos
                $data['tasa_bcv'] = $tasaUsd;

                // Registrar venta a través del modelo robusto
                $ventaId = Venta::registrarVenta($data);

                echo json_encode([
                    'success' => true,
                    'message' => '¡Venta procesada con éxito!',
                    'venta_id' => $ventaId,
                    'redirect_url' => "' . BASE_URL . '/ventas/notaEntrega/$ventaId"
                ]);

            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;
        }
    }

    /**
     * Vista o generación de la Nota de Entrega / Comprobante en PDF
     */
    public function notaEntrega($id) {
        $ventaId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$ventaId) {
            $_SESSION['error'] = "ID de venta inválido.";
            header('Location: ' . BASE_URL . '/ventas');
            exit;
        }

        $venta = Venta::obtenerPorId($ventaId);
        if (!$venta) {
            $_SESSION['error'] = "La venta especificada no existe.";
            header('Location: ' . BASE_URL . '/ventas');
            exit;
        }

        $tasaUsd = $venta['tasa_bcv'];

        // Cargar vista de la nota de entrega (adaptada para impresión o PDF)
        require_once BASE_PATH . 'views/ventas/nota_entrega.php';
    }

    /**
     * API asíncrona para buscar productos en tiempo real desde el POS
     */
    public function buscarProductos() {
        header('Content-Type: application/json');
        try {
            $query = trim($_GET['q'] ?? '');
            $productos = Producto::buscarPorNombreObarras($query);
            
            echo json_encode([
                'success' => true,
                'productos' => $productos
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Vista del Historial de Ventas con filtros y DataTables
     */
    /**
     * Vista del Historial de Ventas con filtros y DataTables
     */
    public function historial() {
        $tasaUsd = $this->bcvService->getCurrentRate('USD');
        
        // Obtener parámetros de filtro opcionales
        $filtroFechaInicio = $_GET['fecha_inicio'] ?? '';
        $filtroFechaFin    = $_GET['fecha_fin'] ?? '';
        $filtroCliente     = $_GET['cliente_id'] ?? '';
        $filtroServicio    = $_GET['servicio_id'] ?? '';

        // Obtener listas y ventas filtradas
        $ventas = Venta::obtenerFiltradas($filtroFechaInicio, $filtroFechaFin, $filtroCliente, $filtroServicio);
        $clientes = Cliente::obtenerTodos(100, 0);
        $servicios = Servicio::obtenerTodos();

        require_once BASE_PATH . 'views/ventas/historial.php';
    }

    /**
     * Endpoint AJAX para consultar el detalle completo de una venta en la modal
     */
    public function obtenerDetalleAjax($id) {
        header('Content-Type: application/json');
        try {
            $ventaId = filter_var($id, FILTER_VALIDATE_INT);
            if (!$ventaId) throw new Exception("ID inválido.");

            $venta = Venta::obtenerPorId($ventaId);
            if (!$venta) throw new Exception("La venta no existe.");

            echo json_encode([
                'success' => true,
                'venta'   => $venta
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
     * Procesar la anulación de una venta (límite 24 horas)
     */
    public function anular() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $ventaId = filter_var($_POST['venta_id'] ?? null, FILTER_VALIDATE_INT);
                $motivo  = trim($_POST['motivo_anulacion'] ?? '');
                $usuarioId = $_SESSION['user_id'] ?? 1;

                if (!$ventaId) {
                    throw new Exception("ID de venta inválido.");
                }

                if (empty($motivo)) {
                    throw new Exception("Debe especificar un motivo para la anulación.");
                }

                Venta::anularVenta($ventaId, $motivo, $usuarioId);
                $_SESSION['mensaje'] = "¡La venta #" . str_pad($ventaId, 6, '0', STR_PAD_LEFT) . " ha sido anulada exitosamente y sus saldos/stock han sido revertidos!";

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            header('Location: ' . BASE_URL . '/ventas/historial');
            exit;
        }
    }
}