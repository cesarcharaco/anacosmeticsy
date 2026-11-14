<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      
      <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-green-lt text-green font-weight-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck-delivery" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 a2 2 0 1 0 4 0 a2 2 0 1 0 -4 0" /><path d="M17 17m-2 a2 2 0 1 0 4 0 a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
          </span>
          <div>
            <h2 class="page-title mb-0 fs-2">Actualizar Inventario</h2>
            <div class="text-muted small">Carga rápida de mercancía entrante</div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-auto ms-md-auto d-flex gap-2">
        <a href="<?= BASE_URL ?>/inventario" class="btn btn-outline-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
          Volver al Inventario
        </a>
      </div>

    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    
    <form action="<?= BASE_URL ?>/inventario/procesarActualizacion" method="POST" id="form-recepcion">
      
      <div class="row row-cards">
        
        <!-- LADO IZQUIERDO: Buscador y Filtro Rápidos -->
        <div class="col-12 col-lg-4">
          <div class="card shadow-sm border-0 rounded-3 mb-3">
            <div class="card-body">
              <h3 class="card-title fw-bold mb-3">1. Seleccionar Producto</h3>
              
              <div class="mb-3">
                <div class="mb-3 position-relative">
                <label class="form-label small text-muted text-uppercase fw-bold">Buscar por Código o Nombre</label>
                <div class="input-icon">
                  <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -4 0" /><path d="M21 21l-6 -6" /></svg>
                  </span>
                  <input type="text" id="input-buscador-producto" class="form-control" placeholder="Escribe el nombre o SKU del producto..." autocomplete="off" oninput="filtrarProductos(this.value)">
                </div>

                <!-- Lista flotante de resultados dinámicos -->
                <div id="lista-resultados-productos" class="dropdown-menu w-100 shadow-lg border-0 mt-1 p-0" style="max-height: 280px; overflow-y: auto; display: none; position: absolute; z-index: 1050;">
                  <!-- Los resultados se inyectan dinámicamente aquí -->
                </div>
              </div>
              </div>

              <div class="hr-text text-muted my-3">ó si es un producto nuevo</div>

              <button type="button" class="btn btn-outline-success w-100 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#modal-producto" onclick="abrirModalNuevo()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                + Registrar Producto Nuevo
              </button>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
              <h3 class="card-title fw-bold mb-2">2. Notas del Lote</h3>
              <div class="mb-0">
                <textarea name="observaciones" class="form-control" rows="3" placeholder="Ej. Mercancía traída en viaje / Lote semanal de reposición (Opcional)..."></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- LADO DERECHO: Lista en Lote de Carga -->
        <div class="col-12 col-lg-8">
          <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
              <h3 class="card-title fw-bold mb-0">Detalle de Recepción de Mercancía</h3>
              <span class="badge bg-purple-lt fs-4" id="cant-items">0 Ítems</span>
            </div>

            <div class="table-responsive">
              <table class="table table-vcenter card-table align-middle" id="tabla-recepcion">
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th style="width: 110px;">Cant. Entrante</th>
                    <th style="width: 140px;">Costo Nuevo ($)</th>
                    <th style="width: 180px;">Actualizar Precio</th>
                    <th class="text-end" style="width: 50px;"></th>
                  </tr>
                </thead>
                <tbody id="contenedor-items">
                  <!-- Fila vacía inicial -->
                  <tr id="row-empty">
                    <td colspan="5" class="text-center py-5 text-muted">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package-off text-muted mb-2" width="40" height="40" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8.812 4.793l3.188 -1.793l8 4.5v8.5m-2.282 1.718l-5.718 3.282l-8 -4.5v-9l3.018 -1.698" /><path d="M12 12v9" /><path d="M12 12l8 -4.5" /><path d="M12 12l-8 -4.5" /><path d="M3 3l18 18" /></svg>
                      <div>No has agregado productos a esta recepción.</div>
                      <small class="text-muted">Selecciona un producto a la izquierda para comenzar.</small>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="card-footer bg-light d-flex justify-content-end gap-2 p-3">
              <a href="<?= BASE_URL ?>/inventario" class="btn btn-link link-secondary">Cancelar</a>
              <button type="submit" class="btn btn-success px-4 shadow-sm" id="btn-guardar-lote" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                Procesar y Sumar al Inventario
              </button>
            </div>
          </div>
        </div>

      </div>

    </form>

  </div>
</div>

