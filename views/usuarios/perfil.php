<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-blue-lt text-blue font-weight-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 2.807 .593" /><path d="M16 19l2 2l4 -4" /></svg>
          </span>
          <div>
            <h2 class="page-title mb-0 fs-2">Mi Perfil & Configuración</h2>
            <div class="text-muted small">Actualización individual de credenciales de acceso y datos personales</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-auto ms-md-auto">
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary d-flex align-items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
          Volver al Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        
        <div class="card shadow-sm border-0 rounded-3">
          <div class="card-header bg-light py-3">
            <h3 class="card-title fw-bold m-0 text-dark">Información de la Cuenta</h3>
          </div>
          <div class="card-body p-4">
            
            <!-- CAMPO: Nombre completo -->
            <div class="mb-4">
              <label class="form-label small fw-bold text-muted text-uppercase mb-1">Nombre Completo</label>
              <div class="input-group">
                <input type="text" id="input-nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" readonly>
                <button type="button" class="btn btn-outline-primary" id="btn-edit-nombre" onclick="habilitarEdicion('nombre')" title="Editar Nombre">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil me-0" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                </button>
                <button type="button" class="btn btn-success d-none" id="btn-save-nombre" onclick="guardarCampoIndividual('nombre')">
                  Guardar
                </button>
              </div>
            </div>

            <!-- CAMPO: Correo Electrónico -->
            <div class="mb-4">
              <label class="form-label small fw-bold text-muted text-uppercase mb-1">Correo Electrónico (Login)</label>
              <div class="input-group">
                <input type="email" id="input-email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" readonly>
                <button type="button" class="btn btn-outline-primary" id="btn-edit-email" onclick="habilitarEdicion('email')" title="Editar Email">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil me-0" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                </button>
                <button type="button" class="btn btn-success d-none" id="btn-save-email" onclick="guardarCampoIndividual('email')">
                  Guardar
                </button>
              </div>
            </div>

            <!-- CAMPO: Contraseña -->
            <div class="mb-3">
              <label class="form-label small fw-bold text-muted text-uppercase mb-1">Nueva Contraseña</label>
              <div class="input-group">
                <input type="password" id="input-password" class="form-control" placeholder="••••••••••••" readonly>
                <button type="button" class="btn btn-outline-secondary" id="btn-toggle-eye" onclick="togglePasswordVisibility()" title="Mostrar/Ocultar">
                  <svg id="icon-eye-open" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye me-0" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                  <svg id="icon-eye-closed" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye-off me-0 d-none" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.584 10.587a2 2 0 0 0 2.828 2.83" /><path d="M9.363 5.365a9.466 9.466 0 0 1 2.637 -.365c3.6 0 6.6 2 9 6c-.734 1.223 -1.62 2.24 -2.637 3.036m-3.363 1.964a9.466 9.466 0 0 1 -3 0c-3.6 0 -6.6 -2 -9 -6a15.748 15.748 0 0 1 3.256 -4.015" /><path d="M3 3l18 18" /></svg>
                </button>
                <button type="button" class="btn btn-outline-primary" id="btn-edit-password" onclick="habilitarEdicion('password')" title="Editar Contraseña">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil me-0" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                </button>
                <button type="button" class="btn btn-success d-none" id="btn-save-password" onclick="guardarCampoIndividual('password')">
                  Guardar
                </button>
              </div>
              <div class="form-text small text-muted">Deje este campo bloqueado si no desea modificar su contraseña de acceso.</div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  function habilitarEdicion(campo) {
    const input = document.getElementById(`input-${campo}`);
    const btnEdit = document.getElementById(`btn-edit-${campo}`);
    const btnSave = document.getElementById(`btn-save-${campo}`);

    input.removeAttribute('readonly');
    if (campo === 'password') {
      input.value = '';
      input.focus();
    } else {
      input.focus();
      input.select();
    }

    btnEdit.classList.add('d-none');
    btnSave.classList.remove('d-none');
  }

  function togglePasswordVisibility() {
    const input = document.getElementById('input-password');
    const eyeOpen = document.getElementById('icon-eye-open');
    const eyeClosed = document.getElementById('icon-eye-closed');

    if (input.type === 'password') {
      input.type = 'text';
      eyeOpen.classList.add('d-none');
      eyeClosed.classList.remove('d-none');
    } else {
      input.type = 'password';
      eyeOpen.classList.remove('d-none');
      eyeClosed.classList.add('d-none');
    }
  }

  function guardarCampoIndividual(campo) {
    const input = document.getElementById(`input-${campo}`);
    const valor = input.value.trim();

    if (!valor && campo !== 'password') {
      Swal.fire({ icon: 'warning', title: 'Campo requerido', text: 'El campo no puede quedar vacío.' });
      return;
    }

    if (campo === 'password' && valor.length < 4) {
      Swal.fire({ icon: 'warning', title: 'Contraseña débil', text: 'La contraseña debe tener al menos 4 caracteres.' });
      return;
    }

    const payload = {};
    payload[campo] = valor;

    fetch('<?= BASE_URL ?>/usuarios/actualizarPerfilCampo', {
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
      Swal.fire({ icon: 'error', title: 'Error de Red', text: 'No se pudo procesar la solicitud.' });
    });
  }
</script>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>