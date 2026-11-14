<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<!-- CDNs de DataTables y SweetAlert2 por resguardo -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-blue-lt text-blue font-weight-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-history" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8l0 4l2 2" /><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" /></svg>
          </span>
          <div>
            <h2 class="page-title mb-0 fs-2">Historial de Ventas</h2>
            <div class="text-muted small">Consulta, exportación, reimpresión y anulación de comprobantes</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-auto ms-md-auto d-flex gap-2">
        <a href="<?= BASE_URL ?>/ventas" class="btn btn-primary d-flex align-items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash-register" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 21h14a2 2 0 0 0 2 -2v-8a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2z" /><path d="M6 7h12a3 3 0 0 0 3 -3v-1a1 1 0 0 0 -1 1v1a3 3 0 0 0 3 3z" /><path d="M9 14h2v2h-2z" /><path d="M13 14h2v2h-2z" /><path d="M17 14h2v2h-2z" /><path d="M9 18h2v2h-2z" /><path d="M13 18h2v2h-2z" /><path d="M17 18h2v2h-2z" /></svg>
          Ir a Caja (POS)
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-fluid px-3 px-lg-4">
    
    <!-- Alertas Flash -->
    <?php if (isset($_SESSION['mensaje'])): ?>
      <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <div class="d-flex align-items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
          <div><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <div class="d-flex align-items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5.07 19h13.86a1.5 1.5 0 0 0 1.3 -2.25l-6.93 -12a1.5 1.5 0 0 0 -2.6 0l-6.93 12a1.5 1.5 0 0 0 1.3 2.25z" /></svg>
          <div><?= htmlspecialchars($_SESSION['error']) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- TARJETA DE FILTROS -->
    <div class="card shadow-sm border-0 rounded-3 mb-3">
      <div class="card-header bg-light py-2">
        <h3 class="card-title fw-bold mb-0">🔍 Filtros de Búsqueda</h3>
      </div>
      <div class="card-body p-3">
        <form method="GET" action="<?= BASE_URL ?>/ventas/historial" class="row g-2">
          <div class="col-12 col-md-2">
            <label class="form-label small fw-bold mb-1">Fecha Desde:</label>
            <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="<?= htmlspecialchars($filtroFechaInicio ?? '') ?>">
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label small fw-bold mb-1">Fecha Hasta:</label>
            <input type="date" name="fecha_fin" class="form-control form-control-sm" value="<?= htmlspecialchars($filtroFechaFin ?? '') ?>">
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold mb-1">Cliente:</label>
            <select name="cliente_id" class="form-select form-select-sm">
              <option value="">-- Todos los Clientes --</option>
              <?php foreach ($clientes as $cli): ?>
                <option value="<?= $cli['id'] ?>" <?= (isset($filtroCliente) && $filtroCliente == $cli['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cli['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label small fw-bold mb-1">Servicio de Salón:</label>
            <select name="servicio_id" class="form-select form-select-sm">
              <option value="">-- Todos los Servicios --</option>
              <?php foreach ($servicios as $serv): ?>
                <option value="<?= $serv['id'] ?>" <?= (isset($filtroServicio) && $filtroServicio == $serv['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($serv['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 col-md-2 d-flex align-items-end gap-1">
            <button type="submit" class="btn btn-sm btn-primary w-100">Filtrar</button>
            <a href="<?= BASE_URL ?>/ventas/historial" class="btn btn-sm btn-outline-secondary" title="Limpiar Filtros">↺</a>
          </div>
        </form>
      </div>
    </div>

    <!-- TABLA DE VENTAS REGISTRADAS -->
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-body p-3">
        <div class="table-responsive">
          <table id="tabla-historial-ventas" class="table table-sm table-striped table-hover align-middle w-100">
            <thead class="table-light">
              <tr>
                <th># Nota</th>
                <th>Fecha / Hora</th>
                <th>Cliente</th>
                <th>Modalidad</th>
                <th>Estado</th>
                <th class="text-end">Total ($)</th>
                <th class="text-end">En Caja ($)</th>
                <th>Atendido por</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ventas as $v): ?>
                <?php 
                  $esAnulada = (isset($v['estado']) && $v['estado'] === 'anulada');
                  $fechaVentaSec = strtotime($v['created_at']);
                  $horasTranscurridas = (time() - $fechaVentaSec) / 3600;
                  $puedeAnular = (!$esAnulada && $horasTranscurridas <= 24);

                  // Definir monto que ingresó en caja
                  $montoEnCaja = $v['total_general_usd'];
                  if ($v['tipo_venta'] === 'cashea') {
                    $montoEnCaja = $v['monto_inicial_cashea_usd'];
                  } elseif ($v['tipo_venta'] === 'credito') {
                    $montoEnCaja = $v['monto_abonado_credito_usd'] ?? 0.00;
                  }
                ?>
                <tr class="<?= $esAnulada ? 'table-danger opacity-75' : '' ?>">
                  <td class="fw-bold">
                    #<?= str_pad($v['id'], 6, '0', STR_PAD_LEFT) ?>
                  </td>
                  <td class="small"><?= date('d/m/Y h:i A', strtotime($v['created_at'])) ?></td>
                  <td>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($v['cliente_nombre']) ?></div>
                    <div class="text-muted text-xs">📞 <?= htmlspecialchars($v['cliente_telefono'] ?? 'S/N') ?></div>
                  </td>
                  <td>
                    <?php if ($v['tipo_venta'] === 'cashea'): ?>
                      <span class="badge bg-yellow text-dark fw-bold">Cashea</span>
                    <?php elseif ($v['tipo_venta'] === 'credito'): ?>
                      <span class="badge bg-blue text-white fw-bold">Crédito Directo</span>
                    <?php else: ?>
                      <span class="badge bg-green-lt fw-bold">Regular</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($esAnulada): ?>
                      <span class="badge bg-danger text-white fw-bold" title="Motivo: <?= htmlspecialchars($v['motivo_anulacion'] ?? 'N/A') ?>">Anulada</span>
                    <?php else: ?>
                      <span class="badge bg-success text-white fw-bold">Completada</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end fw-bold $<?= $esAnulada ? 'text-decoration-line-through' : '' ?>">
                    $<?= number_format($v['total_general_usd'], 2) ?>
                  </td>
                  <td class="text-end text-success fw-bold">
                    $<?= number_format($montoEnCaja, 2) ?>
                  </td>
                  <td class="small text-muted"><?= htmlspecialchars($v['usuario_nombre']) ?></td>
                  <td class="text-center">
                    <div class="btn-list flex-nowrap justify-content-center">
                      <!-- Botón Ver Detalle en Modal -->
                      <button type="button" class="btn btn-icon btn-sm btn-outline-info" title="Ver Detalle" onclick="cargarDetalleVenta(<?= $v['id'] ?>)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                      </button>
                      
                      <!-- Botón Ver / Imprimir Nota -->
                      <a href="<?= BASE_URL ?>/ventas/notaEntrega/<?= $v['id'] ?>" target="_blank" class="btn btn-icon btn-sm btn-outline-primary" title="Imprimir Nota">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                      </a>

                      <!-- Botón Anular Venta (Límite 24h) -->
                      <?php if ($puedeAnular): ?>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger" title="Anular Venta (Límite 24h)" onclick="abrirModalAnularVenta(<?= $v['id'] ?>, '<?= str_pad($v['id'], 6, '0', STR_PAD_LEFT) ?>')">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-x" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>
                        </button>
                      <?php else: ?>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-secondary" disabled title="<?= $esAnulada ? 'La venta ya fue anulada' : 'Excedió el tiempo límite de 24 horas' ?>">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-ban" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M5.7 5.7l12.6 12.6" /></svg>
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

<!-- MODAL PARA MOSTRAR DETALLES DE LA VENTA -->
<div class="modal modal-blur fade" id="modal-detalle-venta" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-light py-2">
        <h5 class="modal-title fw-bold" id="lbl-modal-titulo">Detalle de la Venta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3" id="modal-detalle-body">
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status"></div>
          <div class="text-muted small mt-2">Cargando información...</div>
        </div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL CONFIRMAR ANULACIÓN DE VENTA -->
<div class="modal modal-blur fade" id="modal-anular-venta" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="<?= BASE_URL ?>/ventas/anular" method="POST" onsubmit="return validarAnulacionVenta(event)">
        <input type="hidden" name="venta_id" id="input-anular-venta-id">
        <div class="modal-body text-center py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5.07 19h13.86a1.5 1.5 0 0 0 1.3 -2.25l-6.93 -12a1.5 1.5 0 0 0 -2.6 0l-6.93 12a1.5 1.5 0 0 0 1.3 2.25z" /></svg>
          <h3 class="mb-1">¿Anular Venta <span id="txt-num-nota-anular">#000000</span>?</h3>
          <p class="text-muted small mb-3">Esta acción revertirá el stock de los productos, la deuda del cliente si fue a crédito y el estado de la cita correspondiente.</p>
          
          <div class="text-start mb-2">
            <label class="form-label small fw-bold text-dark mb-1">Motivo de Anulación <span class="text-danger">*</span></label>
            <textarea name="motivo_anulacion" id="txt-motivo-anulacion" class="form-control form-control-sm" rows="3" placeholder="Explique brevemente por qué anula esta venta..." required></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <div class="w-100">
            <div class="row g-2">
              <div class="col-6">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Cancelar</button>
              </div>
              <div class="col-6">
                <button type="submit" class="btn btn-danger w-100">Sí, Anular Venta</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JQUERY Y DATATABLES JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function() {
    $('#tabla-historial-ventas').DataTable({
      language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
      },
      order: [[0, 'desc']],
      pageLength: 10
    });
  });

  function abrirModalAnularVenta(id, numNota) {
    document.getElementById('input-anular-venta-id').value = id;
    document.getElementById('txt-num-nota-anular').innerText = `#${numNota}`;
    document.getElementById('txt-motivo-anulacion').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('modal-anular-venta'));
    modal.show();
  }

  function validarAnulacionVenta(event) {
    const motivo = document.getElementById('txt-motivo-anulacion').value.trim();
    if (!motivo) {
      Swal.fire({
        icon: 'warning',
        title: 'Motivo Requerido',
        text: 'Por favor ingrese el motivo por el cual desea anular la venta.',
        confirmButtonColor: '#206bc4'
      });
      event.preventDefault();
      return false;
    }
    return true;
  }

  function cargarDetalleVenta(ventaId) {
    const modal = new bootstrap.Modal(document.getElementById('modal-detalle-venta'));
    modal.show();

    fetch(`<?= BASE_URL ?>/ventas/obtenerDetalleAjax/${ventaId}`)
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          document.getElementById('modal-detalle-body').innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
          return;
        }

        const v = data.venta;
        document.getElementById('lbl-modal-titulo').innerText = `Detalle Venta #${String(v.id).padStart(6, '0')}`;

        let itemsHtml = '';
        v.detalles.forEach(d => {
          itemsHtml += `
            <tr>
              <td>${d.item_nombre}</td>
              <td class="text-center"><span class="badge ${d.tipo_item === 'producto' ? 'bg-blue-lt' : 'bg-purple-lt'}">${d.tipo_item}</span></td>
              <td class="text-center">${d.cantidad}</td>
              <td class="text-end">$${parseFloat(d.precio_unitario_usd).toFixed(2)}</td>
              <td class="text-end fw-bold">$${parseFloat(d.subtotal_usd).toFixed(2)}</td>
            </tr>
          `;
        });

        let pagosHtml = '';
        if (v.pagos && v.pagos.length > 0) {
          v.pagos.forEach(p => {
            pagosHtml += `
              <tr>
                <td class="text-capitalize">${p.metodo_pago.replace('_', ' ')}</td>
                <td class="text-end">$${parseFloat(p.monto_equivalente_usd).toFixed(2)}</td>
                <td>${p.referencia || 'N/A'}</td>
              </tr>
            `;
          });
        } else {
          pagosHtml = `<tr><td colspan="3" class="text-center text-muted small">No hubo ingresos directos en caja (Crédito Total).</td></tr>`;
        }

        let alertaAnuladaHtml = '';
        if (v.estado === 'anulada') {
          alertaAnuladaHtml = `
            <div class="alert alert-danger p-2 mb-3">
              <div class="fw-bold">⚠️ ESTA VENTA FUE ANULADA</div>
              <div class="small"><strong>Motivo:</strong> ${v.motivo_anulacion || 'Sin motivo especificado'}</div>
            </div>
          `;
        }

        document.getElementById('modal-detalle-body').innerHTML = `
          ${alertaAnuladaHtml}
          <div class="row g-2 mb-3">
            <div class="col-6">
              <div class="text-muted small">Cliente:</div>
              <div class="fw-bold">${v.cliente_nombre}</div>
            </div>
            <div class="col-6 text-end">
              <div class="text-muted small">Fecha:</div>
              <div class="fw-bold">${v.created_at}</div>
            </div>
          </div>
          
          <h6 class="fw-bold border-bottom pb-1 mb-2">Ítems Facturados</h6>
          <table class="table table-sm card-table table-bordered mb-3">
            <thead class="bg-light">
              <tr>
                <th>Ítem</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Cant</th>
                <th class="text-end">Precio Unit.</th>
                <th class="text-end">Subtotal</th>
              </tr>
            </thead>
            <tbody>${itemsHtml}</tbody>
          </table>

          <h6 class="fw-bold border-bottom pb-1 mb-2">Pagos Recibidos en Caja</h6>
          <table class="table table-sm card-table table-bordered mb-0">
            <thead class="bg-light">
              <tr>
                <th>Método</th>
                <th class="text-end">Monto Eq. USD</th>
                <th>Referencia</th>
              </tr>
            </thead>
            <tbody>${pagosHtml}</tbody>
          </table>
        `;
      })
      .catch(err => {
        document.getElementById('modal-detalle-body').innerHTML = `<div class="alert alert-danger">Error de comunicación con el servidor.</div>`;
      });
  }
</script>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>