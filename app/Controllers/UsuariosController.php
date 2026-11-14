<?php
// app/Controllers/UsuariosController.php

require_once BASE_PATH . 'app/Models/Usuario.php';

class UsuariosController {

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth');
            exit;
        }
    }

    // Listado general de usuarios (Exclusivo Admin)
    public function index() {
        if ($_SESSION['user_rol'] !== 'admin') {
            $_SESSION['error'] = "No tienes permisos para acceder a la gestión de usuarios.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $usuarios = Usuario::obtenerTodos();
        require_once BASE_PATH . 'views/usuarios/index.php';
    }

    // Vista de perfil y seguridad del usuario autenticado
    public function perfil() {
        $usuarioId = $_SESSION['user_id'];
        $usuario = Usuario::buscarPorId($usuarioId);
        
        require_once BASE_PATH . 'views/usuarios/perfil.php';
    }

    // Registrar nuevo usuario desde el módulo administrativo
    public function guardar() {
        if ($_SESSION['user_rol'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Acción no autorizada.']);
            exit;
        }

        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            Usuario::registrar([
                'nombre'   => $input['nombre'] ?? '',
                'email'    => $input['email'] ?? '',
                'password' => $input['password'] ?? '',
                'rol'      => $input['rol'] ?? 'operador'
            ]);

            echo json_encode(['success' => true, 'message' => '¡Usuario registrado exitosamente!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // Actualizar campo individual vía AJAX desde el perfil o administración
    public function actualizarPerfilCampo() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $usuarioId = $_SESSION['user_id'];

            if (!$input) {
                throw new Exception("Datos inválidos.");
            }

            $campo = key($input);
            $valor = trim($input[$campo]);

            if (empty($valor)) {
                throw new Exception("El valor no puede estar vacío.");
            }

            Usuario::actualizarCampo($usuarioId, $campo, $valor);

            if ($campo === 'nombre') {
                $_SESSION['user_nombre'] = $valor;
            }

            echo json_encode(['success' => true, 'message' => '¡Campo actualizado correctamente!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // Eliminar usuario
    public function eliminar($id) {
        if ($_SESSION['user_rol'] !== 'admin') {
            $_SESSION['error'] = "No tienes permisos para realizar esta acción.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        try {
            Usuario::eliminar($id);
            $_SESSION['mensaje'] = "Usuario eliminado correctamente del sistema.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ' . BASE_URL . '/usuarios');
        exit;
    }

    /**
     * Procesar la edición de usuario enviada desde el modal administrativo.
     */
    public function editar() {
        if ($_SESSION['user_rol'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Acción no autorizada.']);
            exit;
        }

        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $id       = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);
            $nombre   = trim($input['nombre'] ?? '');
            $email    = trim($input['email'] ?? '');
            $rol      = $input['rol'] ?? 'operador';
            $password = trim($input['password'] ?? '');

            if (!$id || empty($nombre) || empty($email)) {
                throw new Exception("Los campos Nombre y Email son obligatorios.");
            }

            Usuario::actualizarAdmin($id, $nombre, $email, $rol, $password);

            // Si el admin editó su propio nombre desde esta tabla, actualizar sesión
            if ($id == $_SESSION['user_id']) {
                $_SESSION['user_nombre'] = $nombre;
                $_SESSION['user_rol'] = $rol;
            }

            echo json_encode(['success' => true, 'message' => '¡Usuario actualizado exitosamente!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}