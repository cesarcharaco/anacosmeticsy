<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<!-- CDNs de DataTables y SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-orange-lt text-orange font-weight-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.871l-8.106 -13.534a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
          </span>
          <div>
            <h2 class="page-title mb-0 fs-2">Control de Mermas y Salidas Extraordinarias</h2>
            <div class="text-muted small">Registro de daños, vencimientos, donaciones, consumo interno y salidas de inventario</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-auto ms-md-auto d-flex gap-2">
        <a href="/anacosmeticsy/public/inventario" class="btn btn-outline-secondary d-flex align-items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
          Volver a Inventario
        </a>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-1" onclick="abrirModalRegistrarMerma()">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
          Registrar Nueva Salida / Merma
        </button>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-fluid px-3 px-lg-4">
    
    <!-- TABLA DE HISTORIAL DE MERMAS -->
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-body p-3">
        <div class="table-responsive">
          <table id="tabla-historial-mermas" class="table table-sm table-striped table-hover align-middle w-100">
            <thead class="table-light">
              <tr>
                <th># ID</th>
                <th>Fecha / Hora</th>
                <th>Motivo / Clasificación</th>
                <th>Estado</th>
                <th>Responsable</th>
                <th class="text-center">Ítems</th>
                <th class="text-end">Valor Total Costo ($)</th>
                <th>Observaciones</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($mermas as $m): ?>
                <?php 
                  $esAnulada = (isset($m['estado']) && $m['estado'] === 'anulada');
                ?>
                <tr class="<?= $esAnulada ? 'table-danger opacity-75' : '' ?>">
                  <td class="fw-bold">#<?= str_pad($m['id'], 5, '0', STR_PAD_LEFT) ?></td>
                  <td class="small"><?= date('d/m/Y h:i A', strtotime($m['created_at'])) ?></td>
                  <td>
                    <?php 
                      $badgeClass = 'bg-secondary-lt';
                      $motivoTexto = ucfirst(str_replace('_', ' ', $m['motivo']));
                      if ($m['motivo'] === 'dañado' || $m['motivo'] === 'defectuoso_fabrica' || $m['motivo'] === 'extravio') $badgeClass = 'bg-danger-lt';
                      elseif ($m['motivo'] === 'vencido') $badgeClass = 'bg-warning-lt';
                      elseif ($m['motivo'] === 'donacion_regalo') $badgeClass = 'bg-success-lt';
                      elseif ($m['motivo'] === 'consumo_interno') $badgeClass = 'bg-blue-lt';
                    ?>
                    <span class="badge <?= $badgeClass ?> fw-bold"><?= $motivoTexto ?></span>
                  </td>
                  <td>
                    <?php if ($esAnulada): ?>
                      <span class="badge bg-danger text-white fw-bold" title="Motivo: <?= htmlspecialchars($m['motivo_anulacion'] ?? 'N/A') ?>">Anulada</span>
                    <?php else: ?>
                      <span class="badge bg-success text-white fw-bold">Completada</span>
                    <?php endif; ?>
                  </td>
                  <td class="small"><?= htmlspecialchars($m['usuario_nombre']) ?></td>
                  <td class="text-center"><span class="badge bg-light text-dark border"><?= $m['total_items'] ?></span></td>
                  <td class="text-end fw-bold text-dark <?= $esAnulada ? 'text-decoration-line-through' : '' ?>">
                    $<?= number_format($m['total_perdida_usd'], 2) ?>
                  </td>
                  <td class="small text-muted text-truncate" style="max-width: 180px;" title="<?= htmlspecialchars($m['observaciones'] ?? '') ?>">
                    <?= htmlspecialchars($m['observaciones'] ?? 'Sin observaciones') ?>
                  </td>
                  <td class="text-center">
                    <div class="btn-list flex-nowrap justify-content-center">
                      <button type="button" class="btn btn-icon btn-sm btn-outline-info" title="Ver Detalles" onclick="verDetallesMerma(<?= $m['id'] ?>)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                      </button>

                      <?php if (!$esAnulada): ?>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger" title="Anular Salida" onclick="abrirModalAnularMerma(<?= $m['id'] ?>, '<?= str_pad($m['id'], 5, '0', STR_PAD_LEFT) ?>')">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-x" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>
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

