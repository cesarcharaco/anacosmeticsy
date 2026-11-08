<?php
// app/Controllers/ConfiguracionController.php

require_once BASE_PATH . 'app/Services/BcvRateService.php';

class ConfiguracionController {

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
        
        // Obtener historial de tasas guardadas
        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM exchange_rates ORDER BY fetched_at DESC LIMIT 10");
        $historial = $stmt->fetchAll();

        require_once BASE_PATH . 'views/configuracion/index.php';
    }

    // Acción para actualizar manualmente por AJAX o POST
    public function actualizarTasa() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nuevaTasa = $this->bcvService->refreshRate('USD');
            $_SESSION['mensaje'] = "Tasa BCV actualizada con éxito: Bs. " . number_format($nuevaTasa, 2, ',', '.');
            header('Location: /anacosmeticsy/public/configuracion');
            exit;
        }
    }
}