<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<!-- Header tipo App -->
<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Configuración & Tasa BCV</h2>
        <div class="text-muted text-secondary small">Gestión de parámetros del sistema y sincronización cambiaria.</div>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <a href="/anacosmeticsy/public/dashboard" class="btn btn-outline-secondary">
          &larr; Volver al Panel
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">

    <!-- Mensajes de éxito o alerta -->
    <?php if (isset($_SESSION['mensaje'])): ?>
      <div class="alert alert-success alert-dismissible" role="alert">
        <div><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
      </div>
      <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <div class="row row-cards">
      
      <!-- Tarjeta de Tasa BCV en Vivo -->
      <div class="col-md-6">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4 text-center">
            <span class="avatar avatar-xl rounded-circle bg-green-lt mb-3 mx-auto">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4h-6" /><path d="M12 3v3m0 12v3" /></svg>
            </span>
            <h3 class="card-title mb-1">Tasa Oficial BCV (USD)</h3>
            <div class="display-5 fw-bold text-success my-3">
              Bs. <?= number_format($tasaUsd, 4, ',', '.') ?>
            </div>
            <p class="text-muted small mb-4">La tasa se actualiza automáticamente cada 6 horas o puedes forzar la sincronización manual aquí mismo.</p>
            
            <form action="/anacosmeticsy/public/configuracion/actualizarTasa" method="POST">
              <button type="submit" class="btn btn-success w-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                Actualizar Tasa BCV Ahora
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Tarjeta de Historial de Tasas -->
      <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-header">
            <h3 class="card-title">Historial Reciente de Tasas</h3>
          </div>
          <div class="table-responsive">
            <table class="table table-vcenter card-table">
              <thead>
                <tr>
                  <th>Moneda</th>
                  <th>Tasa</th>
                  <th>Fecha BCV</th>
                  <th>Sincronizado</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($historial)): ?>
                  <?php foreach ($historial as $h): ?>
                    <tr>
                      <td><span class="badge bg-blue-lt"><?= htmlspecialchars($h['currency']) ?></span></td>
                      <td class="fw-bold">Bs. <?= number_format($h['rate'], 4, ',', '.') ?></td>
                      <td><?= htmlspecialchars($h['rate_date']) ?></td>
                      <td class="text-muted small"><?= htmlspecialchars($h['fetched_at']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="text-center text-muted py-3">No hay registros históricos aún.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>