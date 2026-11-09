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
    <link rel="icon" href="/anacosmeticsy/public/assets/img/logo_ana.ico" type="image/x-icon"/>
    
    <!-- CSS Local de Tabler (Offline) -->
    <link href="/anacosmeticsy/public/assets/css/tabler.min.css" rel="stylesheet"/>

    <style>
      /* Estilo de fondo con marca de agua corporativa sutil */
      body.layout-fluid {
        position: relative;
        background-color: #f8fafc;
      }
      body.layout-fluid::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-image: url('/anacosmeticsy/public/assets/img/logo_ana.jpeg');
        background-repeat: no-repeat;
        background-position: center center;
        background-size: 450px auto;
        opacity: 0.04; /* Opacidad muy baja estilo marca de agua para no afectar la legibilidad */
        pointer-events: none;
        z-index: 0;
      }
      .page {
        position: relative;
        z-index: 1;
      }
    </style>
  </head>
  <body class="layout-fluid">
    <div class="page">
      <div class="page-wrapper">
        <!-- Contenedor fluid para ocupar todo el ancho disponible -->
        <div class="container-fluid py-4 px-3 px-lg-4">