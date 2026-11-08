<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      
      <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-purple-lt text-purple font-weight-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-history" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8l0 4l2 2" /><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" /></svg>
          </span>
          <div>
            <h2 class="page-title mb-0 fs-2">Historial de Recepciones de Mercancía</h2>
            <div class="text-muted small">Registro y auditoría de todas las cargas de inventario realizadas</div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-auto ms-md-auto d-flex gap-2">
        <a href="/anacosmeticsy/public/inventario" class="btn btn-outline-secondary d-flex align-items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
          Volver al Inventario
        </a>
      </div>

    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">

    <?php if (!empty($recepciones)): ?>
      <div class="card shadow-sm border-0 rounded-3">
        <div class="table-responsive">
          <table class="table table-vcenter card-table table-hover">
            <thead>
              <tr>
                <th>ID Lote</th>
                <th>Fecha y Hora</th>
                <th>Usuario Responsable</th>
                <th>Observaciones / Lote</th>
                <th class="text-center">Ítems Diferentes</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recepciones as $rec): ?>
                <tr>
                  <td class="text-muted fw-bold">#<?= $rec['id'] ?></td>
                  <td>
                    <div class="fw-bold text-dark"><?= date('d/m/Y h:i A', strtotime($rec['created_at'])) ?></div>
                  </td>
                  <td>
                    <span class="badge bg-blue-lt"><?= htmlspecialchars($rec['usuario_nombre'] ?? 'Sistema') ?></span>
                  </td>
                  <td class="text-muted">
                    <?= !empty($rec['observaciones']) ? htmlspecialchars($rec['observaciones']) : '<em class="text-muted">Sin observaciones</em>' ?>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-purple-lt fw-bold px-2 py-1"><?= $rec['total_items'] ?> productos</span>
                  </td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" onclick="cargarDetallesRecepcion(<?= $rec['id'] ?>, '<?= date('d/m/Y h:i A', strtotime($rec['created_at'])) ?>')">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                      Ver Productos
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Paginador Tabler -->
        <?php if ($totalPaginas > 1): ?>
          <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted small">Mostrando página <strong><?= $paginaActual ?></strong> de <strong><?= $totalPaginas ?></strong> (Total de lotes: <?= $totalRegistros ?>)</p>
            <ul class="pagination m-0 ms-auto">
              <!-- Botón Anterior -->
              <li class="page-item <?= ($paginaActual <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="/anacosmeticsy/public/inventario/historial?pagina=<?= $paginaActual - 1 ?>" tabindex="-1" aria-disabled="true">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>
                  Anterior
                </a>
              </li>

              <!-- Páginas numéricas -->
              <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= ($paginaActual == $i) ? 'active' : '' ?>">
                  <a class="page-link" href="/anacosmeticsy/public/inventario/historial?pagina=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <!-- Botón Siguiente -->
              <li class="page-item <?= ($paginaActual >= $totalPaginas) ? 'disabled' : '' ?>">
                <a class="page-link" href="/anacosmeticsy/public/inventario/historial?pagina=<?= $paginaActual + 1 ?>">
                  Siguiente
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                </a>
              </li>
            </ul>
          </div>
        <?php endif; ?>

      </div>
    <?php else: ?>
      <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
          <h3 class="fw-bold text-secondary mb-1">No hay historial de recepciones</h3>
          <p class="small text-muted mb-3">Aún no se ha procesado ninguna carga de mercancía en el sistema.</p>
          <a href="/anacosmeticsy/public/inventario/actualizar" class="btn btn-success">Actualizar Inventario Ahora</a>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

<!-- Modal para Mostrar los Productos del Lote Seleccionado -->
<div class="modal modal-blur fade" id="modal-detalles-recepcion" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon text-purple" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5" /></svg>
          Detalle de Recepción de Mercancía - Lote <span id="lbl-lote-id" class="text-primary"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="p-3 bg-light-subtle border-bottom small text-muted">
          Fecha de carga: <strong id="lbl-lote-fecha" class="text-dark"></strong>
        </div>
        <div class="table-responsive">
          <table class="table table-vcenter card-table align-middle mb-0">
            <thead>
              <tr>
                <th>Código / Producto</th>
                <th class="text-center">Cant. Recibida</th>
                <th class="text-end">Costo Registrado</th>
                <th class="text-end">Precio Aplicado</th>
                <th class="text-center">¿Actualizó Precio?</th>
              </tr>
            </thead>
            <tbody id="contenedor-detalles-tabla">
              <!-- Se rellena vía JavaScript -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
  function cargarDetallesRecepcion(recepcionId, fecha) {
    document.getElementById('lbl-lote-id').innerText = `#${recepcionId}`;
    document.getElementById('lbl-lote-fecha').innerText = fecha;
    
    const tbody = document.getElementById('contenedor-detalles-tabla');
    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">Cargando detalles...</td></tr>`;

    const modalEl = document.getElementById('modal-detalles-recepcion');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    fetch(`/anacosmeticsy/public/inventario/verDetalleRecepcion/${recepcionId}`)
      .then(response => response.json())
      .then(data => {
        if (data.length === 0) {
          tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">No hay productos registrados en este lote.</td></tr>`;
          return;
        }

        let html = '';
        data.forEach(item => {
          const actualizoBadge = item.actualizo_precio == 1 
            ? '<span class="badge bg-green-lt fw-bold">Sí (Actualizado)</span>' 
            : '<span class="badge bg-secondary-lt">No (Mantuvo anterior)</span>';

          html += `
            <tr>
              <td>
                <div class="fw-bold text-dark">${item.producto_nombre}</div>
                <div class="small text-muted">SKU: <strong>${item.producto_codigo}</strong></div>
              </td>
              <td class="text-center fw-bold fs-3 text-purple">+${item.cantidad_recibida}</td>
              <td class="text-end fw-bold text-secondary">$${parseFloat(item.costo_unitario_registrado).toFixed(2)}</td>
              <td class="text-end fw-bold text-success">$${parseFloat(item.precio_venta_aplicado).toFixed(2)}</td>
              <td class="text-center">${actualizoBadge}</td>
            </tr>
          `;
        });
        tbody.innerHTML = html;
      })
      .catch(error => {
        console.error('Error al cargar los detalles:', error);
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Error al cargar los detalles del lote.</td></tr>`;
      });
  }
</script>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>