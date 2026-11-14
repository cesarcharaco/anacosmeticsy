<?php
// app/Controllers/DashboardController.php

class DashboardController {

    public function index() {
        // Validación estricta de sesión (Middleware casero)
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth');
            exit;
        }

        require_once BASE_PATH . 'views/dashboard/index.php';
    }
}