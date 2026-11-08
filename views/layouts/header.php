<?php
require_once BASE_PATH . 'app/Services/BcvRateService.php';
$bcvService = new BcvRateService();
$tasaActual = $bcvService->getCurrentRate('USD');
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Ana Cosmetics — Sistema de Gestión</title>
    
    <!-- CSS Local de Tabler (Offline) -->
    <link href="/anacosmeticsy/public/assets/css/tabler.min.css" rel="stylesheet"/>
  </head>
  <body class="layout-fluid">
    <div class="page">
      <div class="page-wrapper">
        <!-- Cambiamos container-xl por container-fluid para ocupar todo el ancho disponible -->
        <div class="container-fluid py-4 px-3 px-lg-4">