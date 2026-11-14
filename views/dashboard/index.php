<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<!-- Estilo CSS integrado para el efecto translúcido / glassmorphism en las tarjetas -->
<style>
  /* Fondo translúcido solo en las tarjetas de los módulos */
  .row-cards .card.card-link {
    background: rgba(255, 255, 255, 0.20) !important;

    border: 1px solid rgba(255, 255, 255, 0.40) !important;
    border-radius: 16px;

    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08) !important;

    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);

    overflow: hidden;

    transition:
      transform 0.2s ease,
      background-color 0.2s ease,
      box-shadow 0.2s ease;
  }

  /* El interior no debe añadir otra capa blanca */
  .row-cards .card.card-link > .card-body {
    background: transparent !important;

    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
  }

  /* Mantener la transparencia al pasar el cursor */
  .row-cards .card.card-link:hover {
    background: rgba(255, 255, 255, 0.30) !important;

    transform: translateY(-3px);

    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
  }

  .row-cards .card.card-link,
  .row-cards .card.card-link:hover {
    background: transparent !important;

    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
  }
</style>

<!-- Header tipo App Bar Responsivo -->
<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">
      
      <!-- Logo y Título Principal (Razón Social Real) -->
      <div class="col-12 col-md">
        <div class="d-flex align-items-center gap-3">
          <!-- Logotipo de la Empresa (Responsive) -->
          <span class="avatar avatar-lg rounded bg-white border p-1 shadow-sm flex-shrink-0" style="width: 48px; height: 48px;">
            <img src="<?= BASE_URL ?>/assets/img/logo_ana.jpeg" alt="Logo Ana Boutique Cosmetics" class="w-100 h-100 object-fit-contain rounded">
          </span>
          <div>
            <h2 class="page-title mb-0 fs-3 text-dark fw-bold">ANA BOUTIQUE COSMETICS, C.A.</h2>
            <div class="text-muted text-secondary small d-flex flex-wrap align-items-center gap-2 mt-1">
              <span class="fw-bold text-primary">RIF: J508365690</span>
              <span class="d-none d-sm-inline">&bull;</span>
              <span>Gestión Integral de Salón & Comercio</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Indicador Tasa BCV + Dropdown Desplegable de Usuario -->
      <div class="col-12 col-md-auto ms-md-auto d-flex align-items-center justify-content-between justify-content-md-end gap-3 mt-2 mt-md-0 pt-2 pt-md-0 border-top border-top-md-0">
        
        <!-- Píldora de Tasa BCV -->
        <div class="bg-green-lt text-green border border-green-subtle px-3 py-1 rounded-pill d-flex align-items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar me-0" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4h-6" /><path d="M12 3v3m0 12v3" /></svg>
          <span class="small fw-bold">BCV:</span>
          <span class="fw-extrabold fs-3">Bs. <?= number_format($tasaActual ?? $tasaUsd ?? 0, 2, ',', '.') ?></span>
        </div>

        <!-- DROPDOWN DE USUARIO Y PERFIL -->
        <div class="nav-item dropdown">
          <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
            <span class="avatar avatar-sm bg-blue-lt fw-bold text-uppercase">
              <?= strtoupper(substr($_SESSION['user_nombre'] ?? 'U', 0, 2)) ?>
            </span>
            <div class="d-none d-xl-block ps-2">
              <div class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['user_nombre'] ?? 'Usuario') ?></div>
              <div class="mt-1 small text-muted text-uppercase fw-bold fs-6">
                <span class="badge bg-blue-lt px-1"><?= htmlspecialchars($_SESSION['user_rol'] ?? 'operador') ?></span>
              </div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-down ms-1 text-muted" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" /></svg>
          </a>

          <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow shadow-sm border-0 rounded-3 mt-2">
            <!-- Encabezado en móvil -->
            <div class="dropdown-header d-xl-none border-bottom pb-2 mb-1">
              <div class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['user_nombre'] ?? 'Usuario') ?></div>
              <span class="badge bg-blue-lt text-uppercase mt-1"><?= htmlspecialchars($_SESSION['user_rol'] ?? 'operador') ?></span>
            </div>

            <!-- Opción 1: Mi Perfil -->
            <a href="<?= BASE_URL ?>/usuarios/perfil" class="dropdown-item d-flex align-items-center gap-2 py-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user text-primary" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
              Mi Perfil & Seguridad
            </a>

            <!-- Opción 2: Gestión de Usuarios (Exclusivo Admin) -->
            <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin'): ?>
              <a href="<?= BASE_URL ?>/usuarios" class="dropdown-item d-flex align-items-center gap-2 py-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users-group text-purple" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 13a5 5 0 1 0 -5 -5" /><path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" /><path d="M15 5a5 5 0 1 0 5 5" opacity=".5" /><path d="M17 21v-1a2 2 0 0 0 -2 -2h-1" opacity=".5" /></svg>
                Gestión de Usuarios
              </a>
            <?php endif; ?>

            <div class="dropdown-divider"></div>

            <!-- Opción 3: Salir / Cerrar Sesión -->
            <a href="<?= BASE_URL ?>/auth/logout" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12m-3 -3l3 3l-3 3" /></svg>
              Cerrar Sesión
            </a>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

