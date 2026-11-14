<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<!-- Header tipo App Bar -->
<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-green-lt text-green font-weight-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash-register" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 21h14a2 2 0 0 0 2 -2v-8a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2z" /><path d="M6 7h12a3 3 0 0 0 3 -3v-1a1 1 0 0 0 -1 -1h-14a1 1 0 0 0 -1 1v1a3 3 0 0 0 3 3z" /><path d="M9 14h2v2h-2z" /><path d="M13 14h2v2h-2z" /><path d="M17 14h2v2h-2z" /><path d="M9 18h2v2h-2z" /><path d="M13 18h2v2h-2z" /><path d="M17 18h2v2h-2z" /></svg>
          </span>
          <div>
            <h2 class="page-title mb-0 fs-2">Salida y Caja (POS)</h2>
            <div class="text-muted small">Facturación de productos, servicios de citas, Cashea, Créditos y pagos múltiples</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-auto ms-md-auto d-flex gap-2">
        <a href="<?= BASE_URL ?>/ventas/historial" class="btn btn-outline-primary d-flex align-items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-history" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8l0 4l2 2" /><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" /></svg>
          Historial de Ventas
        </a>
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary d-flex align-items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
          Volver al Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-fluid px-3 px-lg-4">
    
    <div class="row row-cards">
      
      <!-- COLUMNA IZQUIERDA: Selección de Cliente y Catálogo de Productos / Servicios / Citas -->
      <div class="col-12 col-lg-7">
        
        <!-- 1. Selección de Cliente -->
        <div class="card shadow-sm border-0 rounded-3 mb-3">
          <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h3 class="card-title fw-bold mb-0">1. Cliente Facturado</h3>
            <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modal-nuevo-cliente-pos">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
              Nuevo Cliente
            </button>
          </div>
          <div class="card-body p-3">
            <div class="row g-2 align-items-center">
              <div class="col-12 col-md-9">
                <select id="select-cliente-pos" class="form-select" onchange="seleccionarCliente(this)">
                  <option value="">-- Seleccione un cliente --</option>
                  <?php foreach ($clientes as $cli): ?>
                    <option value="<?= $cli['id'] ?>" data-nombre="<?= htmlspecialchars($cli['nombre']) ?>" data-telefono="<?= htmlspecialchars($cli['telefono'] ?? '') ?>" data-saldo="<?= $cli['saldo_favor_usd'] ?>">
                      <?= htmlspecialchars($cli['nombre']) ?> (📞 <?= htmlspecialchars($cli['telefono'] ?? 'Sin telf') ?>) - Saldo Favor: $<?= number_format($cli['saldo_favor_usd'], 2) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-12 col-md-3">
                <div class="text-muted small">Saldo a Favor:</div>
                <div class="fw-bold text-success fs-3" id="lbl-saldo-favor-cliente">$0.00</div>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. Importar Cita Pendiente del Día (Servicios) -->
        <div class="card shadow-sm border-0 rounded-3 mb-3">
          <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h3 class="card-title fw-bold mb-0">📅 Citas de Salón Pendientes Hoy</h3>
            <span class="badge bg-blue-lt"><?= count($citasPendientes) ?> Disponibles</span>
          </div>
          <div class="card-body p-3">
            <?php if (!empty($citasPendientes)): ?>
              <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                <table class="table table-sm table-vcenter card-table">
                  <thead>
                    <tr>
                      <th>Cliente / Hora</th>
                      <th>Servicio Agendado</th>
                      <th class="text-end">Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($citasPendientes as $cita): ?>
                      <tr>
                        <td>
                          <div class="fw-bold text-dark"><?= htmlspecialchars($cita['cliente_nombre']) ?></div>
                          <div class="text-muted small">🕒 <?= htmlspecialchars($cita['hora_cita'] ?? date('H:i', strtotime($cita['fecha_cita']))) ?></div>
                        </td>
                        <td>
                          <div><?= htmlspecialchars($cita['servicio_nombre']) ?></div>
                          <strong class="text-success">$<?= number_format($cita['precio_usd'], 2) ?></strong>
                        </td>
                        <td class="text-end">
                          <button type="button" class="btn btn-sm btn-outline-success" onclick='importarCitaACarrito(<?= json_encode($cita) ?>)'>
                            Cargar en Carrito
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="text-center py-2 text-muted small">No hay citas pendientes registradas para hoy.</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- 3. Catálogo de Productos y Servicios Adicionales -->
        <div class="card shadow-sm border-0 rounded-3">
          <div class="card-header bg-light py-2">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
              <li class="nav-item"><a href="#tab-productos" class="nav-link active fw-bold" data-bs-toggle="tab">🛍️ Productos (Inventario)</a></li>
              <li class="nav-item"><a href="#tab-servicios" class="nav-link fw-bold" data-bs-toggle="tab">✨ Servicios Extra (Salón)</a></li>
            </ul>
          </div>
          <div class="card-body p-3">
            <div class="tab-content">
              
              <!-- Pestaña Productos -->
              <div class="tab-pane active show" id="tab-productos">
                <div class="input-icon mb-2">
                  <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                  </span>
                  <input type="text" class="form-control form-control-sm" id="busqueda-prod-pos" placeholder="Buscar producto por nombre..." oninput="filtrarProductosCatalogo(this.value)">
                </div>
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                  <table class="table table-sm table-vcenter card-table">
                    <tbody id="tbody-catalogo-productos">
                      <?php foreach ($productos as $prod): ?>
                        <tr class="item-catalogo-prod" data-nombre="<?= strtolower($prod['nombre']) ?>">
                          <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($prod['nombre']) ?></div>
                            <div class="text-muted small">Stock: <span class="<?= $prod['stock'] > 0 ? 'text-success' : 'text-danger' ?>"><?= $prod['stock'] ?></span></div>
                          </td>
                          <td class="text-end">
                            <span class="fw-bold text-primary me-2">$<?= number_format($prod['precio_venta_usd'], 2) ?></span>
                            <button type="button" class="btn btn-sm btn-primary" <?= $prod['stock'] <= 0 ? 'disabled' : '' ?> onclick='agregarItemCarrito("producto", <?= $prod['id'] ?>, "<?= htmlspecialchars($prod['nombre']) ?>", <?= $prod['precio_venta_usd'] ?>, <?= $prod['stock'] ?>)'>
                              + Agregar
                            </button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Pestaña Servicios -->
              <div class="tab-pane" id="tab-servicios">
                <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                  <table class="table table-sm table-vcenter card-table">
                    <tbody>
                      <?php foreach ($servicios as $serv): ?>
                        <tr>
                          <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($serv['nombre']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($serv['descripcion'] ?? 'Servicio de salón') ?></div>
                          </td>
                          <td class="text-end">
                            <span class="fw-bold text-primary me-2">$<?= number_format($serv['precio_usd'], 2) ?></span>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick='agregarItemCarrito("servicio", <?= $serv['id'] ?>, "<?= htmlspecialchars($serv['nombre']) ?>", <?= $serv['precio_usd'] ?>, 9999)'>
                              + Agregar
                            </button>
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

      </div>

      <!-- COLUMNA DERECHA: Carrito Híbrido, Descuentos, Modalidad y Pagos -->
      <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-0 rounded-3">
          
          <div class="card-header bg-dark text-white py-2 d-flex justify-content-between align-items-center">
            <h3 class="card-title fw-bold mb-0 text-white">🛒 Carrito de Venta Híbrido</h3>
            <span class="badge bg-yellow text-dark fw-bold" id="badge-contador-items">0 Ítems</span>
          </div>

          <div class="card-body p-3">
            
            <!-- Tabla de Ítems en el Carrito -->
            <div class="table-responsive mb-3" style="min-height: 180px; max-height: 240px; overflow-y: auto;">
              <table class="table table-sm table-vcenter card-table align-middle">
                <thead>
                  <tr class="bg-light">
                    <th>Ítem / Precio Unit.</th>
                    <th style="width: 75px;">Cant</th>
                    <th class="text-end" style="width: 80px;">Subtotal</th>
                    <th class="text-center" style="width: 30px;"></th>
                  </tr>
                </thead>
                <tbody id="tbody-carrito-items">
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted small">El carrito está vacío. Agregue productos o servicios.</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Opciones de Venta: Descuento y Modalidad (Regular, Cashea, Crédito Directo) -->
            <div class="bg-light-subtle p-3 rounded-3 mb-3 border">
              
              <!-- Descuento Comercial Flexible (5% al 10%) -->
              <div class="mb-2">
                <label class="form-label small fw-bold mb-1">Descuento Comercial Opcional</label>
                <select id="select-descuento-pos" class="form-select form-select-sm" onchange="recalcularCarrito()">
                  <option value="0">Sin descuento (0%)</option>
                  <option value="5">5% de descuento</option>
                  <option value="6">6% de descuento</option>
                  <option value="7">7% de descuento</option>
                  <option value="8">8% de descuento</option>
                  <option value="9">9% de descuento</option>
                  <option value="10">10% de descuento</option>
                </select>
              </div>

              <!-- Modalidad de Venta -->
              <div class="mb-2">
                <label class="form-label small fw-bold mb-1">Modalidad de Facturación</label>
                <select id="select-tipo-venta" class="form-select form-select-sm" onchange="cambiarModalidadVenta()">
                  <option value="regular">Venta Regular (100% en Caja)</option>
                  <option value="cashea">Financiamiento Cashea (Paga Inicial en Caja)</option>
                  <option value="credito">Crédito Directo (Deuda en Cuenta)</option>
                </select>
              </div>

              <!-- Contenedor Condicional Cashea -->
              <div id="contenedor-cashea-config" class="d-none mt-2 p-2 bg-yellow-lt rounded border border-warning">
                <label class="form-label small fw-bold text-dark mb-1">Monto Inicial a Cancelar en Caja ($ USD)</label>
                <input type="number" step="0.01" min="0.01" class="form-control form-control-sm bg-white" id="input-inicial-cashea" placeholder="0.00" oninput="recalcularCarrito()">
                <div class="text-xs text-muted mt-1">Este monto entra a caja; el resto lo financia Cashea.</div>
              </div>

              <!-- Contenedor Condicional Crédito Directo -->
              <div id="contenedor-credito-config" class="d-none mt-2 p-2 bg-blue-lt rounded border border-primary">
                <label class="form-label small fw-bold text-dark mb-1">Monto Abonado Inicialmente ($ USD)</label>
                <input type="number" step="0.01" min="0.00" class="form-control form-control-sm bg-white" id="input-abonado-credito" placeholder="0.00" oninput="recalcularCarrito()">
                <div class="text-xs text-muted mt-1">Indique si abona algo hoy; el saldo restante pasará a la deuda del cliente.</div>
              </div>

            </div>

            <!-- Resumen Financiero -->
            <div class="border-top pt-2 mb-3">
              <div class="d-flex justify-content-between small text-muted mb-1">
                <span>Subtotal General:</span>
                <span id="lbl-subtotal-resumen">$0.00</span>
              </div>
              <div class="d-flex justify-content-between small text-danger mb-1">
                <span>Descuento Aplicado:</span>
                <span id="lbl-descuento-resumen">-$0.00</span>
              </div>
              <div class="d-flex justify-content-between fs-3 fw-extrabold text-dark mt-2 border-top pt-2">
                <span id="lbl-titulo-total-pagar">Total a Pagar en Caja:</span>
                <span class="text-success" id="lbl-total-pagar-resumen">$0.00</span>
              </div>
              <div class="text-end text-xs text-muted mb-2" id="lbl-total-bcv-resumen">Eq. Bs. 0,00 (Tasa BCV: <?= number_format($tasaUsd, 2) ?>)</div>
              
              <!-- RESTANTE A PAGAR EN TIEMPO REAL -->
              <div class="p-2 bg-orange-lt rounded text-center border border-orange">
                <span class="text-uppercase small fw-bold text-dark d-block" id="lbl-titulo-restante">Restante a Pagar en Caja:</span>
                <span class="fs-3 fw-extrabold text-orange" id="lbl-restante-usd">$0.00</span>
                <div class="text-xs text-muted" id="lbl-restante-bs">Eq. Bs. 0,00</div>
              </div>
            </div>

            <!-- SECCIÓN DE MÉTODOS DE PAGO MÚLTIPLES -->
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label small fw-bold text-dark mb-0">Formas de Pago Mixtas</label>
                <button type="button" class="btn btn-xs btn-outline-primary" onclick="agregarLineaPagoPos()">+ Agregar Pago</button>
              </div>
              <div class="table-responsive border rounded bg-light-subtle" style="max-height: 150px; overflow-y: auto;">
                <table class="table table-sm table-vcenter card-table align-middle mb-0">
                  <tbody id="tbody-pagos-pos">
                    <!-- Inyectado por JS -->
                  </tbody>
                </table>
              </div>
              <div class="d-flex justify-content-between small mt-1">
                <span class="text-muted">Total Ingresado:</span>
                <span class="fw-bold text-success" id="lbl-total-ingresado-pagos">$0.00</span>
              </div>
            </div>

            <!-- Botón Procesar Venta -->
            <button type="button" class="btn btn-success w-100 py-2 fs-3 fw-bold shadow-sm" onclick="procesarVentaPos()">
              🚀 Procesar Venta y Generar Nota
            </button>

          </div>

        </div>
      </div>

    </div>

  </div>
</div>

<!-- Modal Registrar Nuevo Cliente (POS) -->
<div class="modal modal-blur fade" id="modal-nuevo-cliente-pos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form id="form-nuevo-cliente-pos" onsubmit="registrarClientePos(event)">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-dark">Registrar Nuevo Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold required">Nombre Completo</label>
            <input type="text" class="form-control" id="nuevo-cli-nombre" placeholder="Ej. María Pérez" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Teléfono</label>
            <input type="text" class="form-control" id="nuevo-cli-telefono" placeholder="Ej. 0414-1234567">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Dirección</label>
            <textarea class="form-control" id="nuevo-cli-direccion" rows="2" placeholder="Dirección opcional"></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary px-4" id="btn-guardar-cliente">Guardar Cliente</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  let tasaActualBCV = <?= floatval($tasaUsd) ?>;
  let carritoItems = [];
  let pagosPos = [];
  let clienteSeleccionadoId = null;
  let citaAsociadaId = null;

  document.addEventListener("DOMContentLoaded", function() {
    agregarLineaPagoPos();
  });

  function seleccionarCliente(select) {
    clienteSeleccionadoId = select.value;
    const option = select.options[select.selectedIndex];
    const saldoFavor = option ? parseFloat(option.getAttribute('data-saldo')) || 0 : 0;
    document.getElementById('lbl-saldo-favor-cliente').innerText = `$${saldoFavor.toFixed(2)}`;
  }

  function registrarClientePos(event) {
    event.preventDefault();
    
    const nombre = document.getElementById('nuevo-cli-nombre').value.trim();
    const telefono = document.getElementById('nuevo-cli-telefono').value.trim();
    const direccion = document.getElementById('nuevo-cli-direccion').value.trim();

    if (!nombre) {
      Swal.fire({ icon: 'warning', title: 'Atención', text: 'El nombre del cliente es obligatorio.', confirmButtonColor: '#206bc4' });
      return;
    }

    const btnGuardar = document.getElementById('btn-guardar-cliente');
    btnGuardar.disabled = true;
    btnGuardar.innerText = 'Guardando...';

    // Se asume la ruta estándar del controlador de clientes para inserción asíncrona
    fetch('<?= BASE_URL ?>/clientes/guardarAjax', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nombre, telefono, direccion })
    })
    .then(res => res.json())
    .then(data => {
      btnGuardar.disabled = false;
      btnGuardar.innerText = 'Guardar Cliente';

      if (!data.success) {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo registrar el cliente.', confirmButtonColor: '#d63939' });
        return;
      }

      // Cerrar modal
      const modalEl = document.getElementById('modal-nuevo-cliente-pos');
      const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
      modalInstance.hide();
      document.getElementById('form-nuevo-cliente-pos').reset();

      // Agregar el nuevo cliente al select de forma dinámica y seleccionarlo
      const select = document.getElementById('select-cliente-pos');
      const nuevoId = data.cliente_id;
      const nuevoSaldo = 0.00;

      const opt = document.createElement('option');
      opt.value = nuevoId;
      opt.setAttribute('data-nombre', nombre);
      opt.setAttribute('data-telefono', telefono);
      opt.setAttribute('data-saldo', nuevoSaldo);
      opt.text = `${nombre} (📞 ${telefono || 'Sin telf'}) - Saldo Favor: $0.00`;
      
      select.appendChild(opt);
      select.value = nuevoId;
      seleccionarCliente(select);

      Swal.fire({
        icon: 'success',
        title: '¡Cliente Registrado!',
        text: 'El cliente ha sido agregado y seleccionado exitosamente.',
        timer: 1800,
        showConfirmButton: false
      });
    })
    .catch(err => {
      console.error(err);
      btnGuardar.disabled = false;
      btnGuardar.innerText = 'Guardar Cliente';
      Swal.fire({ icon: 'error', title: 'Error de Red', text: 'Ocurrió un error al comunicarse con el servidor.', confirmButtonColor: '#d63939' });
    });
  }

  function importarCitaACarrito(cita) {
    if (citaAsociadaId) {
      Swal.fire({
        icon: 'warning',
        title: 'Atención',
        text: 'Ya se ha cargado una cita en este carrito.',
        confirmButtonColor: '#206bc4'
      });
      return;
    }
    const selectCli = document.getElementById('select-cliente-pos');
    selectCli.value = cita.cliente_id;
    seleccionarCliente(selectCli);

    citaAsociadaId = cita.id;
    
    carritoItems.push({
      tipo: 'servicio',
      id: cita.servicio_id,
      nombre: `Cita: ${cita.servicio_nombre} (${cita.cliente_nombre})`,
      precio_unitario_usd: parseFloat(cita.precio_usd),
      cantidad: 1,
      stockMax: 9999
    });
    renderizarCarrito();
  }

  function agregarItemCarrito(tipo, id, nombre, precio, stockMax) {
    const index = carritoItems.findIndex(i => i.tipo === tipo && i.id === id);
    if (index !== -1) {
      if (tipo === 'producto' && carritoItems[index].cantidad >= stockMax) {
        Swal.fire({
          icon: 'warning',
          title: 'Stock Límite',
          text: 'Stock máximo alcanzado.',
          confirmButtonColor: '#206bc4'
        });
        return;
      }
      carritoItems[index].cantidad++;
    } else {
      carritoItems.push({
        tipo: tipo,
        id: id,
        nombre: nombre,
        precio_unitario_usd: parseFloat(precio),
        cantidad: 1,
        stockMax: stockMax
      });
    }
    renderizarCarrito();
  }

  function cambiarPrecioItem(index, nuevoPrecio) {
    const precio = parseFloat(nuevoPrecio);
    if (!isNaN(precio) && precio >= 0) {
      carritoItems[index].precio_unitario_usd = precio;
      recalcularCarrito();
      const subtotalLinea = carritoItems[index].cantidad * precio;
      const subtotalEl = document.getElementById(`subtotal-linea-${index}`);
      if (subtotalEl) {
        subtotalEl.innerText = `$${subtotalLinea.toFixed(2)}`;
      }
    }
  }

  function cambiarCantidadItem(index, delta) {
    carritoItems[index].cantidad += delta;
    if (carritoItems[index].cantidad <= 0) {
      carritoItems.splice(index, 1);
      if (index === 0 && citaAsociadaId) { citaAsociadaId = null; }
    } else if (carritoItems[index].tipo === 'producto' && carritoItems[index].cantidad > carritoItems[index].stockMax) {
      Swal.fire({
        icon: 'warning',
        title: 'Stock Insuficiente',
        text: 'No hay suficiente stock disponible.',
        confirmButtonColor: '#206bc4'
      });
      carritoItems[index].cantidad = carritoItems[index].stockMax;
    }
    renderizarCarrito();
  }

  function removerItemCarrito(index) {
    carritoItems.splice(index, 1);
    if (index === 0) { citaAsociadaId = null; }
    renderizarCarrito();
  }

  function renderizarCarrito() {
    const tbody = document.getElementById('tbody-carrito-items');
    document.getElementById('badge-contador-items').innerText = `${carritoItems.length} Ítems`;

    if (carritoItems.length === 0) {
      tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-muted small">El carrito está vacío. Agregue productos o servicios.</td></tr>`;
      recalcularCarrito();
      return;
    }

    let html = '';
    carritoItems.forEach((item, index) => {
      const subtotal = item.cantidad * item.precio_unitario_usd;
      html += `
        <tr>
          <td>
            <div class="fw-bold text-dark text-truncate" style="max-width: 120px;" title="${item.nombre}">${item.nombre}</div>
            <div class="input-group input-group-xs mt-1" style="width: 90px;" title="Modificar precio unitario">
              <span class="input-group-text px-1">$</span>
              <input type="number" step="0.01" min="0" class="form-control px-1 text-xs" value="${item.precio_unitario_usd.toFixed(2)}" oninput="cambiarPrecioItem(${index}, this.value)">
            </div>
          </td>
          <td>
            <div class="input-group input-group-sm" style="width: 75px;">
              <button class="btn btn-outline-secondary px-1" type="button" onclick="cambiarCantidadItem(${index}, -1)">-</button>
              <input type="text" class="form-control text-center px-0 bg-white" value="${item.cantidad}" readonly>
              <button class="btn btn-outline-secondary px-1" type="button" onclick="cambiarCantidadItem(${index}, 1)">+</button>
            </div>
          </td>
          <td class="text-end fw-bold" id="subtotal-linea-${index}">$${subtotal.toFixed(2)}</td>
          <td class="text-center">
            <button type="button" class="btn btn-icon btn-xs btn-outline-danger" onclick="removerItemCarrito(${index})">×</button>
          </td>
        </tr>
      `;
    });
    tbody.innerHTML = html;
    recalcularCarrito();
  }

  function cambiarModalidadVenta() {
    const tipo = document.getElementById('select-tipo-venta').value;
    const contenedorCashea = document.getElementById('contenedor-cashea-config');
    const contenedorCredito = document.getElementById('contenedor-credito-config');
    const lblTitulo = document.getElementById('lbl-titulo-total-pagar');
    const lblRestanteTitulo = document.getElementById('lbl-titulo-restante');

    contenedorCashea.classList.add('d-none');
    contenedorCredito.classList.add('d-none');
    document.getElementById('input-inicial-cashea').value = '';
    document.getElementById('input-abonado-credito').value = '';

    if (tipo === 'cashea') {
      contenedorCashea.classList.remove('d-none');
      lblTitulo.innerText = 'Monto Inicial Cashea (Caja):';
      lblRestanteTitulo.innerText = 'Restante a Pagar de la Inicial:';
    } else if (tipo === 'credito') {
      contenedorCredito.classList.remove('d-none');
      lblTitulo.innerText = 'Abono Inicial en Caja:';
      lblRestanteTitulo.innerText = 'Restante del Abono en Caja:';
    } else {
      lblTitulo.innerText = 'Total a Pagar en Caja:';
      lblRestanteTitulo.innerText = 'Restante a Pagar en Caja:';
    }
    recalcularCarrito();
  }

  function recalcularCarrito() {
    let subtotal = 0;
    carritoItems.forEach(i => { subtotal += i.cantidad * i.precio_unitario_usd; });

    const pctDescuento = parseFloat(document.getElementById('select-descuento-pos').value) || 0;
    const descuentoUsd = (subtotal * pctDescuento) / 100;
    const totalGeneral = Math.max(0, subtotal - descuentoUsd);

    const tipoVenta = document.getElementById('select-tipo-venta').value;
    let montoACobrarCaja = totalGeneral;

    if (tipoVenta === 'cashea') {
      montoACobrarCaja = parseFloat(document.getElementById('input-inicial-cashea').value) || 0;
    } else if (tipoVenta === 'credito') {
      montoACobrarCaja = parseFloat(document.getElementById('input-abonado-credito').value) || 0;
    }

    document.getElementById('lbl-subtotal-resumen').innerText = `$${subtotal.toFixed(2)}`;
    document.getElementById('lbl-descuento-resumen').innerText = `-$${descuentoUsd.toFixed(2)}`;
    document.getElementById('lbl-total-pagar-resumen').innerText = `$${montoACobrarCaja.toFixed(2)}`;
    
    const eqBs = montoACobrarCaja * tasaActualBCV;
    document.getElementById('lbl-total-bcv-resumen').innerText = `Eq. Bs. ${eqBs.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (Tasa BCV: ${tasaActualBCV})`;

    calcularTotalPagosIngresados();
  }

  function agregarLineaPagoPos() {
    const tbody = document.getElementById('tbody-pagos-pos');
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="p-1">
        <select class="form-select form-select-xs metodo-pago-select" onchange="calcularTotalPagosIngresados()">
          <option value="efectivo_usd">Efectivo USD ($)</option>
          <option value="efectivo_bs">Efectivo Bs</option>
          <option value="pago_movil">Pago Móvil (Bs)</option>
          <option value="punto_venta">Punto de Venta (Bs)</option>
          <option value="zelle">Zelle ($)</option>
          <option value="usdt">USDT ($)</option>
        </select>
      </td>
      <td class="p-1" style="width: 100px;">
        <input type="number" step="0.01" min="0.01" class="form-control form-control-xs monto-pago-input" placeholder="0.00" oninput="calcularTotalPagosIngresados()">
      </td>
      <td class="p-1" style="width: 90px;">
        <input type="text" class="form-control form-control-xs ref-pago-input" placeholder="Ref">
      </td>
      <td class="p-1 text-center" style="width: 30px;">
        <button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove(); calcularTotalPagosIngresados();">×</button>
      </td>
    `;
    tbody.appendChild(tr);
  }

  function calcularTotalPagosIngresados() {
    const filas = document.querySelectorAll('#tbody-pagos-pos tr');
    let totalEqUsd = 0;

    filas.forEach(fila => {
      const metodo = fila.querySelector('.metodo-pago-select').value;
      const monto = parseFloat(fila.querySelector('.monto-pago-input').value) || 0;
      const metodosEnBs = ['efectivo_bs', 'pago_movil', 'punto_venta'];
      
      let eq = metodosEnBs.includes(metodo) ? (tasaActualBCV > 0 ? monto / tasaActualBCV : 0) : monto;
      totalEqUsd += eq;
    });

    document.getElementById('lbl-total-ingresado-pagos').innerText = `$${totalEqUsd.toFixed(2)}`;

    let subtotal = 0;
    carritoItems.forEach(i => { subtotal += i.cantidad * i.precio_unitario_usd; });
    const pctDescuento = parseFloat(document.getElementById('select-descuento-pos').value) || 0;
    const descuentoUsd = (subtotal * pctDescuento) / 100;
    const totalGeneral = Math.max(0, subtotal - descuentoUsd);

    const tipoVenta = document.getElementById('select-tipo-venta').value;
    let montoACobrarCaja = totalGeneral;

    if (tipoVenta === 'cashea') {
      montoACobrarCaja = parseFloat(document.getElementById('input-inicial-cashea').value) || 0;
    } else if (tipoVenta === 'credito') {
      montoACobrarCaja = parseFloat(document.getElementById('input-abonado-credito').value) || 0;
    }

    let restanteUsd = Math.max(0, montoACobrarCaja - totalEqUsd);
    let restanteBs = restanteUsd * tasaActualBCV;

    document.getElementById('lbl-restante-usd').innerText = `$${restanteUsd.toFixed(2)}`;
    document.getElementById('lbl-restante-bs').innerText = `Eq. Bs. ${restanteBs.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }

  function filtrarProductosCatalogo(query) {
    query = query.toLowerCase().trim();
    document.querySelectorAll('.item-catalogo-prod').forEach(row => {
      const nombre = row.getAttribute('data-nombre');
      row.style.display = nombre.includes(query) ? '' : 'none';
    });
  }

  function procesarVentaPos() {
    if (!clienteSeleccionadoId) {
      Swal.fire({
        icon: 'warning',
        title: 'Cliente Requerido',
        text: 'Por favor seleccione un cliente antes de procesar la venta.',
        confirmButtonColor: '#206bc4'
      });
      return;
    }
    if (carritoItems.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Carrito Vacío',
        text: 'El carrito está vacío.',
        confirmButtonColor: '#206bc4'
      });
      return;
    }

    const tipoVenta = document.getElementById('select-tipo-venta').value;
    let inicialCashea = 0;
    let abonadoCredito = 0;
    
    let subtotal = 0;
    carritoItems.forEach(i => { subtotal += i.cantidad * i.precio_unitario_usd; });
    const pctDescuento = parseFloat(document.getElementById('select-descuento-pos').value) || 0;
    const totalGeneral = Math.max(0, subtotal - ((subtotal * pctDescuento) / 100));

    if (tipoVenta === 'cashea') {
      inicialCashea = parseFloat(document.getElementById('input-inicial-cashea').value) || 0;
      if (inicialCashea <= 0 || inicialCashea >= totalGeneral) {
        Swal.fire({
          icon: 'warning',
          title: 'Monto Inicial Inválido',
          text: 'La inicial de Cashea debe ser mayor a 0 y menor al total de la compra.',
          confirmButtonColor: '#206bc4'
        });
        return;
      }
    } else if (tipoVenta === 'credito') {
      abonadoCredito = parseFloat(document.getElementById('input-abonado-credito').value) || 0;
      if (abonadoCredito < 0 || abonadoCredito >= totalGeneral) {
        Swal.fire({
          icon: 'warning',
          title: 'Abono Inválido',
          text: 'El abono inicial para crédito debe ser menor al total general (si es 100%, use Venta Regular).',
          confirmButtonColor: '#206bc4'
        });
        return;
      }
    }

    // Recopilar y sumar los pagos ingresados
    const filasPagos = document.querySelectorAll('#tbody-pagos-pos tr');
    let pagosArr = [];
    let totalPagadoEqUsd = 0;

    filasPagos.forEach(fila => {
      const metodo = fila.querySelector('.metodo-pago-select').value;
      const monto = parseFloat(fila.querySelector('.monto-pago-input').value) || 0;
      const ref = fila.querySelector('.ref-pago-input').value.trim();
      
      if (monto > 0) {
        const metodosEnBs = ['efectivo_bs', 'pago_movil', 'punto_venta'];
        let eq = metodosEnBs.includes(metodo) ? (tasaActualBCV > 0 ? monto / tasaActualBCV : 0) : monto;
        
        totalPagadoEqUsd += eq;
        pagosArr.push({ metodo: metodo, monto: monto, referencia: ref });
      }
    });

    // Definir el monto exacto requerido en caja según la modalidad
    const montoRequeridoCaja = (tipoVenta === 'cashea') ? inicialCashea : ((tipoVenta === 'credito') ? abonadoCredito : totalGeneral);

    // VALIDACIÓN ESTRICTA: Si se requiere dinero en caja, el total pagado debe cubrirlo exactamente (con tolerancia decimal mínima)
    if (montoRequeridoCaja > 0) {
      if (pagosArr.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Pagos Requeridos',
          text: 'Debe registrar al menos una forma de pago para cubrir el monto requerido en caja.',
          confirmButtonColor: '#206bc4'
        });
        return;
      }

      if (Math.round(totalPagadoEqUsd * 100) < Math.round(montoRequeridoCaja * 100)) {
        const faltante = montoRequeridoCaja - totalPagadoEqUsd;
        Swal.fire({
          icon: 'warning',
          title: 'Pago Insuficiente',
          text: `El total pagado en caja ($${totalPagadoEqUsd.toFixed(2)}) no cubre el monto requerido ($${montoRequeridoCaja.toFixed(2)}). Faltan $${faltante.toFixed(2)}.`,
          confirmButtonColor: '#206bc4'
        });
        return;
      }
    }

    const payload = {
      cliente_id: clienteSeleccionadoId,
      cita_id: citaAsociadaId,
      tipo_venta: tipoVenta,
      porcentaje_descuento: pctDescuento,
      monto_inicial_cashea_usd: inicialCashea,
      monto_abonado_credito_usd: abonadoCredito,
      items: carritoItems,
      pagos: pagosArr
    };

    fetch('<?= BASE_URL ?>/ventas/procesarVenta', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        Swal.fire({
          icon: 'error',
          title: 'Error al Procesar',
          text: data.message,
          confirmButtonColor: '#d63939'
        });
        return;
      }
      window.location.href = data.redirect_url;
    })
    .catch(err => {
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'Error de Red',
        text: 'Ocurrió un error al procesar la venta en el servidor.',
        confirmButtonColor: '#d63939'
      });
    });
  }
</script>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>