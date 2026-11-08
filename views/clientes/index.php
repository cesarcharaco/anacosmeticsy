<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<!-- Header tipo App Bar -->
<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">
      
      <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-blue-lt text-blue font-weight-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
          </span>
          <div>
            <h2 class="page-title mb-0 fs-2">Directorio de Clientes</h2>
            <div class="text-muted small">Gestión de datos de contacto, saldos a favor e historial</div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-auto ms-md-auto d-flex gap-2">
        <button class="btn btn-blue d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-nuevo-cliente">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-plus" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4c.348 0 .686 .045 1 .128" /><path d="M16 19h6" /><path d="M19 16v6" /></svg>
          Nuevo Cliente
        </button>
        <a href="/anacosmeticsy/public/dashboard" class="btn btn-outline-secondary d-flex align-items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
          Volver
        </a>
      </div>

    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-fluid px-3 px-lg-4">
    
    <!-- Mensajes de Notificación y Alertas -->
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

    <div class="card shadow-sm border-0 rounded-3">
      
      <!-- Buscador -->
      <div class="card-body border-bottom bg-light-subtle py-2">
        <div class="row align-items-center">
          <div class="col-12 col-md-4">
            <div class="input-icon">
              <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
              </span>
              <input type="text" id="buscador-clientes" class="form-control" placeholder="Buscar por nombre o teléfono..." onkeyup="filtrarClientes()">
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de Clientes -->
      <div class="table-responsive">
        <table class="table table-vcenter card-table align-middle" id="tabla-clientes">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Contacto</th>
              <th>Dirección</th>
              <th class="text-end">Saldo a Favor</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($clientes)): ?>
              <?php foreach ($clientes as $c): ?>
                <tr class="fila-cliente">
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <span class="avatar avatar-xs rounded-circle bg-blue-lt text-blue fw-bold">
                        <?= strtoupper(substr($c['nombre'], 0, 2)) ?>
                      </span>
                      <div class="fw-bold text-dark nombre-cliente"><?= htmlspecialchars($c['nombre']) ?></div>
                    </div>
                  </td>
                  <td>
                    <div class="text-muted small telefono-cliente">
                      📞 <?= !empty($c['telefono']) ? htmlspecialchars($c['telefono']) : 'Sin teléfono' ?>
                    </div>
                  </td>
                  <td>
                    <div class="small text-muted text-truncate" style="max-width: 250px;">
                      <?= !empty($c['direccion']) ? htmlspecialchars($c['direccion']) : '---' ?>
                    </div>
                  </td>
                  <td class="text-end">
                    <?php if ($c['saldo_favor_usd'] > 0): ?>
                      <span class="badge bg-success-lt fw-bold">$<?= number_format($c['saldo_favor_usd'], 2) ?> USD</span>
                    <?php else: ?>
                      <span class="text-muted small">$0.00</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end">
                    <div class="d-flex justify-content-end gap-1">
                      <button class="btn btn-icon btn-sm btn-outline-primary" title="Editar" onclick='abrirModalEditarCliente(<?= json_encode($c) ?>)'>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                      </button>
                      <button class="btn btn-icon btn-sm btn-outline-danger" title="Eliminar" onclick="abrirModalEliminarCliente(<?= $c['id'] ?>, '<?= htmlspecialchars($c['nombre'], ENT_QUOTES) ?>')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">No hay clientes registrados en el directorio.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Paginación de la Tabla (CORRECTAMENTE UBICADA FUERA DEL TABLE-RESPONSIVE) -->
      <?php if (isset($totalPaginas) && $totalPaginas > 1): ?>
        <div class="card-footer d-flex align-items-center justify-content-between bg-light py-2">
          <p class="m-0 text-muted small">
            Mostrando página <strong><?= $paginaActual ?></strong> de <strong><?= $totalPaginas ?></strong> (Total: <?= $totalClientes ?> clientes)
          </p>
          <ul class="pagination m-0 ms-auto">
            <!-- Botón Anterior -->
            <li class="page-item <?= ($paginaActual <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="/anacosmeticsy/public/clientes?p=<?= $paginaActual - 1 ?>" tabindex="-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>
                Anterior
              </a>
            </li>

            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
              <li class="page-item <?= ($paginaActual == $i) ? 'active' : '' ?>">
                <a class="page-link" href="/anacosmeticsy/public/clientes?p=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>

            <!-- Botón Siguiente -->
            <li class="page-item <?= ($paginaActual >= $totalPaginas) ? 'disabled' : '' ?>">
              <a class="page-link" href="/anacosmeticsy/public/clientes?p=<?= $paginaActual + 1 ?>">
                Siguiente
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
              </a>
            </li>
          </ul>
        </div>
      <?php endif; ?>

    </div>

  </div>
