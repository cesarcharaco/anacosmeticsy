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
    <title>Ana Boutique Cosmetics, C.A. — Sistema de Gestión</title>
    
    <!-- Favicon de la Empresa -->
    <link rel="icon" href="<?= BASE_URL ?>/assets/img/logo_ana.ico" type="image/x-icon"/>
    
    <!-- CSS Local de Tabler (Offline) -->
    <link href="<?= BASE_URL ?>/assets/css/tabler.min.css" rel="stylesheet"/>

    <style>
      /* Estilo de fondo con marca de agua corporativa sutil corregido para modales */
      body.layout-fluid {
        background-color: #f8fafc;
        background-image: url('<?= BASE_URL ?>/assets/img/logo_ana.jpeg');
        background-repeat: no-repeat;
        background-position: center center;
        background-attachment: fixed;
        background-size: 450px auto;
      }
      /* Forzar que la marca de agua tenga una opacidad muy sutil sobre el fondo sin bloquear clics */
      body.layout-fluid::before {
        content: "";
        position: fixed;
        inset: 0;
        background-color: rgba(248, 250, 252, 0.96); /* Tapa el fondo con blanco translúcido para proteger la legibilidad */
        pointer-events: none;
        z-index: -1;
      }
    </style>
  </head>
  <body class="layout-fluid">
    <div class="page">
      <div class="page-wrapper">
        <!-- Contenedor fluid para ocupar todo el ancho disponible -->
        <div class="container-fluid py-4 px-3 px-lg-4">