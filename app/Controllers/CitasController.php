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
        $citasRaw = Cita::obtenerTodas();
        
        // Inyectar servicios específicos a cada cita
        $citas = [];
        foreach ($citasRaw as $cita) {
            $cita['servicios'] = Cita::obtenerServiciosDeCita($cita['id']);
            $citas[] = $cita;
        }

        require_once BASE_PATH . 'app/Models/Cliente.php';
        $clientes = Cliente::obtenerTodos(100, 0);

        require_once BASE_PATH . 'views/citas/index.php';
    }

    public function guardarCita() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $clienteId = filter_var($_POST['cliente_id'] ?? null, FILTER_VALIDATE_INT);
                if (!$clienteId) throw new Exception("Debe seleccionar un cliente válido.");

                $fechaCita = trim($_POST['fecha_cita'] ?? '');
                if (empty($fechaCita)) throw new Exception("La fecha y hora son obligatorias.");

                $servicios = $_POST['servicios'] ?? [];
                if (!is_array($servicios) || empty($servicios)) throw new Exception("Debe agregar al menos un servicio.");

                $data = [
                    'cliente_id'    => $clienteId,
                    'usuario_id'    => $_SESSION['user_id'] ?? 1,
                    'fecha_cita'    => $fechaCita,
                    'observaciones' => trim($_POST['observaciones'] ?? '')
                ];

                Cita::crearConServicios($data, $servicios);
                $_SESSION['mensaje'] = "¡Cita agendada exitosamente!";
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /anacosmeticsy/public/citas');
            exit;
        }
    }

    public function cancelarCita($id) {
        try {
            Cita::cancelar($id);
            $_SESSION['mensaje'] = "La cita ha sido cancelada exitosamente.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: /anacosmeticsy/public/citas');
        exit;
    }

    public function reagendarCita() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = filter_var($_POST['cita_id'] ?? null, FILTER_VALIDATE_INT);
                $nuevaFecha = trim($_POST['nueva_fecha_cita'] ?? '');

                if (!$id) throw new Exception("ID de cita inválido.");
                if (empty($nuevaFecha)) throw new Exception("Debe indicar la nueva fecha y hora.");

                Cita::reagendar($id, $nuevaFecha);
                $_SESSION['mensaje'] = "¡La cita ha sido reagendada exitosamente!";
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
                if (empty($nombre)) throw new Exception("El nombre del servicio es obligatorio.");

                $precioUsd = filter_var($_POST['precio_usd'] ?? '', FILTER_VALIDATE_FLOAT);
                if ($precioUsd === false || $precioUsd < 0) throw new Exception("Precio en USD inválido.");

                Servicio::crear([
                    'nombre'      => $nombre,
                    'descripcion' => trim($_POST['descripcion'] ?? ''),
                    'precio_usd'  => round($precioUsd, 2)
                ]);
                $_SESSION['mensaje'] = "¡Servicio registrado exitosamente!";
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
                if (!$id) throw new Exception("ID no válido.");

                $nombre = trim($_POST['nombre'] ?? '');
                if (empty($nombre)) throw new Exception("Nombre obligatorio.");

                $precioUsd = filter_var($_POST['precio_usd'] ?? '', FILTER_VALIDATE_FLOAT);
                if ($precioUsd === false || $precioUsd < 0) throw new Exception("Precio inválido.");

                Servicio::actualizar($id, [
                    'nombre'      => $nombre,
                    'descripcion' => trim($_POST['descripcion'] ?? ''),
                    'precio_usd'  => round($precioUsd, 2)
                ]);
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
            if (!$id) throw new Exception("ID no válido.");

            Servicio::eliminar($id);
            $_SESSION['mensaje'] = "Servicio eliminado del catálogo.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: /anacosmeticsy/public/citas');
        exit;
    }
}