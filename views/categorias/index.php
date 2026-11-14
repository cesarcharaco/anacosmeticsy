<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<!-- Header tipo App Bar -->
<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-blue-lt text-blue font-weight-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-category" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v6h-6z" /><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M14 14h6v6h-6z" /></svg>
          </span>
          <div>
            <h2 class="page-title mb-0 fs-2">Gestión de Categorías</h2>
            <div class="text-muted small">Administra los departamentos o clasificaciones del catálogo.</div>
          </div>
        </div>
      </div>
      
      <div class="col-12 col-md-auto ms-md-auto d-flex gap-2">
        <a href="<?= BASE_URL ?>/inventario" class="btn btn-outline-secondary flex-fill flex-md-grow-0">
          &larr; Ir a Inventario
        </a>
        <button class="btn btn-primary flex-fill flex-md-grow-0 d-flex align-items-center justify-content-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-categoria" onclick="abrirModalNueva()">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
          Nueva Categoría
        </button>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">

    <!-- Notificaciones -->
    <?php if (isset($_SESSION['mensaje'])): ?>
      <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <div><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php if (!empty($categorias)): ?>

      <!-- Vista Móvil (Tarjetas) -->
      <div class="d-block d-md-none">
        <div class="row row-cards">
          <?php foreach ($categorias as $cat): ?>
            <div class="col-12">
              <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-3">
                  <h4 class="card-title fw-bold text-dark mb-1"><?= htmlspecialchars($cat['nombre']) ?></h4>
                  <p class="text-muted small mb-3"><?= htmlspecialchars($cat['descripcion'] ?? 'Sin descripción') ?></p>
                  <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-categoria" onclick='abrirModalEditar(<?= json_encode($cat) ?>)'>Editar</button>
                    <a href="<?= BASE_URL ?>/categorias/eliminar/<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta categoría?')">Eliminar</a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Vista Escritorio (Tabla) -->
      <div class="card shadow-sm border-0 rounded-3 d-none d-md-block">
        <div class="table-responsive">
          <table class="table table-vcenter card-table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre de la Categoría</th>
                <th>Descripción</th>
                <th>Fecha de Creación</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categorias as $cat): ?>
                <tr>
                  <td class="text-muted fw-bold">#<?= $cat['id'] ?></td>
                  <td class="fw-bold text-dark"><?= htmlspecialchars($cat['nombre']) ?></td>
                  <td class="text-muted"><?= htmlspecialchars($cat['descripcion'] ?? '—') ?></td>
                  <td class="text-muted small"><?= htmlspecialchars($cat['created_at']) ?></td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-icon btn-ghost-primary me-1" title="Editar" data-bs-toggle="modal" data-bs-target="#modal-categoria" onclick='abrirModalEditar(<?= json_encode($cat) ?>)'>
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                    </button>
                    <a href="<?= BASE_URL ?>/categorias/eliminar/<?= $cat['id'] ?>" class="btn btn-sm btn-icon btn-ghost-danger" title="Eliminar" onclick="return confirm('¿Deseas eliminar esta categoría?')">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php else: ?>
      <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
          <h3 class="fw-bold text-secondary mb-1">No hay categorías registradas</h3>
          <p class="small text-muted mb-3">Crea la primera categoría para clasificar tus productos.</p>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-categoria" onclick="abrirModalNueva()">+ Registrar Categoría</button>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

<!-- Modal Tabler para Crear / Editar Categoría -->
<div class="modal modal-blur fade" id="modal-categoria" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="<?= BASE_URL ?>/categorias/guardar" method="POST" autocomplete="off">
        
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-dark" id="modal-titulo">Nueva Categoría</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">
          <input type="hidden" name="id" id="cat_id">
          
          <div class="mb-3">
            <label class="form-label required small text-muted text-uppercase fw-bold">Nombre de la Categoría</label>
            <input type="text" name="nombre" id="cat_nombre" class="form-control" placeholder="Ej. Cuidado Capilar" required>
          </div>

          <div class="mb-3">
            <label class="form-label small text-muted text-uppercase fw-bold">Descripción (Opcional)</label>
            <textarea name="descripcion" id="cat_descripcion" class="form-control" rows="3" placeholder="Breve detalle de la categoría..."></textarea>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary px-4">Guardar Categoría</button>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
  function abrirModalNueva() {
    document.getElementById('modal-titulo').innerText = 'Nueva Categoría';
    document.getElementById('cat_id').value = '';
    document.getElementById('cat_nombre').value = '';
    document.getElementById('cat_descripcion').value = '';
  }

  function abrirModalEditar(cat) {
    document.getElementById('modal-titulo').innerText = 'Editar Categoría';
    document.getElementById('cat_id').value = cat.id;
    document.getElementById('cat_nombre').value = cat.nombre;
    document.getElementById('cat_descripcion').value = cat.descripcion || '';
  }
</script>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>