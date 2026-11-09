<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<!-- CDNs DataTables y SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-purple-lt text-purple font-weight-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
          </span>
          <div>
            <h2 class="page-title mb-0 fs-2">Gestión de Usuarios & Roles</h2>
            <div class="text-muted small">Control de personal autorizado con acceso al sistema</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-auto ms-md-auto d-flex gap-2">
        <a href="/anacosmeticsy/public/dashboard" class="btn btn-outline-secondary d-flex align-items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
          Volver
        </a>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modal-nuevo-usuario">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-plus" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M16 19h6" /><path d="M19 16v6" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /></svg>
          Nuevo Usuario
        </button>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-fluid px-3 px-lg-4">

    <?php if (isset($_SESSION['mensaje'])): ?>
      <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <div><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <div><?= htmlspecialchars($_SESSION['error']) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-body p-3">
        <div class="table-responsive">
          <table id="tabla-usuarios" class="table table-sm table-striped table-hover align-middle w-100">
            <thead class="table-light">
              <tr>
                <th># ID</th>
                <th>Nombre Completo</th>
                <th>Correo Electrónico</th>
                <th>Rol</th>
                <th>Fecha Registro</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usuarios as $u): ?>
                <tr>
                  <td class="fw-bold">#<?= str_pad($u['id'], 3, '0', STR_PAD_LEFT) ?></td>
                  <td class="fw-bold text-dark"><?= htmlspecialchars($u['nombre']) ?></td>
                  <td><?= htmlspecialchars($u['email']) ?></td>
                  <td>
                    <?php if ($u['rol'] === 'admin'): ?>
                      <span class="badge bg-red-lt fw-bold text-uppercase">Admin</span>
                    <?php else: ?>
                      <span class="badge bg-blue-lt fw-bold text-uppercase">Operador</span>
                    <?php endif; ?>
                  </td>
                  <td class="small text-muted"><?= date('d/m/Y h:i A', strtotime($u['created_at'])) ?></td>
                  <td class="text-center">
                    <div class="btn-list flex-nowrap justify-content-center">
                      <!-- Botón Editar -->
                      <button type="button" class="btn btn-icon btn-sm btn-outline-primary" title="Editar Usuario" onclick='abrirModalEditarUsuario(<?= json_encode($u) ?>)'>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                      </button>

                      <!-- Botón Eliminar (Evitar auto-eliminación) -->
                      <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger" title="Eliminar Usuario" onclick="confirmarEliminacionUsuario(<?= $u['id'] ?>, '<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>')">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                        </button>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- MODAL NUEVO USUARIO -->
<div class="modal modal-blur fade" id="modal-nuevo-usuario" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form id="form-nuevo-usuario" onsubmit="registrarUsuario(event)">
        <div class="modal-header bg-light py-2">
          <h5 class="modal-title fw-bold">Registrar Nuevo Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label required small fw-bold text-dark">Nombre Completo</label>
            <input type="text" id="nuevo-nombre" class="form-control" placeholder="Ej. María Pérez" required>
          </div>
          <div class="mb-3">
            <label class="form-label required small fw-bold text-dark">Correo Electrónico (Login)</label>
            <input type="email" id="nuevo-email" class="form-control" placeholder="usuario@anacosmetics.com" required>
          </div>
          <div class="mb-3">
            <label class="form-label required small fw-bold text-dark">Contraseña Temporal</label>
            <input type="password" id="nuevo-password" class="form-control" placeholder="••••••••" required>
          </div>
          <div class="mb-3">
            <label class="form-label required small fw-bold text-dark">Rol del Sistema</label>
            <select id="nuevo-rol" class="form-select">
              <option value="operador">Operador (Acceso Estándar)</option>
              <option value="admin">Administrador (Acceso Total)</option>
            </select>
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success px-4">Guardar Usuario</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDITAR USUARIO -->
<div class="modal modal-blur fade" id="modal-editar-usuario" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form id="form-editar-usuario" onsubmit="actualizarUsuario(event)">
        <input type="hidden" id="edit-id">
        <div class="modal-header bg-light py-2">
          <h5 class="modal-title fw-bold">Editar Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label required small fw-bold text-dark">Nombre Completo</label>
            <input type="text" id="edit-nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label required small fw-bold text-dark">Correo Electrónico (Login)</label>
            <input type="email" id="edit-email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-dark">Cambiar Contraseña (Opcional)</label>
            <input type="password" id="edit-password" class="form-control" placeholder="Dejar en blanco para mantener la contraseña actual">
          </div>
          <div class="mb-3">
            <label class="form-label required small fw-bold text-dark">Rol del Sistema</label>
            <select id="edit-rol" class="form-select">
              <option value="operador">Operador (Acceso Estándar)</option>
              <option value="admin">Administrador (Acceso Total)</option>
            </select>
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary px-4">Actualizar Usuario</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function() {
    $('#tabla-usuarios').DataTable({
      language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
      order: [[0, 'desc']],
      pageLength: 10
    });
  });

  function registrarUsuario(e) {
    e.preventDefault();
    const payload = {
      nombre: document.getElementById('nuevo-nombre').value.trim(),
      email: document.getElementById('nuevo-email').value.trim(),
      password: document.getElementById('nuevo-password').value,
      rol: document.getElementById('nuevo-rol').value
    };

    fetch('/anacosmeticsy/public/usuarios/guardar', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        return;
      }
      Swal.fire({
        icon: 'success',
        title: '¡Registrado!',
        text: data.message,
        timer: 1500,
        showConfirmButton: false
      }).then(() => {
        location.reload();
      });
    })
    .catch(err => {
      Swal.fire({ icon: 'error', title: 'Error de Red', text: 'No se pudo procesar la solicitud.' });
    });
  }

  function abrirModalEditarUsuario(u) {
    document.getElementById('edit-id').value = u.id;
    document.getElementById('edit-nombre').value = u.nombre;
    document.getElementById('edit-email').value = u.email;
    document.getElementById('edit-rol').value = u.rol;
    document.getElementById('edit-password').value = '';

    const modal = new bootstrap.Modal(document.getElementById('modal-editar-usuario'));
    modal.show();
  }

  function actualizarUsuario(e) {
    e.preventDefault();
    const payload = {
      id: document.getElementById('edit-id').value,
      nombre: document.getElementById('edit-nombre').value.trim(),
      email: document.getElementById('edit-email').value.trim(),
      rol: document.getElementById('edit-rol').value,
      password: document.getElementById('edit-password').value.trim()
    };

    fetch('/anacosmeticsy/public/usuarios/editar', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        return;
      }
      Swal.fire({
        icon: 'success',
        title: '¡Actualizado!',
        text: data.message,
        timer: 1500,
        showConfirmButton: false
      }).then(() => {
        location.reload();
      });
    })
    .catch(err => {
      Swal.fire({ icon: 'error', title: 'Error de Red', text: 'No se pudo comunicar con el servidor.' });
    });
  }

  function confirmarEliminacionUsuario(id, nombre) {
    Swal.fire({
      title: `¿Eliminar a ${nombre}?`,
      text: 'Esta acción borrará el acceso del usuario al sistema de forma definitiva.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d63939',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, Eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = `/anacosmeticsy/public/usuarios/eliminar/${id}`;
      }
    });
  }
</script>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>