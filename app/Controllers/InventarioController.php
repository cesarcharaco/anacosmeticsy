<?php
// app/Controllers/InventarioController.php

require_once BASE_PATH . 'app/Models/Producto.php';
require_once BASE_PATH . 'app/Models/Categoria.php';
require_once BASE_PATH . 'app/Services/BcvRateService.php';

class InventarioController {

    private $bcvService;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth');
            exit;
        }
        $this->bcvService = new BcvRateService();
    }

    public function index() {
        $productos = Producto::obtenerTodos();
        $categorias = Categoria::obtenerTodas(); // Carga de categorías activas
        $tasaUsd = $this->bcvService->getCurrentRate('USD');

        require_once BASE_PATH . 'views/inventario/index.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $costo = (float) ($_POST['costo_usd'] ?? 0);
            $margen = (float) ($_POST['margen_ganancia'] ?? 0);
            $precioUsd = $costo + ($costo * ($margen / 100));

            $data = [
                'codigo'          => trim($_POST['codigo'] ?? ''),
                'nombre'          => trim($_POST['nombre'] ?? ''),
                'categoria_id'    => !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null,
                'costo_usd'       => $costo,
                'margen_ganancia' => $margen,
                'precio_usd'      => $precioUsd,
                'stock'           => (int) ($_POST['stock'] ?? 0),
                'stock_minimo'    => (int) ($_POST['stock_minimo'] ?? 5),
                'estado'          => $_POST['estado'] ?? 'activo'
            ];

            try {
                if (!empty($_POST['id'])) {
                    Producto::actualizar($_POST['id'], $data);
                    $_SESSION['mensaje'] = "Producto actualizado correctamente.";
                } else {
                    Producto::crear($data);
                    $_SESSION['mensaje'] = "Producto nuevo registrado exitosamente.";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            // Si el registro vino desde la pantalla de Actualizar Inventario (Recepción)
            if (isset($_POST['origen']) && $_POST['origen'] === 'actualizar') {
                header('Location: ' . BASE_URL . '/inventario/actualizar');
                exit;
            }

            // Flujo normal desde la vista principal de Inventario
            header('Location: ' . BASE_URL . '/inventario');
            exit;
        }
    }

    public function eliminar($id) {
        try {
            $idInt = filter_var($id, FILTER_VALIDATE_INT);
            if ($idInt) {
                Producto::eliminar($idInt);
                $_SESSION['mensaje'] = "Producto eliminado del inventario correctamente.";
            } else {
                throw new Exception("El identificador del producto no es válido.");
            }
        } catch (Exception $e) {
            // Capturamos el mensaje de negocio o error del modelo y lo guardamos en sesión
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ' . BASE_URL . '/inventario');
        exit;
    }

    public function actualizar() {
        // Carga la tasa actual y los productos/categorías si se requieren en la vista de recepción
        $tasaUsd = $this->bcvService->getCurrentRate('USD');
        $productos = Producto::obtenerTodos();
        $categorias = Categoria::obtenerTodas();
        
        require_once BASE_PATH . 'views/inventario/actualizar.php';
    }

    public function procesarActualizacion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $usuarioId     = $_SESSION['user_id'] ?? 1;
                $observaciones = $_POST['observaciones'] ?? '';
                $items         = $_POST['items'] ?? [];

                if (empty($items)) {
                    throw new Exception("No se ha seleccionado ningún producto para procesar la recepción.");
                }

                // Llamar al modelo transaccional
                Producto::registrarRecepcionLote($usuarioId, $observaciones, $items);

                $_SESSION['mensaje'] = "¡Mercancía recibida e inventario actualizado exitosamente!";

            } catch (Exception $e) {
                $_SESSION['error'] = "Error al procesar la recepción: " . $e->getMessage();
            }

            header('Location: ' . BASE_URL . '/inventario');
            exit;
        }
    }

    public function historial() {
        $tasaUsd = $this->bcvService->getCurrentRate('USD');
        
        // Paginación
        $porPagina = 10; // Cantidad de lotes por página
        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        if ($paginaActual < 1) $paginaActual = 1;
        
        $offset = ($paginaActual - 1) * $porPagina;
        
        $totalRegistros = Producto::contarTotalRecepciones();
        $totalPaginas = ceil($totalRegistros / $porPagina);
        
        $recepciones = Producto::obtenerHistorialRecepcionesPaginado($porPagina, $offset);
        
        require_once BASE_PATH . 'views/inventario/historial.php';
    }

    // Método opcional vía AJAX para retornar los detalles de un lote y mostrarlos en el modal
    public function verDetalleRecepcion($id) {
        if ($id) {
            $detalles = Producto::obtenerDetallesRecepcion($id);
            header('Content-Type: application/json');
            echo json_encode($detalles);
            exit;
        }
    }
}