<!-- MODAL REGISTRAR NUEVA MERMA / SALIDA EXTRAORDINARIA -->
<div class="modal modal-blur fade" id="modal-registrar-merma" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-light py-2">
        <h5 class="modal-title fw-bold">Registrar Salida Extraordinaria de Inventario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        
        <!-- Mensaje permanente de advertencia para prevenir errores humanos -->
        <div class="alert alert-warning p-2 small mb-3 border-0 shadow-sm d-flex align-items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon text-warning flex-shrink-0" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5.07 19h13.86a1.5 1.5 0 0 0 1.3 -2.25l-6.93 -12a1.5 1.5 0 0 0 -2.6 0l-6.93 12a1.5 1.5 0 0 0 1.3 2.25z" /></svg>
          <div>
            <strong>¡Atención!</strong> Esta acción descontará stock físico de forma inmediata y el registro será inmutable por seguridad contable. Verifique bien las cantidades antes de guardar.
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-12 col-md-6">
            <label class="form-label small fw-bold text-dark">Motivo de la Salida <span class="text-danger">*</span></label>
            <select id="select-motivo-merma" class="form-select form-select-sm" onchange="actualizarEtiquetaTotal()">
              <option value="dañado">Dañado / Roto (Merma)</option>
              <option value="vencido">Vencido / Caducado (Merma)</option>
              <option value="defectuoso_fabrica">Defectuoso de Fábrica (Merma)</option>
              <option value="extravio">Extravío / Faltante (Pérdida)</option>
              <option value="donacion_regalo">Donación / Regalo / Promoción (Cortesía)</option>
              <option value="consumo_interno">Consumo Interno / Uso en Salón (Inversión Operativa)</option>
            </select>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label small fw-bold text-dark">Seleccionar Producto del Inventario</label>
            <select id="select-producto-catalogo" class="form-select form-select-sm" onchange="agregarProductoMerma(this)">
              <option value="">-- Seleccionar producto para agregar --</option>
              <?php foreach ($productos as $prod): ?>
                <option value="<?= $prod['id'] ?>" data-nombre="<?= htmlspecialchars($prod['nombre']) ?>" data-stock="<?= $prod['stock'] ?>" data-costo="<?= $prod['costo_usd'] ?>">
                  <?= htmlspecialchars($prod['nombre']) ?> (Stock: <?= $prod['stock'] ?> | Costo U: $<?= number_format($prod['costo_usd'], 2) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Tabla de Ítems Agregados -->
        <div class="mb-3">
          <label class="form-label small fw-bold text-dark mb-1">Ítems a Retirar del Inventario</label>
          <div class="table-responsive border rounded bg-light-subtle" style="max-height: 220px; overflow-y: auto;">
            <table class="table table-sm table-vcenter card-table align-middle mb-0">
              <thead>
                <tr class="bg-light">
                  <th>Producto</th>
                  <th class="text-center" style="width: 90px;">Stock Disp.</th>
                  <th class="text-center" style="width: 100px;">Cantidad</th>
                  <th class="text-end" style="width: 100px;">Costo U.</th>
                  <th class="text-end" style="width: 100px;">Subtotal</th>
                  <th class="text-center" style="width: 40px;"></th>
                </tr>
              </thead>
              <tbody id="tbody-items-merma">
                <tr>
                  <td colspan="6" class="text-center py-3 text-muted small">No hay productos agregados en esta salida.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold text-dark">Observaciones o Justificación</label>
          <textarea id="txt-observaciones-merma" class="form-control form-control-sm" rows="2" placeholder="Detalles adicionales, número de autorización, etc..."></textarea>
        </div>

        <div class="card bg-light border-0 p-2 rounded-3 text-end">
          <span class="text-muted small" id="lbl-titulo-total-dinamico">Valor Total a Costo:</span>
          <span class="fs-3 fw-extrabold text-dark" id="lbl-total-perdida-modal">$0.00</span>
        </div>

      </div>
      <div class="modal-footer bg-light py-2">
        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success px-4" onclick="confirmarGuardarMerma()">Guardar Salida</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL CONFIRMAR ANULACIÓN DE SALIDA -->
<div class="modal modal-blur fade" id="modal-anular-merma" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form onsubmit="procesarAnulacionMerma(event)">
        <input type="hidden" id="input-anular-merma-id">
        <div class="modal-body text-center py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5.07 19h13.86a1.5 1.5 0 0 0 1.3 -2.25l-6.93 -12a1.5 1.5 0 0 0 -2.6 0l-6.93 12a1.5 1.5 0 0 0 1.3 2.25z" /></svg>
          <h3 class="mb-1">¿Anular Salida <span id="txt-num-salida-anular">#00000</span>?</h3>
          <p class="text-muted small mb-3">Esta acción devolverá automáticamente el stock de los productos al inventario.</p>
          
          <div class="text-start mb-2">
            <label class="form-label small fw-bold text-dark mb-1">Motivo de Anulación <span class="text-danger">*</span></label>
            <textarea id="txt-motivo-anulacion" class="form-control form-control-sm" rows="3" placeholder="Explique por qué anula este registro..." required></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <div class="w-100">
            <div class="row g-2">
              <div class="col-6">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Cancelar</button>
              </div>
              <div class="col-6">
                <button type="submit" class="btn btn-danger w-100">Sí, Anular</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL VER DETALLES DE MERMA -->
<div class="modal modal-blur fade" id="modal-detalle-merma" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-light py-2">
        <h5 class="modal-title fw-bold" id="lbl-detalle-titulo">Detalle de Salida</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3" id="modal-detalle-body">
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status"></div>
          <div class="text-muted small mt-2">Cargando detalles...</div>
        </div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPTS Y LÓGICA FRONTEND -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
  let itemsMerma = [];

  $(document).ready(function() {
    $('#tabla-historial-mermas').DataTable({
      language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
      },
      order: [[0, 'desc']],
      pageLength: 10
    });
  });

  function abrirModalRegistrarMerma() {
    itemsMerma = [];
    document.getElementById('txt-observaciones-merma').value = '';
    document.getElementById('select-motivo-merma').value = 'dañado';
    document.getElementById('select-producto-catalogo').value = '';
    actualizarEtiquetaTotal();
    renderizarItemsMerma();
    
    const modal = new bootstrap.Modal(document.getElementById('modal-registrar-merma'));
    modal.show();
  }

  function actualizarEtiquetaTotal() {
    const motivo = document.getElementById('select-motivo-merma').value;
    const lblTitulo = document.getElementById('lbl-titulo-total-dinamico');
    const lblMonto = document.getElementById('lbl-total-perdida-modal');

    if (motivo === 'dañado' || motivo === 'vencido' || motivo === 'defectuoso_fabrica' || motivo === 'extravio') {
      lblTitulo.innerText = 'Valor Total de la Merma / Pérdida (Costo):';
      lblMonto.className = 'fs-3 fw-extrabold text-danger';
    } else if (motivo === 'donacion_regalo') {
      lblTitulo.innerText = 'Valor Total de la Promoción / Cortesía (Costo):';
      lblMonto.className = 'fs-3 fw-extrabold text-success';
    } else if (motivo === 'consumo_interno') {
      lblTitulo.innerText = 'Valor Total del Insumo / Inversión Operativa (Costo):';
      lblMonto.className = 'fs-3 fw-extrabold text-primary';
    }
  }

  function agregarProductoMerma(select) {
    const option = select.options[select.selectedIndex];
    if (!option.value) return;

    const productoId = parseInt(option.value);
    const nombre = option.getAttribute('data-nombre');
    const stockMax = parseInt(option.getAttribute('data-stock'));
    const costo = parseFloat(option.getAttribute('data-costo')) || 0;

    if (stockMax <= 0) {
      Swal.fire({ icon: 'warning', title: 'Sin Stock', text: 'Este producto no posee stock disponible para retirar.' });
      select.value = '';
      return;
    }

    const existeIndex = itemsMerma.findIndex(i => i.producto_id === productoId);
    if (existeIndex !== -1) {
      if (itemsMerma[existeIndex].cantidad < stockMax) {
        itemsMerma[existeIndex].cantidad++;
      } else {
        Swal.fire({ icon: 'warning', title: 'Límite de Stock', text: 'Ha alcanzado el stock máximo disponible para este producto.' });
      }
    } else {
      itemsMerma.push({
        producto_id: productoId,
        nombre: nombre,
        stock_max: stockMax,
        costo_unitario: costo,
        cantidad: 1
      });
    }

    select.value = '';
    renderizarItemsMerma();
  }

  function cambiarCantidadMerma(index, delta) {
    itemsMerma[index].cantidad += delta;
    if (itemsMerma[index].cantidad <= 0) {
      itemsMerma.splice(index, 1);
    } else if (itemsMerma[index].cantidad > itemsMerma[index].stock_max) {
      Swal.fire({ icon: 'warning', title: 'Stock Insuficiente', text: 'La cantidad supera el stock físico actual.' });
      itemsMerma[index].cantidad = itemsMerma[index].stock_max;
    }
    renderizarItemsMerma();
  }

  function removerItemMerma(index) {
    itemsMerma.splice(index, 1);
    renderizarItemsMerma();
  }

  function renderizarItemsMerma() {
    const tbody = document.getElementById('tbody-items-merma');
    if (itemsMerma.length === 0) {
      tbody.innerHTML = `<tr><td colspan="6" class="text-center py-3 text-muted small">No hay productos agregados en esta salida.</td></tr>`;
      document.getElementById('lbl-total-perdida-modal').innerText = '$0.00';
      return;
    }

    let html = '';
    let totalPerdida = 0;

    itemsMerma.forEach((item, index) => {
      const subtotal = item.cantidad * item.costo_unitario;
      totalPerdida += subtotal;

      html += `
        <tr>
          <td class="fw-bold text-dark">${item.nombre}</td>
          <td class="text-center text-muted">${item.stock_max}</td>
          <td class="text-center">
            <div class="input-group input-group-sm justify-content-center" style="width: 90px; margin: 0 auto;">
              <button class="btn btn-outline-secondary px-1 py-0" type="button" onclick="cambiarCantidadMerma(${index}, -1)">-</button>
              <input type="text" class="form-control text-center px-0 bg-white form-control-sm" value="${item.cantidad}" readonly>
              <button class="btn btn-outline-secondary px-1 py-0" type="button" onclick="cambiarCantidadMerma(${index}, 1)">+</button>
            </div>
          </td>
          <td class="text-end">$${item.costo_unitario.toFixed(2)}</td>
          <td class="text-end fw-bold">$${subtotal.toFixed(2)}</td>
          <td class="text-center">
            <button type="button" class="btn btn-icon btn-xs btn-outline-danger" onclick="removerItemMerma(${index})">×</button>
          </td>
        </tr>
      `;
    });

    tbody.innerHTML = html;
    document.getElementById('lbl-total-perdida-modal').innerText = `$${totalPerdida.toFixed(2)}`;
  }

  // Doble confirmación previa antes de registrar para prevenir errores humanos
  function confirmarGuardarMerma() {
    if (itemsMerma.length === 0) {
      Swal.fire({ icon: 'warning', title: 'Lista Vacía', text: 'Debe agregar al menos un producto a la salida extraordinaria.' });
      return;
    }

    Swal.fire({
      title: '¿Confirmar Salida Extraordinaria?',
      text: 'Se descontará el stock físico de forma definitiva para estos productos. ¿Desea proceder?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#2fb344',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, Registrar',
      cancelButtonText: 'Revisar'
    }).then((result) => {
      if (result.isConfirmed) {
        procesarGuardarMerma();
      }
    });
  }

  function procesarGuardarMerma() {
    const payload = {
      motivo: document.getElementById('select-motivo-merma').value,
      observaciones: document.getElementById('txt-observaciones-merma').value.trim(),
      items: itemsMerma
    };

    fetch('/anacosmeticsy/public/mermas/registrar', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        Swal.fire({ icon: 'error', title: 'Error al Registrar', text: data.message });
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
      Swal.fire({ icon: 'error', title: 'Error de Red', text: 'No se pudo comunicar con el servidor.' });
    });
  }

  function abrirModalAnularMerma(id, numSalida) {
    document.getElementById('input-anular-merma-id').value = id;
    document.getElementById('txt-num-salida-anular').innerText = `#${numSalida}`;
    document.getElementById('txt-motivo-anulacion').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('modal-anular-merma'));
    modal.show();
  }

  function procesarAnulacionMerma(event) {
    event.preventDefault();
    const mermaId = document.getElementById('input-anular-merma-id').value;
    const motivo = document.getElementById('txt-motivo-anulacion').value.trim();

    if (!motivo) {
      Swal.fire({ icon: 'warning', title: 'Motivo Requerido', text: 'Debe ingresar el motivo de anulación.' });
      return;
    }

    fetch('/anacosmeticsy/public/mermas/anular', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ merma_id: mermaId, motivo_anulacion: motivo })
    })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        Swal.fire({ icon: 'error', title: 'Error al Anular', text: data.message });
        return;
      }
      Swal.fire({
        icon: 'success',
        title: '¡Anulado!',
        text: data.message,
        timer: 1800,
        showConfirmButton: false
      }).then(() => {
        location.reload();
      });
    })
    .catch(err => {
      Swal.fire({ icon: 'error', title: 'Error de Red', text: 'No se pudo comunicar con el servidor.' });
    });
  }

  function verDetallesMerma(mermaId) {
    const modal = new bootstrap.Modal(document.getElementById('modal-detalle-merma'));
    modal.show();

    document.getElementById('lbl-detalle-titulo').innerText = `Detalle Salida #${String(mermaId).padStart(5, '0')}`;
    document.getElementById('modal-detalle-body').innerHTML = `<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>`;

    fetch(`/anacosmeticsy/public/mermas/obtenerDetalleAjax/${mermaId}`)
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          document.getElementById('modal-detalle-body').innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
          return;
        }

        let filas = '';
        let sumaTotal = 0;
        data.detalles.forEach(d => {
          sumaTotal += parseFloat(d.subtotal_perdida_usd);
          filas += `
            <tr>
              <td><code>${d.producto_codigo || 'S/C'}</code> - ${d.producto_nombre}</td>
              <td class="text-center">${d.cantidad}</td>
              <td class="text-end">$${parseFloat(d.costo_unitario_usd).toFixed(2)}</td>
              <td class="text-end fw-bold">$${parseFloat(d.subtotal_perdida_usd).toFixed(2)}</td>
            </tr>
          `;
        });

        let alertaAnuladaHtml = '';
        if (data.estado === 'anulada') {
          alertaAnuladaHtml = `
            <div class="alert alert-danger p-2 mb-3">
              <div class="fw-bold">⚠️ ESTA SALIDA FUE ANULADA</div>
              <div class="small"><strong>Motivo:</strong> ${data.motivo_anulacion || 'Sin motivo especificado'}</div>
            </div>
          `;
        }

        document.getElementById('modal-detalle-body').innerHTML = `
          ${alertaAnuladaHtml}
          <div class="table-responsive">
            <table class="table table-sm table-bordered card-table mb-2">
              <thead class="bg-light">
                <tr>
                  <th>Producto</th>
                  <th class="text-center">Cant. Retirada</th>
                  <th class="text-end">Costo Unit.</th>
                  <th class="text-end">Subtotal Costo</th>
                </tr>
              </thead>
              <tbody>${filas}</tbody>
            </table>
          </div>
          <div class="text-end fw-bold fs-3 text-dark">
            Valor Total de Salida: <span class="text-primary">$${sumaTotal.toFixed(2)}</span>
          </div>
        `;
      });
  }
</script>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>