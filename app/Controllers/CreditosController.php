<?php
// app/Controllers/CreditosController.php

require_once BASE_PATH . 'app/Models/Credito.php';
require_once BASE_PATH . 'app/Models/Cliente.php';
require_once BASE_PATH . 'app/Services/BcvRateService.php';

class CreditosController {

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
        $tasaActual = $tasaUsd;

        $porPagina = 10;
        $paginaActual = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
        $offset = ($paginaActual - 1) * $porPagina;

        $totalClientes = Cliente::contarTotal();
        $totalPaginas = ceil($totalClientes / $porPagina);
        $clientes = Cliente::obtenerTodos($porPagina, $offset);

        $clienteSeleccionadoId = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : (!empty($clientes) ? $clientes[0]['id'] : null);
        
        $clienteActual = null;
        $creditosCliente = [];

        if ($clienteSeleccionadoId) {
            $clienteActual = Cliente::obtenerPorId($clienteSeleccionadoId);
            $creditosCliente = Credito::obtenerPorCliente($clienteSeleccionadoId);
        }

        require_once BASE_PATH . 'views/creditos/index.php';
    }

    public function obtenerHistorialCliente($clienteId) {
        header('Content-Type: application/json');
        try {
            $clienteId = filter_var($clienteId, FILTER_VALIDATE_INT);
            if (!$clienteId) throw new Exception("ID de cliente inválido.");

            $cliente = Cliente::obtenerPorId($clienteId);
            $creditos = Credito::obtenerPorCliente($clienteId);

            $todosLosAbonos = [];
            foreach ($creditos as &$cred) {
                $cred['detalles'] = Credito::obtenerDetalles($cred['id']);
                $abonosCredito = Credito::obtenerAbonos($cred['id']);
                
                foreach ($abonosCredito as $ab) {
                    $ab['credito_id_ref'] = $cred['id'];
                    $todosLosAbonos[] = $ab;
                }
            }

            echo json_encode([
                'success' => true,
                'cliente' => $cliente,
                'creditos' => $creditos,
                'abonos' => $todosLosAbonos
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // Método para generar el reporte imprimible de deuda pendiente
    public function reporteDeuda($clienteId) {
        $clienteId = filter_var($clienteId, FILTER_VALIDATE_INT);
        if (!$clienteId) {
            die("Cliente no válido.");
        }

        $tasaUsd = $this->bcvService->getCurrentRate('USD');
        $cliente = Cliente::obtenerPorId($clienteId);
        $creditosPendientes = Credito::obtenerReportePendientesCliente($clienteId);

        require_once BASE_PATH . 'views/creditos/reporte_deuda.php';
    }

    public function abonar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clienteIdRedirect = $_POST['cliente_id_redirect'] ?? '';
            try {
                $tasaUsd = $this->bcvService->getCurrentRate('USD');
                $creditoId = filter_var($_POST['credito_id'] ?? null, FILTER_VALIDATE_INT);

                if (!$creditoId) {
                    throw new Exception("No se especificó un crédito válido para aplicar el abono.");
                }

                $data = [
                    'credito_id' => $creditoId,
                    'tasa_bcv'   => $tasaUsd,
                    'pagos'      => $_POST['pagos'] ?? [] 
                ];

                Credito::registrarAbono($data);
                $_SESSION['mensaje'] = "¡Abono registrado exitosamente!";
                
                header('Location: /anacosmeticsy/public/creditos' . ($clienteIdRedirect ? "?cliente_id=$clienteIdRedirect" : ''));
                exit;

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /anacosmeticsy/public/creditos' . ($clienteIdRedirect ? "?cliente_id=$clienteIdRedirect" : ''));
                exit;
            }
        }
    }

    public function buscarClientes() {
        header('Content-Type: application/json');
        try {
            $query = trim($_GET['q'] ?? '');
            $db = Database::connect();
            
            if (empty($query)) {
                $stmt = $db->query("SELECT * FROM clientes ORDER BY nombre ASC LIMIT 10");
                $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $searchTerm = "%" . $query . "%";
                $stmt = $db->prepare("SELECT * FROM clientes WHERE nombre LIKE ? OR telefono LIKE ? ORDER BY nombre ASC LIMIT 15");
                $stmt->execute([$searchTerm, $searchTerm]);
                $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            echo json_encode([
                'success' => true,
                'clientes' => $clientes
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function anularAbono() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clienteIdRedirect = $_POST['cliente_id_redirect'] ?? '';
            try {
                $abonoId = $_POST['abono_id'] ?? null;

                Credito::anularAbono($abonoId);
                $_SESSION['mensaje'] = "¡Abono anulado exitosamente y saldos revertidos!";

                header('Location: /anacosmeticsy/public/creditos' . ($clienteIdRedirect ? "?cliente_id=$clienteIdRedirect" : ''));
                exit;

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /anacosmeticsy/public/creditos' . ($clienteIdRedirect ? "?cliente_id=$clienteIdRedirect" : ''));
                exit;
            }
        }
    }
}