<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<!-- Header tipo App Bar -->
<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">
      
      <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-yellow-lt text-yellow font-weight-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" /><path d="M9 7h6" /><path d="M9 11h6" /><path d="M9 15h4" /></svg>
          </span>
          <div>
            <h2 class="page-title mb-0 fs-2">Créditos & Cuentas por Cobrar</h2>
            <div class="text-muted small">Control de saldos pendientes, abonos y adeudos por cliente</div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-auto ms-md-auto d-flex gap-2">
        <a href="/anacosmeticsy/public/dashboard" class="btn btn-outline-secondary d-flex align-items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
          Volver al Dashboard
        </a>
      </div>

    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-fluid px-3 px-lg-4">
    
    <!-- Alertas -->
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

    <!-- Estructura de Dos Paneles Responsivos -->
    <div class="row row-cards">
      
      <!-- PANEL IZQUIERDO: Buscador Global y Tabla de Clientes -->
      <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 rounded-3">
          
          <div class="card-header bg-light py-2">
            <h3 class="card-title fw-bold mb-0">Seleccionar Cliente</h3>
          </div>

          <div class="card-body p-2 border-bottom bg-light-subtle">
            <div class="input-icon">
              <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
              </span>
              <input type="text" id="buscador-clientes-global" class="form-control form-control-sm" placeholder="Buscar en todo el sistema..." oninput="buscarClientesGlobal(this.value)">
            </div>
          </div>

          <div class="table-responsive" style="min-height: 380px; max-height: 480px; overflow-y: auto;">
            <table class="table table-vcenter card-table align-middle" id="tabla-clientes-credito">
              <thead>
                <tr class="bg-light">
                  <th>Cliente</th>
                  <th class="text-end" style="width: 80px;">Acción</th>
                </tr>
              </thead>
              <tbody id="tbody-clientes-lista">
                <?php if (!empty($clientes)): ?>
                  <?php foreach ($clientes as $cli): ?>
                    <tr class="fila-cliente-credito <?= ($clienteSeleccionadoId == $cli['id']) ? 'table-active border-start border-4 border-yellow' : '' ?>" style="cursor: pointer;" onclick="cargarHistorialCliente(<?= $cli['id'] ?>, this)">
                      <td>
                        <div class="fw-bold text-dark nombre-cli"><?= htmlspecialchars($cli['nombre']) ?></div>
                        <div class="text-muted small telefono-cli">📞 <?= htmlspecialchars($cli['telefono'] ?? 'Sin teléfono') ?></div>
                      </td>
                      <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-icon">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="2" class="text-center py-4 text-muted">No hay clientes registrados.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <?php if (isset($totalPaginas) && $totalPaginas > 1): ?>
            <div class="card-footer d-flex align-items-center justify-content-between bg-light py-2" id="paginacion-clientes-footer">
              <span class="text-muted text-xs">Pág. <?= $paginaActual ?> de <?= $totalPaginas ?></span>
              <ul class="pagination pagination-sm m-0">
                <li class="page-item <?= ($paginaActual <= 1) ? 'disabled' : '' ?>">
                  <a class="page-link" href="/anacosmeticsy/public/creditos?p=<?= $paginaActual - 1 ?>">&lsaquo;</a>
                </li>
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                  <li class="page-item <?= ($paginaActual == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="/anacosmeticsy/public/creditos?p=<?= $i ?>"><?= $i ?></a>
                  </li>
                <?php endfor; ?>
                <li class="page-item <?= ($paginaActual >= $totalPaginas) ? 'disabled' : '' ?>">
                  <a class="page-link" href="/anacosmeticsy/public/creditos?p=<?= $paginaActual + 1 ?>">&rsaquo;</a>
                </li>
              </ul>
            </div>
          <?php endif; ?>

        </div>
      </div>

      <!-- PANEL DERECHO: Estado Financiero y Pestañas -->
      <div class="col-12 col-lg-8">
        
        <!-- Tarjeta Superior: Estado de Crédito y Saldo a Favor -->
        <div class="card shadow-sm border-0 rounded-3 mb-3 bg-light-subtle">
          <div class="card-body p-3 p-md-4">
            <div class="row align-items-center g-3">
              <div class="col-12 col-md-6">
                <span class="text-uppercase text-muted small fw-bold d-block mb-1">Cliente Seleccionado</span>
                <h2 class="fw-extrabold text-dark mb-0" id="panel-nombre-cliente">
                  <?= $clienteActual ? htmlspecialchars($clienteActual['nombre']) : 'Seleccione un cliente' ?>
                </h2>
                <div class="text-muted small mt-1" id="panel-telefono-cliente">
                  <?= $clienteActual ? '📞 ' . htmlspecialchars($clienteActual['telefono'] ?? 'Sin teléfono') : '' ?>
                </div>
              </div>
              <div class="col-6 col-md-3 text-center border-start">
                <span class="text-uppercase text-muted small fw-bold d-block">Deuda Total Pendiente</span>
                <span class="fs-2 fw-extrabold text-danger" id="panel-deuda-pendiente">
                  $<?= $clienteActual ? number_format(array_sum(array_column(array_filter($creditosCliente, function($c){ return $c['estado']=='pendiente'; }), 'saldo_pendiente_usd')), 2) : '0.00' ?>
                </span>
              </div>
              <div class="col-6 col-md-3 text-center border-start">
                <span class="text-uppercase text-muted small fw-bold d-block">Saldo a Favor Disponible</span>
                <span class="fs-2 fw-extrabold text-success" id="panel-saldo-favor">
                  $<?= $clienteActual ? number_format($clienteActual['saldo_favor_usd'], 2) : '0.00' ?>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- SISTEMA DE 2 PESTAÑAS: Créditos vs Abonos -->
        <div class="card shadow-sm border-0 rounded-3">
          <div class="card-header bg-light py-2">
            <ul class="nav nav-tabs card-header-tabs" id="creditos-abonos-tabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="tab-creditos" data-bs-toggle="tab" data-bs-target="#content-creditos" type="button" role="tab" aria-selected="true">
                  📋 Historial de Créditos
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="tab-abonos" data-bs-toggle="tab" data-bs-target="#content-abonos" type="button" role="tab" aria-selected="false">
                  💵 Historial de Abonos
                </button>
              </li>
            </ul>
          </div>

          <div class="card-body p-3">
            <div class="tab-content" id="creditos-abonos-content">
              
              <!-- PESTAÑA 1: HISTORIAL DE CRÉDITOS -->
              <div class="tab-pane fade show active" id="content-creditos" role="tabpanel" aria-labelledby="tab-creditos">
                <div id="contenedor-historial-creditos" style="min-height: 280px;">
                  <?php if (!empty($creditosCliente)): ?>
                    <?php 
                      $porPaginaHistorial = 5;
                      $totalCreditos = count($creditosCliente);
                      $totalPaginasHistorial = ceil($totalCreditos / $porPaginaHistorial);
                      $creditosPaginaInicial = array_slice($creditosCliente, 0, $porPaginaHistorial);
                    ?>
                    <div id="lista-creditos-items">
                      <?php foreach ($creditosPaginaInicial as $cred): ?>
                        <div class="card border mb-3 shadow-xs">
                          <div class="card-header bg-light-subtle d-flex justify-content-between align-items-center py-2">
                            <div>
                              <span class="fw-bold text-dark">Crédito #<?= $cred['id'] ?></span>
                              <span class="text-muted small ms-2">📅 <?= date('d/m/Y H:i', strtotime($cred['created_at'])) ?></span>
                            </div>
                            <div>
                              <?php if ($cred['estado'] === 'pagado'): ?>
                                <span class="badge bg-success-lt fw-bold">Pagado</span>
                              <?php else: ?>
                                <span class="badge bg-warning-lt fw-bold">Pendiente</span>
                              <?php endif; ?>
                            </div>
                          </div>
                          <div class="card-body p-3">
                            <div class="row g-2 align-items-center mb-3">
                              <div class="col-6 col-md-4">
                                <span class="text-muted small d-block">Total General:</span>
                                <strong class="text-dark">$<?= number_format($cred['total_general_usd'], 2) ?></strong>
                              </div>
                              <div class="col-6 col-md-4">
                                <span class="text-muted small d-block">Saldo Pendiente:</span>
                                <strong class="text-danger">$<?= number_format($cred['saldo_pendiente_usd'], 2) ?></strong>
                              </div>
                              <div class="col-12 col-md-4 text-md-end">
                                <?php if ($cred['estado'] === 'pendiente'): ?>
                                  <button class="btn btn-sm btn-success w-100 w-md-auto" onclick='abrirModalAbono(<?= json_encode($cred) ?>, <?= $tasaUsd ?>)'>
                                    Registrar Abono
                                  </button>
                                <?php endif; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?>
                    <div class="text-center py-5 text-muted">
                      <div>Este cliente no posee créditos registrados en su historial.</div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- PESTAÑA 2: HISTORIAL DE ABONOS -->
              <div class="tab-pane fade" id="content-abonos" role="tabpanel" aria-labelledby="tab-abonos">
                <div id="contenedor-historial-abonos" style="min-height: 280px;">
                  <!-- Rellenado dinámicamente por JavaScript al seleccionar cliente -->
                  <div class="text-center py-5 text-muted">
                    <div>Seleccione un cliente para ver su historial de abonos.</div>
                  </div>
                </div>
              </div>

            </div>
          </div>

        </div>

      </div>

    </div>

  </div>