<!-- Cuerpo Principal tipo App Grid -->
<div class="page-body">
  <div class="container-xl">
    
    <!-- Mensaje de bienvenida minimalista -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="alert alert-important alert-info mb-0 shadow-sm" style="background: rgba(209, 236, 241, 0.85); backdrop-filter: blur(4px);" role="alert">
          <div class="d-flex">
            <div  style="color: #000000;">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
            </div>
            <div style="color: #000000;">
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
        <a href="<?= BASE_URL ?>/inventario" class="card card-link card-link-pop text-decoration-none h-100 shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-purple-lt mb-3 mx-auto">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12v9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
            </span>
            <h3 class="card-title mb-1 text-dark">Inventario & Precios</h3>
            <p class="text-muted small mb-0">Catálogo general, control de stock, costos en USD y cálculo automático de porcentajes de ganancia.</p>
          </div>
        </a>
      </div>

      <!-- 2. Módulo de Citas y Servicios -->
      <div class="col-sm-6 col-lg-4">
        <a href="<?= BASE_URL ?>/citas" class="card card-link card-link-pop text-decoration-none h-100 shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-pink-lt mb-3 mx-auto">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M8 15h2v2h-2z" /></svg>
            </span>
            <h3 class="card-title mb-1 text-dark">Citas & Servicios</h3>
            <p class="text-muted small mb-0">Agenda de salón, catálogo de servicios de manicura/uñas y generación rápida de cotizaciones.</p>
          </div>
        </a>
      </div>

      <!-- 3. Módulo de Salidas y Despachos -->
      <div class="col-sm-6 col-lg-4">
        <a href="<?= BASE_URL ?>/ventas" class="card card-link card-link-pop text-decoration-none h-100 shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-green-lt mb-3 mx-auto">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shopping-cart" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
            </span>
            <h3 class="card-title mb-1 text-dark">Salidas & Caja</h3>
            <p class="text-muted small mb-0">Despachos comerciales, consumo interno, pagos mixtos (Efectivo, Zelle, USDT, Pago Móvil) y Tasa BCV.</p>
          </div>
        </a>
      </div>

      <!-- 4. Módulo de Cuentas por Cobrar / Créditos -->
      <div class="col-sm-6 col-lg-4">
        <a href="<?= BASE_URL ?>/creditos" class="card card-link card-link-pop text-decoration-none h-100 shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-yellow-lt mb-3 mx-auto">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt-2" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" /><path d="M9 7h6" /><path d="M9 11h6" /><path d="M9 15h4" /></svg>
            </span>
            <h3 class="card-title mb-1 text-dark">Créditos & Saldos</h3>
            <p class="text-muted small mb-0">Gestión de cuentas por cobrar, abonos parciales, saldos pendientes y control de saldos a favor de clientes.</p>
          </div>
        </a>
      </div>

      <!-- 5. Módulo de Clientes -->
      <div class="col-sm-6 col-lg-4">
        <a href="<?= BASE_URL ?>/clientes" class="card card-link card-link-pop text-decoration-none h-100 shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-blue-lt mb-3 mx-auto">
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
        <a href="<?= BASE_URL ?>/configuracion" class="card card-link card-link-pop text-decoration-none h-100 shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-red-lt mb-3 mx-auto">
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