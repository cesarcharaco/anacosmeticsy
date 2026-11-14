<?php
// app/Controllers/AuthController.php

require_once BASE_PATH . 'app/Models/Usuario.php';

class AuthController {

    // Muestra la vista de login
    public function index() {
        // Si ya hay sesión iniciada, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        require_once BASE_PATH . 'views/auth/login.php';
    }

    // Procesa el formulario de inicio de sesión
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "Por favor, completa todos los campos.";
                require_once BASE_PATH . 'views/auth/login.php';
                return;
            }

            $usuario = Usuario::buscarPorEmail($email);

            if ($usuario && password_verify($password, $usuario['password'])) {
                // Credenciales correctas: iniciamos sesión
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_nombre'] = $usuario['nombre'];
                $_SESSION['user_email'] = $usuario['email'];
                $_SESSION['user_rol'] = $usuario['rol'];

                // Redireccionar al dashboard general
                header('Location: ' . BASE_URL . '/dashboard');
                exit;
            } else {
                $error = "Credenciales incorrectas. Verifica tu correo y contraseña.";
                require_once BASE_PATH . 'views/auth/login.php';
            }
        }
    }

    // Cierra la sesión
    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . '/auth');
        exit;
    }
}