<!-- Modal Tabler de Producto Nuevo Integrado -->
<div class="modal modal-blur fade" id="modal-producto" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="<?= BASE_URL ?>/inventario/guardar" method="POST" autocomplete="off">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-dark" id="modal-titulo">Nuevo Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-3 p-md-4">
          <input type="hidden" name="id" id="prod_id">
          
          <div class="row">
            <div class="col-12 col-md-4 mb-3">
              <label class="form-label required small text-muted text-uppercase fw-bold">Código / SKU</label>
              <input type="text" name="codigo" id="prod_codigo" class="form-control" placeholder="PROD-001" required>
            </div>
            <div class="col-12 col-md-8 mb-3">
              <label class="form-label required small text-muted text-uppercase fw-bold">Nombre del Producto</label>
              <input type="text" name="nombre" id="prod_nombre" class="form-control" placeholder="Ej. Esmalte Semipermanente Nude" required>
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-md-4 mb-3">
              <label class="form-label required small text-muted text-uppercase fw-bold">Categoría</label>
              <select name="categoria_id" id="prod_categoria_id" class="form-select" required>
                <option value="">Seleccione una categoría...</option>
                <?php foreach ($categorias as $cat): ?>
                  <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-6 col-md-4 mb-3">
              <label class="form-label required small text-muted text-uppercase fw-bold">Costo ($ USD)</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" step="0.01" min="0" name="costo_usd" id="prod_costo" class="form-control" placeholder="0.00" oninput="calcularPrecioModal()" required>
              </div>
            </div>
            <div class="col-6 col-md-4 mb-3">
              <label class="form-label required small text-muted text-uppercase fw-bold">Ganancia (%)</label>
              <div class="input-group">
                <input type="number" step="1" min="0" name="margen_ganancia" id="prod_margen" class="form-control" placeholder="30" value="30" oninput="calcularPrecioModal()" required>
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>

          <div class="card bg-primary-lt border-0 my-2">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
              <div>
                <div class="small text-muted fw-bold text-uppercase">Precio de Venta</div>
                <div class="small text-secondary">Tasa BCV: Bs. <?= number_format($tasaUsd, 2, ',', '.') ?></div>
              </div>
              <div class="text-end">
                <div class="fs-2 fw-bold text-primary" id="lbl_precio_usd">$0.00 USD</div>
                <div class="fw-bold text-success small" id="lbl_precio_bs">Bs. 0,00</div>
              </div>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-6 col-md-6 mb-3">
              <label class="form-label required small text-muted text-uppercase fw-bold">Stock Inicial</label>
              <input type="number" min="0" name="stock" id="prod_stock" class="form-control" value="0" required>
            </div>
            <div class="col-6 col-md-6 mb-3">
              <label class="form-label small text-muted text-uppercase fw-bold">Stock Mínimo</label>
              <input type="number" min="1" name="stock_minimo" id="prod_stock_minimo" class="form-control" value="5">
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <input type="hidden" name="origen" value="actualizar">
          <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary px-4">Guardar Producto</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const tasaBcv = <?= (float) $tasaUsd ?>;
  let itemsAgregados = {};

  // Cargamos todos los productos en un array de JS para búsqueda instantánea en memoria (soporta miles de productos sin latencia)
  const listaProductosDisponibles = [
    <?php foreach ($productos as $p): ?>
      <?= json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
    <?php endforeach; ?>
  ];

  function filtrarProductos(query) {
    const contenedor = document.getElementById('lista-resultados-productos');
    const filtro = query.trim().toLowerCase();

    if (filtro.length === 0) {
      contenedor.style.display = 'none';
      contenedor.innerHTML = '';
      return;
    }

    // Filtrar coincidencias por SKU (código) o nombre
    const resultados = listaProductosDisponibles.filter(p => 
      p.codigo.toLowerCase().includes(filtro) || p.nombre.toLowerCase().includes(filtro)
    );

    if (resultados.length === 0) {
      contenedor.innerHTML = `<div class="p-3 text-muted text-center small">No se encontraron productos coincidentes</div>`;
      contenedor.style.display = 'block';
      return;
    }

    let html = '';
    resultados.forEach(p => {
      html += `
        <button type="button" class="dropdown-item py-2 px-3 border-bottom d-flex justify-content-between align-items-center text-wrap text-start" onclick='seleccionarProductoPorObjeto(${JSON.stringify(p)})'>
          <div>
            <div class="fw-bold text-dark">${p.nombre}</div>
            <div class="small text-muted">SKU: <strong>${p.codigo}</strong> | Stock actual: ${p.stock} unds</div>
          </div>
          <span class="badge bg-green-lt fw-bold">$${parseFloat(p.costo_usd).toFixed(2)}</span>
        </button>
      `;
    });

    contenedor.innerHTML = html;
    contenedor.style.display = 'block';
  }

  function seleccionarProductoPorObjeto(producto) {
    if (itemsAgregados[producto.id]) {
      alert("Este producto ya está agregado en la lista de carga actual.");
      document.getElementById('input-buscador-producto').value = '';
      document.getElementById('lista-resultados-productos').style.display = 'none';
      return;
    }

    agregarFilaItem(producto);
    document.getElementById('input-buscador-producto').value = '';
    document.getElementById('lista-resultados-productos').style.display = 'none';
  }

  // Ocultar la lista flotante si hace clic fuera de ella
  document.addEventListener('click', function(e) {
    if (!e.target.closest('#input-buscador-producto') && !e.target.closest('#lista-resultados-productos')) {
      const contenedor = document.getElementById('lista-resultados-productos');
      if (contenedor) contenedor.style.display = 'none';
    }
  });

  function agregarFilaItem(p) {
    const contenedor = document.getElementById('contenedor-items');
    const emptyRow = document.getElementById('row-empty');
    if (emptyRow) emptyRow.remove();

    itemsAgregados[p.id] = p;

    const tr = document.createElement('tr');
    tr.id = `item-row-${p.id}`;
    tr.innerHTML = `
      <td>
        <div class="fw-bold text-dark">${p.nombre}</div>
        <div class="small text-muted">
          SKU: <strong>${p.codigo}</strong> | Stock actual: <span class="badge bg-secondary-lt">${p.stock} unds</span>
        </div>
        <div class="text-xs text-muted mt-1">Costo anterior: <strong>$${parseFloat(p.costo_usd).toFixed(2)}</strong> (+${parseInt(p.margen_ganancia)}% ganancia)</div>
        <input type="hidden" name="items[${p.id}][producto_id]" value="${p.id}">
        <input type="hidden" name="items[${p.id}][costo_anterior]" value="${p.costo_usd}">
        <input type="hidden" name="items[${p.id}][margen_ganancia]" value="${p.margen_ganancia}">
      </td>

      <td>
        <input type="number" min="1" name="items[${p.id}][cantidad]" class="form-control form-control-sm text-center fw-bold" value="1" required>
      </td>

      <td>
        <div class="input-group input-group-sm">
          <span class="input-group-text">$</span>
          <input type="number" step="0.01" min="0" name="items[${p.id}][costo_nuevo]" class="form-control form-control-sm" value="${parseFloat(p.costo_usd).toFixed(2)}" required>
        </div>
      </td>

      <td>
        <label class="form-check form-switch mb-0">
          <input class="form-check-input" type="checkbox" name="items[${p.id}][actualizar_precio]" value="1">
          <span class="form-check-label small text-muted">Recalcular Venta</span>
        </label>
      </td>

      <td class="text-end">
        <button type="button" class="btn btn-sm btn-icon btn-ghost-danger" title="Quitar de la lista" onclick="eliminarFilaItem(${p.id})">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
        </button>
      </td>
    `;

    contenedor.appendChild(tr);
    actualizarEstadoBotonGuardar();
  }

  function eliminarFilaItem(id) {
    const tr = document.getElementById(`item-row-${id}`);
    if (tr) tr.remove();
    delete itemsAgregados[id];

    const contenedor = document.getElementById('contenedor-items');
    if (contenedor.children.length === 0) {
      contenedor.innerHTML = `
        <tr id="row-empty">
          <td colspan="5" class="text-center py-5 text-muted">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package-off text-muted mb-2" width="40" height="40" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8.812 4.793l3.188 -1.793l8 4.5v8.5m-2.282 1.718l-5.718 3.282l-8 -4.5v-9l3.018 -1.698" /><path d="M12 12v9" /><path d="M12 12l8 -4.5" /><path d="M12 12l-8 -4.5" /><path d="M3 3l18 18" /></svg>
            <div>No has agregado productos a esta recepción.</div>
            <small class="text-muted">Selecciona un producto a la izquierda para comenzar.</small>
          </td>
        </tr>
      `;
    }
    actualizarEstadoBotonGuardar();
  }

  function actualizarEstadoBotonGuardar() {
    const total = Object.keys(itemsAgregados).length;
    document.getElementById('cant-items').innerText = `${total} Ítems`;
    document.getElementById('btn-guardar-lote').disabled = total === 0;
  }

  function calcularPrecioModal() {
    const costo = parseFloat(document.getElementById('prod_costo').value) || 0;
    const margen = parseFloat(document.getElementById('prod_margen').value) || 0;
    const precioUsd = costo + (costo * (margen / 100));
    const precioBs = precioUsd * tasaBcv;

    document.getElementById('lbl_precio_usd').innerText = `$${precioUsd.toFixed(2)} USD`;
    document.getElementById('lbl_precio_bs').innerText = `Bs. ${precioBs.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
  }

  function abrirModalNuevo() {
    document.getElementById('modal-titulo').innerText = 'Nuevo Producto';
    document.getElementById('prod_id').value = '';
    document.getElementById('prod_codigo').value = '';
    document.getElementById('prod_nombre').value = '';
    document.getElementById('prod_categoria_id').value = '';
    document.getElementById('prod_costo').value = '';
    document.getElementById('prod_margen').value = '30';
    document.getElementById('prod_stock').value = '0';
    document.getElementById('prod_stock_minimo').value = '5';
    calcularPrecioModal();
  }
</script>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>