</div>

<!-- Modal Registrar Abono con Pagos Múltiples (Mixtos) -->
<div class="modal modal-blur fade" id="modal-abono" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="/anacosmeticsy/public/creditos/abonar" method="POST" id="form-registrar-abono" onsubmit="return validarFormularioAbono(event)">
        <input type="hidden" name="credito_id" id="abono-credito-id">
        <input type="hidden" name="cliente_id_redirect" id="abono-cliente-id-redirect">
        
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-dark">Registrar Abono con Pagos Múltiples / Mixtos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">
          <div class="row bg-light-subtle p-3 rounded-3 mb-3 align-items-center">
            <div class="col-6">
              <span class="text-muted small d-block">Saldo Pendiente del Crédito:</span>
              <span class="fs-2 fw-extrabold text-danger" id="lbl-abono-saldo">---</span>
              <input type="hidden" id="input-saldo-pendiente-actual" value="0">
            </div>
            <div class="col-6 text-end">
              <span class="text-muted small d-block">Tasa BCV del Día:</span>
              <span class="fw-bold text-success fs-3">Bs. <?= number_format($tasaUsd, 2, ',', '.') ?></span>
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="form-label fw-bold text-dark mb-0">Formas de Pago Aplicadas</label>
              <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" onclick="agregarLineaPago()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                Agregar Método de Pago
              </button>
            </div>

            <div class="table-responsive border rounded-3 bg-light-subtle">
              <table class="table table-vcenter table-sm card-table align-middle mb-0">
                <thead>
                  <tr class="bg-light">
                    <th>Método / Forma</th>
                    <th style="width: 150px;">Monto Original</th>
                    <th style="width: 140px;">Referencia</th>
                    <th class="text-end" style="width: 100px;">Eq. USD</th>
                    <th class="text-center" style="width: 50px;">Acción</th>
                  </tr>
                </thead>
                <tbody id="tabla-pagos-body"></tbody>
              </table>
            </div>
          </div>

          <div class="card bg-success-lt border-0 p-3 rounded-3 mt-3">
            <div class="row align-items-center">
              <div class="col-7">
                <span class="text-uppercase small fw-bold text-muted d-block">Total Abonado (Mixto)</span>
                <span class="fs-3 fw-extrabold text-dark" id="lbl-total-abonado-usd">$0.00</span>
                <div class="small text-muted" id="lbl-total-abonado-bs">Bs. 0,00</div>
              </div>
              <div class="col-5 text-end border-start">
                <span class="text-uppercase small fw-bold text-muted d-block">Saldo a Favor Generado</span>
                <span class="fs-3 fw-extrabold text-success" id="lbl-saldo-favor-generado">$0.00</span>
              </div>
            </div>
          </div>

          <div class="alert alert-success d-none p-2 mt-3 mb-0" id="alerta-saldo-favor">
            <div class="small fw-bold">⚠️ El total abonado supera la deuda. El excedente de <span id="txt-excedente-monto" class="fw-extrabold">$0.00</span> se acreditará de forma automática como <strong>Saldo a Favor</strong> del cliente.</div>
          </div>
        </div>

        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success px-4">Procesar Abono Mixto</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Confirmar Anulación de Abono -->
