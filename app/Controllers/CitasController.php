<?php
// app/Controllers/CitasController.php

require_once BASE_PATH . 'app/Models/Servicio.php';
require_once BASE_PATH . 'app/Models/Cita.php';
require_once BASE_PATH . 'app/Services/BcvRateService.php';

class CitasController {

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
        
        $servicios = Servicio::obtenerTodos();
        $citas = Cita::obtenerTodas();
        
        require_once BASE_PATH . 'app/Models/Cliente.php';
        $clientes = Cliente::obtenerTodos(100, 0); // Traemos una lista amplia para el selector del modal

        require_once BASE_PATH . 'views/citas/index.php';
    }

    public function guardarCita() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // 1. Validar cliente obligatorio
                $clienteId = filter_var($_POST['cliente_id'] ?? null, FILTER_VALIDATE_INT);
                if (!$clienteId) {
                    throw new Exception("Debe seleccionar un cliente válido para agendar la cita.");
                }

                // 2. Validar fecha y hora obligatoria
                $fechaCita = trim($_POST['fecha_cita'] ?? '');
                if (empty($fechaCita)) {
                    throw new Exception("La fecha y hora de la cita son obligatorias.");
                }

                // 3. Validar arreglo de servicios del carrito
                $servicios = $_POST['servicios'] ?? [];
                if (!is_array($servicios) || empty($servicios)) {
                    throw new Exception("Debe agregar al menos un servicio al carrito de la cita.");
                }

                $data = [
                    'cliente_id'    => $clienteId,
                    'usuario_id'    => $_SESSION['user_id'] ?? 1,
                    'fecha_cita'    => $fechaCita,
                    'observaciones' => trim($_POST['observaciones'] ?? '')
                ];

                // Ejecutamos la creación transaccional
                Cita::crearConServicios($data, $servicios);

                $_SESSION['mensaje'] = "¡Cita agendada exitosamente en el sistema!";

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            header('Location: /anacosmeticsy/public/citas');
            exit;
        }
    }

    public function guardarServicio() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = trim($_POST['nombre'] ?? '');
                if (empty($nombre)) {
                    throw new Exception("El nombre del servicio es obligatorio.");
                }
                if (strlen($nombre) > 150) {
                    throw new Exception("El nombre del servicio no puede superar los 150 caracteres.");
                }

                $descripcion = trim($_POST['descripcion'] ?? '');
                if (strlen($descripcion) > 500) {
                    throw new Exception("La descripción es demasiado larga (máximo 500 caracteres).");
                }

                $precioRaw = $_POST['precio_usd'] ?? '';
                if ($precioRaw === '' || !is_numeric($precioRaw)) {
                    throw new Exception("Debe ingresar un precio válido en dólares.");
                }
                
                $precioUsd = filter_var($precioRaw, FILTER_VALIDATE_FLOAT);
                if ($precioUsd === false || $precioUsd < 0) {
                    throw new Exception("El precio en USD no puede ser negativo.");
                }

                $data = [
                    'nombre'      => $nombre,
                    'descripcion' => $descripcion,
                    'precio_usd'  => round($precioUsd, 2)
                ];

                Servicio::crear($data);
                $_SESSION['mensaje'] = "¡Servicio registrado exitosamente en el catálogo!";

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            header('Location: /anacosmeticsy/public/citas');
            exit;
        }
    }

    public function actualizarServicio() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
                if (!$id) throw new Exception("ID de servicio no válido.");

                $nombre = trim($_POST['nombre'] ?? '');
                if (empty($nombre)) throw new Exception("El nombre del servicio es obligatorio.");

                $precioUsd = filter_var($_POST['precio_usd'] ?? '', FILTER_VALIDATE_FLOAT);
                if ($precioUsd === false || $precioUsd < 0) throw new Exception("Precio en USD inválido.");

                $data = [
                    'nombre'      => $nombre,
                    'descripcion' => trim($_POST['descripcion'] ?? ''),
                    'precio_usd'  => round($precioUsd, 2)
                ];

                Servicio::actualizar($id, $data);
                $_SESSION['mensaje'] = "Servicio actualizado correctamente.";
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /anacosmeticsy/public/citas');
            exit;
        }
    }

    public function eliminarServicio($id) {
        try {
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if (!$id) throw new Exception("ID de servicio no válido.");

            Servicio::eliminar($id);
            $_SESSION['mensaje'] = "Servicio eliminado del catálogo.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: /anacosmeticsy/public/citas');
        exit;
    }
}