</div>

<!-- Modal Nuevo Cliente -->
<div class="modal modal-blur fade" id="modal-nuevo-cliente" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="/anacosmeticsy/public/clientes/guardar" method="POST">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M16 19h6" /><path d="M19 16v6" /></svg>
            Registrar Nuevo Cliente
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold required">Nombre Completo</label>
            <input type="text" class="form-control" name="nombre" placeholder="Ej. María Pérez" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Número de Teléfono</label>
            <input type="text" class="form-control" name="telefono" placeholder="Ej. 0414-1234567">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Dirección</label>
            <textarea class="form-control" name="direccion" rows="2" placeholder="Ej. Av. Principal..."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Saldo a Favor Inicial ($ USD)</label>
            <input type="number" step="0.01" min="0" class="form-control" name="saldo_favor_usd" value="0.00">
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-blue px-4">Guardar Cliente</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Editar Cliente -->
<div class="modal modal-blur fade" id="modal-editar-cliente" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="/anacosmeticsy/public/clientes/actualizar" method="POST">
        <input type="hidden" name="id" id="edit-cli-id">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
            Editar Cliente
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold required">Nombre Completo</label>
            <input type="text" class="form-control" name="nombre" id="edit-cli-nombre" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Número de Teléfono</label>
            <input type="text" class="form-control" name="telefono" id="edit-cli-telefono">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Dirección</label>
            <textarea class="form-control" name="direccion" id="edit-cli-direccion" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Saldo a Favor ($ USD)</label>
            <input type="number" step="0.01" min="0" class="form-control" name="saldo_favor_usd" id="edit-cli-saldo">
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary px-4">Actualizar Cliente</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Eliminar Cliente -->
<div class="modal modal-blur fade" id="modal-eliminar-cliente" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-body text-center py-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5.07 19h13.86a1.5 1.5 0 0 0 1.3 -2.25l-6.93 -12a1.5 1.5 0 0 0 -2.6 0l-6.93 12a1.5 1.5 0 0 0 1.3 2.25z" /></svg>
        <h3>¿Eliminar cliente?</h3>
        <p class="text-muted">Vas a eliminar a: <strong id="lbl-eliminar-cli-nombre" class="text-dark"></strong>.</p>
      </div>
      <div class="modal-footer bg-light py-2">
        <div class="w-100">
          <div class="row">
            <div class="col">
              <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Cancelar</button>
            </div>
            <div class="col">
              <a id="btn-confirmar-eliminar-cli" href="#" class="btn btn-danger w-100">Eliminar</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function filtrarClientes() {
    const query = document.getElementById('buscador-clientes').value.toLowerCase();
    const filas = document.querySelectorAll('.fila-cliente');

    filas.forEach(fila => {
      const nombre = fila.querySelector('.nombre-cliente').innerText.toLowerCase();
      const telefono = fila.querySelector('.telefono-cliente').innerText.toLowerCase();

      if (nombre.includes(query) || telefono.includes(query)) {
        fila.style.display = '';
      } else {
        fila.style.display = 'none';
      }
    });
  }

  function abrirModalEditarCliente(cli) {
    document.getElementById('edit-cli-id').value = cli.id;
    document.getElementById('edit-cli-nombre').value = cli.nombre;
    document.getElementById('edit-cli-telefono').value = cli.telefono || '';
    document.getElementById('edit-cli-direccion').value = cli.direccion || '';
    document.getElementById('edit-cli-saldo').value = cli.saldo_favor_usd;

    const modal = new bootstrap.Modal(document.getElementById('modal-editar-cliente'));
    modal.show();
  }

  function abrirModalEliminarCliente(id, nombre) {
    document.getElementById('lbl-eliminar-cli-nombre').innerText = nombre;
    document.getElementById('btn-confirmar-eliminar-cli').href = `/anacosmeticsy/public/clientes/eliminar/${id}`;

    const modal = new bootstrap.Modal(document.getElementById('modal-eliminar-cliente'));
    modal.show();
  }
</script>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>