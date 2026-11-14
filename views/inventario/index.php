<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<!-- Header tipo App Bar Responsivo -->
<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">  
      <!-- Título e Información del Módulo -->
      <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-purple-lt text-purple font-weight-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12v9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
          </span>
          <div>
            <h2 class="page-title mb-0 fs-2">Inventario & Catálogo</h2>
            <div class="text-muted small">Tasa BCV: <span class="badge bg-green-lt fw-bold">Bs. <?= number_format($tasaUsd, 2, ',', '.') ?> /$</span></div>
          </div>
        </div>
      </div>
      
      <!-- Acciones Responsivas (Optimizadas para Móvil y PC) -->
      <div class="col-12 col-md-auto ms-md-auto">
        
        <!-- 1. VISTA MÓVIL: Botones principales + Menú desplegable completo -->
        <div class="d-flex d-md-none gap-2 w-100 flex-column">
          <div class="d-flex gap-2">
            <!-- Botón principal de carga -->
            <a href="<?= BASE_URL ?>/inventario/actualizar" class="btn btn-success flex-fill shadow-sm d-flex align-items-center justify-content-center gap-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck-delivery" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 a2 2 0 1 0 4 0 a2 2 0 1 0 -4 0" /><path d="M17 17m-2 a2 2 0 1 0 4 0 a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
              Carga Stock
            </a>
            <!-- Botón nuevo producto -->
            <button class="btn btn-primary shadow-sm d-flex align-items-center justify-content-center gap-1" data-bs-toggle="modal" data-bs-target="#modal-producto" onclick="abrirModalNuevo()">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
              Nuevo
            </button>
          </div>

          <!-- Dropdown para opciones secundarias en móvil -->
          <div class="dropdown">
            <button class="btn btn-outline-secondary w-100 dropdown-toggle d-flex align-items-center justify-content-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-menu-2" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6l16 0" /><path d="M4 12l16 0" /><path d="M4 18l16 0" /></svg>
              Más Opciones y Menú
            </button>
            <ul class="dropdown-menu w-100 shadow-sm border-0">
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-warning" href="<?= BASE_URL ?>/mermas">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.871l-8.106 -13.534a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                  Control de Mermas / Salidas
                </a>
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= BASE_URL ?>/inventario/historial">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-history text-purple" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8l0 4l2 2" /><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" /></svg>
                  Historial de Cargas
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= BASE_URL ?>/categoria">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-category text-primary" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v6h-6z" /><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M14 14h6v6h-6z" /></svg>
                  Gestionar Categorías
                </a>
              </li>
              <li>
                <button class="dropdown-item d-flex align-items-center gap-2 py-2 text-success" data-bs-toggle="modal" data-bs-target="#modal-rapido-categoria" onclick="limpiarModalCategoriaRapida()">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder-plus" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 19h-7a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2h4l3 3h7a2 2 0 0 1 2 2v3.5" /><path d="M16 19h6" /><path d="M19 16v6" /></svg>
                  + Registrar Categoría Rápida
                </button>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= BASE_URL ?>/dashboard">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left text-muted" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                  Volver al Dashboard
                </a>
              </li>
            </ul>
          </div>
        </div>

        <!-- 2. VISTA ESCRITORIO: Botones principales + Menú "Auditoría & Mermas" -->
        <div class="d-none d-md-flex flex-wrap gap-2">
          <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
            Volver
          </a>

          <!-- BOTÓN DIRECTO: Control de Mermas y Salidas -->
          <a href="<?= BASE_URL ?>/mermas" class="btn btn-outline-warning d-flex align-items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.871l-8.106 -13.534a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
            Control Mermas
          </a>

          <!-- Historial de Cargas -->
          <a href="<?= BASE_URL ?>/inventario/historial" class="btn btn-outline-purple d-flex align-items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-history" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8l0 4l2 2" /><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" /></svg>
            Historial Cargas
          </a>

          <!-- Actualizar / Recibir Stock -->
          <a href="<?= BASE_URL ?>/inventario/actualizar" class="btn btn-success d-flex align-items-center gap-1 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck-delivery" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 a2 2 0 1 0 4 0 a2 2 0 1 0 -4 0" /><path d="M17 17m-2 a2 2 0 1 0 4 0 a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
            Cargar Stock
          </a>

          <!-- Categorías -->
          <a href="<?= BASE_URL ?>/categoria" class="btn btn-outline-primary d-flex align-items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-category" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v6h-6z" /><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M14 14h6v6h-6z" /></svg>
            Categorías
          </a>

          <!-- Nuevo Producto -->
          <button class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-producto" onclick="abrirModalNuevo()">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Nuevo Producto
          </button>
        </div>

      </div>

    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">

    <!-- Mensajes de Notificación -->
    <?php if (isset($_SESSION['mensaje'])): ?>
      <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <div class="d-flex align-items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
          <div><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
    
    <!-- Mensajes de Error o Restricción -->
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <div class="d-flex align-items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5.07 19h13.86a1.5 1.5 0 0 0 1.3 -2.25l-6.93 -12a1.5 1.5 0 0 0 -2.6 0l-6.93 12a1.5 1.5 0 0 0 1.3 2.25z" /></svg>
          <div><?= htmlspecialchars($_SESSION['error']) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($productos)): ?>

      <!-- 1. VISTA MÓVIL (Tarjetas/Cards estilo App) -->
      <div class="d-block d-md-none">
        <div class="row row-cards">
          <?php foreach ($productos as $p): ?>
            <?php 
              $precioBs = $p['precio_usd'] * $tasaUsd;
              $stockBajo = $p['stock'] <= $p['stock_minimo'];
            ?>
            <div class="col-12">
              <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-3">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                      <span class="badge bg-secondary-lt fw-bold me-1"><?= htmlspecialchars($p['codigo']) ?></span>
                      <span class="badge bg-blue-lt"><?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin categoría') ?></span>
                      <h4 class="card-title mt-1 mb-0 fw-bold"><?= htmlspecialchars($p['nombre']) ?></h4>
                    </div>
                    <?php if ($stockBajo): ?>
                      <span class="badge bg-danger-lt text-danger fw-bold"><?= $p['stock'] ?> unds</span>
                    <?php else: ?>
                      <span class="badge bg-green-lt text-dark fw-bold"><?= $p['stock'] ?> unds</span>
                    <?php endif; ?>
                  </div>

                  <div class="row g-2 align-items-center my-2 p-2 bg-light rounded-2">
                    <div class="col-6">
                      <div class="text-muted small">Precio USD</div>
                      <div class="fw-bold text-success fs-3">$<?= number_format($p['precio_usd'], 2) ?></div>
                    </div>
                    <div class="col-6 text-end">
                      <div class="text-muted small">Precio BCV</div>
                      <div class="fw-bold text-primary fs-3">Bs. <?= number_format($precioBs, 2, ',', '.') ?></div>
                    </div>
                  </div>

                  <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <div class="small text-muted">Costo: <strong>$<?= number_format($p['costo_usd'], 2) ?></strong> (+<?= (int)$p['margen_ganancia'] ?>%)</div>
                    <div class="d-flex gap-2">
                      <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-producto" onclick='abrirModalEditar(<?= json_encode($p) ?>)'>
                        Editar
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmarEliminacion(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>')">
                        Eliminar
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 2. VISTA ESCRITORIO (Tabla tradicional) -->
      <div class="card shadow-sm border-0 rounded-3 d-none d-md-block">
        <div class="table-responsive">
          <table class="table table-vcenter card-table table-hover">
            <thead>
              <tr>
                <th>Código</th>
                <th>Producto / Categoría</th>
                <th>Costo USD</th>
                <th>Margen</th>
                <th>Precio USD</th>
                <th>Precio BCV (Bs.)</th>
                <th class="text-center">Stock</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($productos as $p): ?>
                <?php 
                  $precioBs = $p['precio_usd'] * $tasaUsd;
                  $stockBajo = $p['stock'] <= $p['stock_minimo'];
                ?>
                <tr>
                  <td class="text-muted fw-bold"><?= htmlspecialchars($p['codigo']) ?></td>
                  <td>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($p['nombre']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin categoría') ?></div>
                  </td>
                  <td class="text-muted">$<?= number_format($p['costo_usd'], 2) ?></td>
                  <td><span class="badge bg-blue-lt">+<?= number_format($p['margen_ganancia'], 0) ?>%</span></td>
                  <td class="fw-bold text-success">$<?= number_format($p['precio_usd'], 2) ?></td>
                  <td class="fw-bold text-primary">Bs. <?= number_format($precioBs, 2, ',', '.') ?></td>
                  <td class="text-center">
                    <?php if ($stockBajo): ?>
                      <span class="badge bg-danger-lt text-danger fw-bold px-2 py-1"><?= $p['stock'] ?> unds (Alerta)</span>
                    <?php else: ?>
                      <span class="badge bg-green-lt text-dark fw-bold px-2 py-1"><?= $p['stock'] ?> unds</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-icon btn-ghost-primary me-1" title="Editar" data-bs-toggle="modal" data-bs-target="#modal-producto" onclick='abrirModalEditar(<?= json_encode($p) ?>)'>
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                    </button>
                    <button type="button" class="btn btn-sm btn-icon btn-ghost-danger" title="Eliminar" onclick="confirmarEliminacion(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>')">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php else: ?>
      <!-- Estado Vacío Responsivo -->
      <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
          <div class="mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-box-off text-muted" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17.765 17.757l-5.765 3.243l-8 -4.5v-9l2.236 -1.258m3.764 -1.242l2 -1.125l8 4.5v7" /><path d="M12 12v9" /><path d="M12 12l8 -4.5" /><path d="M12 12l-8 -4.5" /><path d="M3 3l18 18" /></svg>
          </div>
          <h3 class="fw-bold text-secondary mb-1">No hay productos registrados</h3>
          <p class="small text-muted mb-3">Agrega tus productos para gestionar existencias y precios en tiempo real.</p>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-producto" onclick="abrirModalNuevo()">+ Registrar Producto</button>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

<!-- Modal Tabler Responsivo para Crear/Editar -->
<div class="modal modal-blur fade" id="modal-producto" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="<?= BASE_URL ?>/inventario/guardar" method="POST" autocomplete="off">
        
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-dark" id="modal-titulo">Nuevo Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-3 p-md-4">
          <input type="hidden" name="id" id="prod_id">
          
          <div class="row">
            <div class="col-12 col-md-4 mb-3">
              <label class="form-label required small text-muted text-uppercase fw-bold">Código / SKU</label>
              <input type="text" name="codigo" id="prod_codigo" class="form-control" placeholder="PROD-001" required>
            </div>
            <div class="col-12 col-md-8 mb-3">
              <label class="form-label required small text-muted text-uppercase fw-bold">Nombre del Producto</label>
              <input type="text" name="nombre" id="prod_nombre" class="form-control" placeholder="Ej. Esmalte Semipermanente Nude" required>
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-md-4 mb-3">
              <label class="form-label required small text-muted text-uppercase fw-bold">Categoría</label>
              <select name="categoria_id" id="prod_categoria_id" class="form-select" required>
                <option value="">Seleccione una categoría...</option>
                <?php foreach ($categorias as $cat): ?>
                  <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-6 col-md-4 mb-3">
              <label class="form-label required small text-muted text-uppercase fw-bold">Costo ($ USD)</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" step="0.01" min="0" name="costo_usd" id="prod_costo" class="form-control" placeholder="0.00" oninput="calcularPrecioModal()" required>
              </div>
            </div>
            <div class="col-6 col-md-4 mb-3">
              <label class="form-label required small text-muted text-uppercase fw-bold">Ganancia (%)</label>
              <div class="input-group">
                <input type="number" step="1" min="0" name="margen_ganancia" id="prod_margen" class="form-control" placeholder="30" value="30" oninput="calcularPrecioModal()" required>
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>

          <!-- Píldora de Cálculo en Tiempo Real -->
          <div class="card bg-primary-lt border-0 my-2">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
              <div>
                <div class="small text-muted fw-bold text-uppercase">Precio de Venta</div>
                <div class="small text-secondary">Tasa BCV: Bs. <?= number_format($tasaUsd, 2, ',', '.') ?></div>
              </div>
              <div class="text-end">
                <div class="fs-2 fw-bold text-primary" id="lbl_precio_usd">$0.00 USD</div>
                <div class="fw-bold text-success small" id="lbl_precio_bs">Bs. 0,00</div>
              </div>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-6 col-md-6 mb-3">
              <label class="form-label required small text-muted text-uppercase fw-bold">Stock Inicial</label>
              <input type="number" min="0" name="stock" id="prod_stock" class="form-control" value="10" required>
            </div>
            <div class="col-6 col-md-6 mb-3">
              <label class="form-label small text-muted text-uppercase fw-bold">Stock Mínimo</label>
              <input type="number" min="1" name="stock_minimo" id="prod_stock_minimo" class="form-control" value="5">
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary px-4">Guardar Producto</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- Modal Rápido para Crear Categoría desde Inventario -->
<div class="modal modal-blur fade" id="modal-rapido-categoria" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="<?= BASE_URL ?>/categoria/guardar" method="POST" autocomplete="off">
        
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v6h-6z" /><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M14 14h6v6h-6z" /></svg>
            Nueva Categoría Rápida
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label required small text-muted text-uppercase fw-bold">Nombre de la Categoría</label>
            <input type="text" name="nombre" id="cat_rapida_nombre" class="form-control" placeholder="Ej. Uñas & Acrílicos" required>
          </div>

          <div class="mb-3">
            <label class="form-label small text-muted text-uppercase fw-bold">Descripción (Opcional)</label>
            <textarea name="descripcion" id="cat_rapida_descripcion" class="form-control" rows="2" placeholder="Detalle rápido..."></textarea>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success px-4">Guardar Categoría</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- Modal Profesional de Confirmación de Eliminación -->
<div class="modal modal-blur fade" id="modal-eliminar" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-body text-center py-4">
        <span class="avatar avatar-lg bg-danger-lt text-danger rounded-circle mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.875h16.214a1.914 1.914 0 0 0 1.636 -2.875l-8.106 -13.534a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
        </span>
        <h3 class="fw-bold mb-1">¿Estás completamente seguro?</h3>
        <p class="text-muted small mb-3">Vas a eliminar del inventario el producto: <br><strong id="lbl_nombre_producto" class="text-dark fs-3"></strong></p>
        <div class="text-muted text-xxs mb-3">Esta acción no se puede deshacer.</div>
        
        <div class="d-flex gap-2 justify-content-center">
          <button type="button" class="btn btn-link link-secondary w-50" data-bs-dismiss="modal">Cancelar</button>
          <a id="btn_confirmar_eliminar" href="#" class="btn btn-danger w-50 shadow-sm">Sí, Eliminar</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const tasaBcv = <?= (float) $tasaUsd ?>;

  function calcularPrecioModal() {
    const costo = parseFloat(document.getElementById('prod_costo').value) || 0;
    const margen = parseFloat(document.getElementById('prod_margen').value) || 0;
    
    const precioUsd = costo + (costo * (margen / 100));
    const precioBs = precioUsd * tasaBcv;

    document.getElementById('lbl_precio_usd').innerText = `$${precioUsd.toFixed(2)} USD`;
    document.getElementById('lbl_precio_bs').innerText = `Bs. ${precioBs.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
  }

  function abrirModalNuevo() {
    document.getElementById('modal-titulo').innerText = 'Nuevo Producto';
    document.getElementById('prod_id').value = '';
    document.getElementById('prod_codigo').value = '';
    document.getElementById('prod_nombre').value = '';
    document.getElementById('prod_categoria_id').value = '';
    document.getElementById('prod_costo').value = '';
    document.getElementById('prod_margen').value = '30';
    document.getElementById('prod_stock').value = '10';
    document.getElementById('prod_stock_minimo').value = '5';
    calcularPrecioModal();
  }

  function abrirModalEditar(prod) {
    document.getElementById('modal-titulo').innerText = 'Editar Producto';
    document.getElementById('prod_id').value = prod.id;
    document.getElementById('prod_codigo').value = prod.codigo;
    document.getElementById('prod_nombre').value = prod.nombre;
    document.getElementById('prod_categoria_id').value = prod.categoria_id;
    document.getElementById('prod_costo').value = prod.costo_usd;
    document.getElementById('prod_margen').value = prod.margen_ganancia;
    document.getElementById('prod_stock').value = prod.stock;
    document.getElementById('prod_stock_minimo').value = prod.stock_minimo;
    calcularPrecioModal();
  }

  function limpiarModalCategoriaRapida() {
    document.getElementById('cat_rapida_nombre').value = '';
    document.getElementById('cat_rapida_descripcion').value = '';
  }

  function confirmarEliminacion(id, nombre) {
    document.getElementById('lbl_nombre_producto').innerText = nombre;
    const urlEliminar = `<?= BASE_URL ?>/inventario/eliminar/${id}`;
    document.getElementById('btn_confirmar_eliminar').href = urlEliminar;
    
    const modalEl = document.getElementById('modal-eliminar');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
  }
</script>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>