<div class="modal modal-blur fade" id="modal-anular-abono" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="/anacosmeticsy/public/creditos/anularAbono" method="POST">
        <input type="hidden" name="abono_id" id="anular-abono-id">
        <input type="hidden" name="cliente_id_redirect" id="anular-cliente-id-redirect">
        <div class="modal-body text-center py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5.07 19h13.86a1.5 1.5 0 0 0 1.3 -2.25l-6.93 -12a1.5 1.5 0 0 0 -2.6 0l-6.93 12a1.5 1.5 0 0 0 1.3 2.25z" /></svg>
          <h3>¿Anular este Abono?</h3>
          <p class="text-muted small">El monto abonado será devuelto al saldo pendiente del crédito y se revertirá cualquier excedente aplicado a saldo a favor.</p>
        </div>
        <div class="modal-footer bg-light py-2">
          <div class="w-100">
            <div class="row">
              <div class="col">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Cancelar</button>
              </div>
              <div class="col">
                <button type="submit" class="btn btn-danger w-100">Sí, anular</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  let tasaActualBCV = <?= floatval($tasaUsd) ?>;
  let clienteIdActual = <?= $clienteSeleccionadoId ? $clienteSeleccionadoId : 'null' ?>;
  let historialCreditosGlobal = <?= json_encode($creditosCliente ?? []) ?>;
  let historialAbonosGlobal = [];
  let paginaHistorialActual = 1;
  const porPaginaHistorial = 5;
  let contadorLineaPago = 0;

  function buscarClientesGlobal(query) {
    query = query.trim();
    const paginacionFooter = document.getElementById('paginacion-clientes-footer');
    if (paginacionFooter) paginacionFooter.style.display = query.length > 0 ? 'none' : '';

    fetch(`/anacosmeticsy/public/creditos/buscarClientes?q=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        if (!data.success) return;
        const tbody = document.getElementById('tbody-clientes-lista');
        if (data.clientes.length === 0) {
          tbody.innerHTML = `<tr><td colspan="2" class="text-center py-4 text-muted">No se encontraron clientes.</td></tr>`;
          return;
        }

        let html = '';
        data.clientes.forEach(cli => {
          const isActive = clienteIdActual == cli['id'] ? 'table-active border-start border-4 border-yellow' : '';
          html += `
            <tr class="fila-cliente-credito ${isActive}" style="cursor: pointer;" onclick="cargarHistorialCliente(${cli['id']}, this)">
              <td>
                <div class="fw-bold text-dark nombre-cli">${cli['nombre']}</div>
                <div class="text-muted small telefono-cli">📞 ${cli['telefono'] || 'Sin teléfono'}</div>
              </td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-primary btn-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                </button>
              </td>
            </tr>
          `;
        });
        tbody.innerHTML = html;
      });
  }

  function cargarHistorialCliente(clienteId, elementoFila) {
    clienteIdActual = clienteId;
    paginaHistorialActual = 1;
    
    document.querySelectorAll('.fila-cliente-credito').forEach(f => f.classList.remove('table-active', 'border-start', 'border-4', 'border-yellow'));
    if (elementoFila) elementoFila.classList.add('table-active', 'border-start', 'border-4', 'border-yellow');

    fetch(`/anacosmeticsy/public/creditos/obtenerHistorialCliente/${clienteId}`)
      .then(res => res.json())
      .then(data => {
        if (!data.success) return;

        document.getElementById('panel-nombre-cliente').innerText = data.cliente.nombre;
        document.getElementById('panel-telefono-cliente').innerText = `📞 ${data.cliente.telefono || 'Sin teléfono'}`;
        document.getElementById('panel-saldo-favor').innerText = `$${parseFloat(data.cliente.saldo_favor_usd).toFixed(2)}`;

        let deudaTotal = 0;
        data.creditos.forEach(c => {
          if (c.estado === 'pendiente') deudaTotal += parseFloat(c.saldo_pendiente_usd);
        });
        document.getElementById('panel-deuda-pendiente').innerText = `$${deudaTotal.toFixed(2)}`;

        historialCreditosGlobal = data.creditos;
        historialAbonosGlobal = data.abonos;
        renderizarHistorialPaginado();
        renderizarHistorialAbonos();
      });
  }

  function renderizarHistorialPaginado() {
    const contenedor = document.getElementById('contenedor-historial-creditos');
    if (historialCreditosGlobal.length === 0) {
      contenedor.innerHTML = `<div class="text-center py-5 text-muted">Este cliente no posee créditos registrados.</div>`;
      return;
    }

    const totalPaginas = Math.ceil(historialCreditosGlobal.length / porPaginaHistorial);
    if (paginaHistorialActual > totalPaginas) paginaHistorialActual = totalPaginas;

    const inicio = (paginaHistorialActual - 1) * porPaginaHistorial;
    const creditosPagina = historialCreditosGlobal.slice(inicio, inicio + porPaginaHistorial);

    let html = '<div id="lista-creditos-items">';
    creditosPagina.forEach(cred => {
      const badgeEstado = cred.estado === 'pagado' ? '<span class="badge bg-success-lt fw-bold">Pagado</span>' : '<span class="badge bg-warning-lt fw-bold">Pendiente</span>';
      const btnAbonar = cred.estado === 'pendiente' ? `<button class="btn btn-sm btn-success w-100 w-md-auto" onclick='abrirModalAbono(${JSON.stringify(cred)}, ${tasaActualBCV})'>Registrar Abono</button>` : '';

      html += `
        <div class="card border mb-3 shadow-xs">
          <div class="card-header bg-light-subtle d-flex justify-content-between align-items-center py-2">
            <div><span class="fw-bold text-dark">Crédito #${cred.id}</span><span class="text-muted small ms-2">📅 ${cred.created_at}</span></div>
            <div>${badgeEstado}</div>
          </div>
          <div class="card-body p-3">
            <div class="row g-2 align-items-center mb-3">
              <div class="col-6 col-md-4"><span class="text-muted small d-block">Total General:</span><strong class="text-dark">$${parseFloat(cred.total_general_usd).toFixed(2)}</strong></div>
              <div class="col-6 col-md-4"><span class="text-muted small d-block">Saldo Pendiente:</span><strong class="text-danger">$${parseFloat(cred.saldo_pendiente_usd).toFixed(2)}</strong></div>
              <div class="col-12 col-md-4 text-md-end">${btnAbonar}</div>
            </div>
          </div>
        </div>
      `;
    });
    html += '</div>';
    contenedor.innerHTML = html;
  }

  function renderizarHistorialAbonos() {
    const contenedor = document.getElementById('contenedor-historial-abonos');
    if (!historialAbonosGlobal || historialAbonosGlobal.length === 0) {
      contenedor.innerHTML = `<div class="text-center py-5 text-muted">Este cliente no posee abonos registrados en su historial.</div>`;
      return;
    }

    let html = '';
    historialAbonosGlobal.forEach(ab => {
      let detallesPagosHtml = '';
      if (ab.detalles_pagos && ab.detalles_pagos.length > 0) {
        ab.detalles_pagos.forEach(det => {
          detallesPagosHtml += `
            <div class="d-flex justify-content-between align-items-center small border-bottom py-1">
              <div><span class="badge bg-blue-lt text-uppercase">${det.metodo_pago}</span> <span class="text-muted ms-1">Ref: ${det.referencia || '---'}</span></div>
              <div class="fw-bold text-success">$${parseFloat(det.monto_equivalente_usd).toFixed(2)} <span class="text-xs text-muted">(Orig: ${det.monto_ingresado})</span></div>
            </div>
          `;
        });
      }

      html += `
        <div class="card border mb-3 shadow-xs">
          <div class="card-header bg-light-subtle d-flex justify-content-between align-items-center py-2">
            <div>
              <span class="fw-bold text-dark">Abono #${ab.id}</span>
              <span class="text-muted small ms-2">Aplicado a Crédito #${ab.credito_id_ref}</span>
              <span class="text-muted small ms-2">📅 ${ab.created_at}</span>
            </div>
            <div>
              <button class="btn btn-xs btn-outline-danger" onclick="abrirModalAnularAbono(${ab.id})">Anular Abono</button>
            </div>
          </div>
          <div class="card-body p-3">
            <div class="mb-2"><strong>Total Abonado:</strong> <span class="text-success fw-bold fs-3">+$${parseFloat(ab.monto_abono_usd).toFixed(2)}</span></div>
            <div class="bg-light p-2 rounded">${detallesPagosHtml}</div>
          </div>
        </div>
      `;
    });
    contenedor.innerHTML = html;
  }

  function abrirModalAnularAbono(abonoId) {
    document.getElementById('anular-abono-id').value = abonoId;
    document.getElementById('anular-cliente-id-redirect').value = clienteIdActual;
    const modal = new bootstrap.Modal(document.getElementById('modal-anular-abono'));
    modal.show();
  }

  function abrirModalAbono(credito, tasa) {
    document.getElementById('abono-credito-id').value = credito.id;
    document.getElementById('abono-cliente-id-redirect').value = clienteIdActual;
    document.getElementById('lbl-abono-saldo').innerText = `$${parseFloat(credito.saldo_pendiente_usd).toFixed(2)}`;
    document.getElementById('input-saldo-pendiente-actual').value = credito.saldo_pendiente_usd;
    
    document.getElementById('tabla-pagos-body').innerHTML = '';
    contadorLineaPago = 0;
    agregarLineaPago();

    calcularTotalesAbonoMixto();
    const modal = new bootstrap.Modal(document.getElementById('modal-abono'));
    modal.show();
  }

  function agregarLineaPago() {
    contadorLineaPago++;
    const tbody = document.getElementById('tabla-pagos-body');
    const tr = document.createElement('tr');
    tr.id = `fila-pago-${contadorLineaPago}`;

    tr.innerHTML = `
      <td>
        <select name="pagos[${contadorLineaPago}][metodo]" class="form-select form-select-sm" required>
          <option value="efectivo_usd">Efectivo USD ($)</option>
          <option value="efectivo_bs">Efectivo Bolívares (Bs)</option>
          <option value="pago_movil">Pago Móvil / Transf. (Bs)</option>
          <option value="punto_venta">Punto de Venta / Biopago (Bs)</option>
          <option value="zelle">Zelle ($)</option>
          <option value="usdt">USDT ($)</option>
        </select>
      </td>
      <td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm monto-linea" name="pagos[${contadorLineaPago}][monto]" placeholder="0.00" required oninput="calcularTotalesAbonoMixto()"></td>
      <td><input type="text" class="form-control form-control-sm" name="pagos[${contadorLineaPago}][referencia]" placeholder="Ref. opcional"></td>
      <td class="text-end fw-bold text-success eq-usd-col">$0.00</td>
      <td class="text-center">
        <button type="button" class="btn btn-icon btn-sm btn-outline-danger" onclick="removerLineaPago(${contadorLineaPago})">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  }

  function removerLineaPago(id) {
    const filas = document.querySelectorAll('#tabla-pagos-body tr');
    if (filas.length <= 1) { alert("Debe mantener al menos un método de pago."); return; }
    document.getElementById(`fila-pago-${id}`)?.remove();
    calcularTotalesAbonoMixto();
  }

  function calcularTotalesAbonoMixto() {
    const filas = document.querySelectorAll('#tabla-pagos-body tr');
    let totalAbonadoUsd = 0;
    const saldoPendienteUsd = parseFloat(document.getElementById('input-saldo-pendiente-actual').value) || 0;

    filas.forEach(fila => {
      const metodo = fila.querySelector('select').value;
      const montoIngresado = parseFloat(fila.querySelector('.monto-linea').value) || 0;
      const metodosEnBs = ['efectivo_bs', 'pago_movil', 'punto_venta'];
      let eqUsd = metodosEnBs.includes(metodo) ? (tasaActualBCV > 0 ? montoIngresado / tasaActualBCV : 0) : montoIngresado;

      fila.querySelector('.eq-usd-col').innerText = `$${eqUsd.toFixed(2)}`;
      totalAbonadoUsd += eqUsd;
    });

    const totalAbonadoBs = totalAbonadoUsd * tasaActualBCV;
    document.getElementById('lbl-total-abonado-usd').innerText = `$${totalAbonadoUsd.toFixed(2)}`;
    document.getElementById('lbl-total-abonado-bs').innerText = `Bs. ${totalAbonadoBs.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

    const alerta = document.getElementById('alerta-saldo-favor');
    const txtExcedente = document.getElementById('txt-excedente-monto');
    const lblSaldoFavorGen = document.getElementById('lbl-saldo-favor-generado');

    if (totalAbonadoUsd > saldoPendienteUsd + 0.001) {
      const excesoUsd = totalAbonadoUsd - saldoPendienteUsd;
      const excesoBs = excesoUsd * tasaActualBCV;
      txtExcedente.innerText = `$${excesoUsd.toFixed(2)} USD (Bs. ${excesoBs.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })})`;
      lblSaldoFavorGen.innerText = `$${excesoUsd.toFixed(2)}`;
      alerta.classList.remove('d-none');
    } else {
      lblSaldoFavorGen.innerText = '$0.00';
      alerta.classList.add('d-none');
    }
  }

  function validarFormularioAbono(event) {
    if (document.querySelectorAll('#tabla-pagos-body tr').length === 0) {
      alert("Debe agregar al menos una forma de pago.");
      event.preventDefault();
      return false;
    }
    return true;
  }
</script>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>