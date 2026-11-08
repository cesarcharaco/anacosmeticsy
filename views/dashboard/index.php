<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<!-- Header tipo App Bar Responsivo -->
<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">
      
      <!-- Usuario y Título Principal -->
      <div class="col-12 col-md">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-primary-lt text-primary font-weight-bold">AC</span>
          <div>
            <h2 class="page-title mb-0 fs-2">Ana Cosmetics</h2>
            <div class="text-muted text-secondary small d-flex flex-wrap align-items-center gap-2 mt-1">
              <span>Sesión activa: <strong><?= htmlspecialchars($_SESSION['user_nombre']) ?></strong></span>
              <span class="d-none d-sm-inline">&bull;</span>
              <span class="badge bg-blue-lt text-uppercase"><?= htmlspecialchars($_SESSION['user_rol']) ?></span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Indicador de Tasa BCV + Botón Salir -->
      <div class="col-12 col-md-auto ms-md-auto d-flex align-items-center justify-content-between justify-content-md-end gap-2 mt-2 mt-md-0 pt-2 pt-md-0 border-top border-top-md-0">
        <div class="bg-green-lt text-green border border-green-subtle px-3 py-1 rounded-pill d-flex align-items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar me-0" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4h-6" /><path d="M12 3v3m0 12v3" /></svg>
          <span class="small fw-bold">BCV:</span>
          <span class="fw-extrabold fs-3">Bs. <?= number_format($tasaActual, 2, ',', '.') ?></span>
        </div>

        <a href="/anacosmeticsy/public/auth/logout" class="btn btn-outline-danger d-flex align-items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout me-0" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12m-3 -3l3 3l-3 3" /></svg>
          Salir
        </a>
      </div>

    </div>
  </div>
</div>

<!-- Resto del Dashboard (Alertas y Cajas de Módulos)... -->

<!-- Cuerpo Principal tipo App Grid -->
<div class="page-body">
  <div class="container-xl">
    
    <!-- Mensaje de bienvenida minimalista -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="alert alert-important alert-info mb-0" role="alert">
          <div class="d-flex">
            <div>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
            </div>
            <div>
              Selecciona un módulo operativo para comenzar a gestionar el inventario, citas, caja o cuentas por cobrar.
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Grid de Módulos (Tarjetas interactivas estilo App) -->
    <div class="row row-cards">
      
      <!-- 1. Módulo de Inventario -->
      <div class="col-sm-6 col-lg-4">
        <a href="/anacosmeticsy/public/inventario" class="card card-link card-link-pop text-decoration-none h-100 shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-purple-lt mb-3 mx-auto">
              <!-- Icono de Inventario / Paquete -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12v9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
            </span>
            <h3 class="card-title mb-1 text-dark">Inventario & Precios</h3>
            <p class="text-muted small mb-0">Catálogo general, control de stock, costos en USD y cálculo automático de porcentajes de ganancia.</p>
          </div>
        </a>
      </div>

      <!-- 2. Módulo de Citas y Servicios -->
      <div class="col-sm-6 col-lg-4">
        <a href="/anacosmeticsy/public/citas" class="card card-link card-link-pop text-decoration-none h-100 shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-pink-lt mb-3 mx-auto">
              <!-- Icono de Calendario / Citas -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M8 15h2v2h-2z" /></svg>
            </span>
            <h3 class="card-title mb-1 text-dark">Citas & Servicios</h3>
            <p class="text-muted small mb-0">Agenda de salón, catálogo de servicios de manicura/uñas y generación rápida de cotizaciones.</p>
          </div>
        </a>
      </div>

      <!-- 3. Módulo de Salidas y Despachos -->
      <div class="col-sm-6 col-lg-4">
        <a href="/anacosmeticsy/public/salidas" class="card card-link card-link-pop text-decoration-none h-100 shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-green-lt mb-3 mx-auto">
              <!-- Icono de Carrito / Caja / Ventas -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shopping-cart" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
            </span>
            <h3 class="card-title mb-1 text-dark">Salidas & Caja</h3>
            <p class="text-muted small mb-0">Despachos comerciales, consumo interno, pagos mixtos (Efectivo, Zelle, USDT, Pago Móvil) y Tasa BCV.</p>
          </div>
        </a>
      </div>

      <!-- 4. Módulo de Cuentas por Cobrar / Créditos -->
      <div class="col-sm-6 col-lg-4">
        <a href="/anacosmeticsy/public/creditos" class="card card-link card-link-pop text-decoration-none h-100 shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-yellow-lt mb-3 mx-auto">
              <!-- Icono de Tarjeta / Crédito / Cobros -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt-2" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" /><path d="M9 7h6" /><path d="M9 11h6" /><path d="M9 15h4" /></svg>
            </span>
            <h3 class="card-title mb-1 text-dark">Créditos & Saldos</h3>
            <p class="text-muted small mb-0">Gestión de cuentas por cobrar, abonos parciales, saldos pendientes y control de saldos a favor de clientes.</p>
          </div>
        </a>
      </div>

      <!-- 5. Módulo de Clientes -->
      <div class="col-sm-6 col-lg-4">
        <a href="/anacosmeticsy/public/clientes" class="card card-link card-link-pop text-decoration-none h-100 shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-blue-lt mb-3 mx-auto">
              <!-- Icono de Usuarios / Clientes -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
            </span>
            <h3 class="card-title mb-1 text-dark">Directorio de Clientes</h3>
            <p class="text-muted small mb-0">Base de datos de clientes, historial de compras, servicios y saldo a favor disponible.</p>
          </div>
        </a>
      </div>

      <!-- 6. Configuración / Tasa BCV (Solo Admin) -->
      <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin'): ?>
      <div class="col-sm-6 col-lg-4">
        <a href="/anacosmeticsy/public/configuracion" class="card card-link card-link-pop text-decoration-none h-100 shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-red-lt mb-3 mx-auto">
              <!-- Icono de Ajustes / Configuración -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M12 12m-3 0 a3 3 0 1 0 6 0 a3 3 0 1 0 -6 0" /></svg>
            </span>
            <h3 class="card-title mb-1 text-dark">Ajustes & Tasa BCV</h3>
            <p class="text-muted small mb-0">Gestión de usuarios del sistema, control de roles y sincronización manual de la tasa cambiaria oficial.</p>
          </div>
        </a>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>