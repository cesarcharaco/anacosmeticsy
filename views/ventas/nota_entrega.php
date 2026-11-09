<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<!-- Estilos exclusivos para modo impresión y presentación -->
<style>
  @media print {
    body * {
      visibility: hidden;
    }
    #printable-area, #printable-area * {
      visibility: visible;
    }
    #printable-area {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      margin: 0;
      padding: 15px;
      box-shadow: none !important;
    }
    .d-print-none {
      display: none !important;
    }
  }
</style>

<div class="page-header d-print-none mb-3">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Comprobante de Salida y Caja</h2>
        <div class="text-muted mt-1">Nota de Entrega #[<?= $venta['id'] ?>]</div>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <button type="button" class="btn btn-primary" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
            Imprimir / Guardar PDF
          </button>
          <a href="/anacosmeticsy/public/ventas" class="btn btn-outline-secondary">
            Nueva Venta (POS)
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    
    <!-- Contenedor Imprimible -->
    <div class="card card-lg shadow-sm border-0 mx-auto" id="printable-area" style="max-width: 800px;">
      <div class="card-body m-2">
        
        <!-- Cabecera de la Empresa (Datos ficticios listos para reemplazar) -->
        <div class="row mb-4">
          <div class="col-6">
            <h3 class="text-primary fw-bold mb-1"> ANA BOUTIQUE COSMETICS, C.A</h3>
            <div class="text-muted small">
              RIF: J-50836569-0<br>
               CALLE BOLIVAR C/C PAEZ CASA  NRO S/N SECTOR CENTRO SAN JOSE DE GUARIBE GUARICO ZONA POSTAL 2323<br>
              Teléfono: +58 (414)-6949682<br>
              Correo: anacosmeticoficial@gmail.com
            </div>
          </div>
          <div class="col-6 text-end">
            <h1 class="h3 fw-bold text-dark mb-1">NOTA DE ENTREGA</h1>
            <div class="text-muted small">
              <strong>Nro:</strong> #<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?><br>
              <strong>Fecha:</strong> <?= date('d/m/Y h:i A', strtotime($venta['created_at'])) ?><br>
              <strong>Cajero(a):</strong> <?= htmlspecialchars($venta['usuario_nombre']) ?><br>
              <strong>Tasa BCV:</strong> Bs. <?= number_format($venta['tasa_bcv'], 4, ',', '.') ?>
            </div>
          </div>
        </div>

        <!-- Datos del Cliente -->
        <div class="card bg-light-subtle border mb-4 p-3 rounded-2">
          <div class="row">
            <div class="col-12">
              <span class="text-uppercase text-muted fs-10 fw-bold d-block mb-1">Datos del Cliente Facturado</span>
              <div class="fw-bold fs-3 text-dark"><?= htmlspecialchars($venta['cliente_nombre']) ?></div>
              <div class="text-muted small">
                📞 Teléfono: <?= htmlspecialchars($venta['cliente_telefono'] ?? 'N/D') ?> | 
                📍 Dirección: <?= htmlspecialchars($venta['cliente_direccion'] ?? 'No especificada') ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de Ítems (Productos / Servicios) -->
        <div class="table-responsive mb-4">
          <table class="table table-bordered table-sm card-table align-middle">
            <thead class="bg-light text-dark">
              <tr>
                <th class="py-2">Cant</th>
                <th class="py-2">Descripción del Ítem</th>
                <th class="py-2 text-center">Tipo</th>
                <th class="py-2 text-end">Precio Unit. ($)</th>
                <th class="py-2 text-end">Subtotal ($)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($venta['detalles'] as $det): ?>
                <tr>
                  <td class="text-center fw-bold"><?= $det['cantidad'] ?></td>
                  <td><?= htmlspecialchars($det['item_nombre'] ?? 'Ítem general') ?></td>
                  <td class="text-center">
                    <span class="badge <?= $det['tipo_item'] == 'producto' ? 'bg-blue-lt' : 'bg-purple-lt' ?>">
                      <?= ucfirst($det['tipo_item']) ?>
                    </span>
                  </td>
                  <td class="text-end">$<?= number_format($det['precio_unitario_usd'], 2) ?></td>
                  <td class="text-end fw-bold">$<?= number_format($det['subtotal_usd'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Totales y Resumen Financiero -->
        <div class="row mb-4">
          <div class="col-7">
            <!-- Modalidad de Pago / Cashea -->
            <div class="border rounded p-3 bg-light-subtle">
              <div class="fw-bold small mb-1">Modalidad de Transacción:</div>
              <?php if ($venta['tipo_venta'] === 'cashea'): ?>
                <span class="badge bg-yellow text-dark fw-bold mb-2">Financiamiento Cashea Aplicado</span>
                <div class="small text-muted">
                  <div>Monto Inicial Pagado en Caja: <strong>$<?= number_format($venta['monto_inicial_cashea_usd'], 2) ?></strong></div>
                  <div>Monto Financiado (Cashea): <strong>$<?= number_format($venta['monto_financiado_cashea_usd'], 2) ?></strong></div>
                </div>
              <?php else: ?>
                <span class="badge bg-green-lt fw-bold mb-2">Venta Regular (100% Contado)</span>
                <div class="small text-muted">Liquidado en su totalidad en caja.</div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="col-5">
            <table class="table table-sm table-borderless text-end mb-0">
              <tr>
                <td class="text-muted">Subtotal General:</td>
                <td class="fw-bold">$<?= number_format($venta['subtotal_usd'], 2) ?></td>
              </tr>
              <?php if ($venta['descuento_usd'] > 0): ?>
                <tr>
                  <td class="text-danger">Descuento Aplicado:</td>
                  <td class="text-danger fw-bold">-$<?= number_format($venta['descuento_usd'], 2) ?></td>
                </tr>
              <?php endif; ?>
              <tr class="border-top">
                <td class="fw-bold text-dark pt-2">Total General:</td>
                <td class="fw-bold text-dark fs-3 pt-2">$<?= number_format($venta['total_general_usd'], 2) ?></td>
              </tr>
              <?php if ($venta['tipo_venta'] === 'cashea'): ?>
                <tr class="bg-yellow-lt">
                  <td class="fw-bold text-dark py-1">Total a Pagar en Caja (Inicial):</td>
                  <td class="fw-bold text-success py-1">$<?= number_format($venta['monto_inicial_cashea_usd'], 2) ?></td>
                </tr>
              <?php endif; ?>
            </table>
          </div>
        </div>

        <!-- Formas de Pago Aplicadas -->
        <div class="mb-4">
          <h4 class="fw-bold small text-uppercase text-muted mb-2">Métodos de Pago Registrados</h4>
          <div class="table-responsive">
            <table class="table table-sm table-bordered card-table">
              <thead class="bg-light">
                <tr>
                  <th>Método</th>
                  <th class="text-end">Monto Ingresado</th>
                  <th class="text-end">Equivalente USD</th>
                  <th>Referencia</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($venta['pagos'] as $pago): ?>
                  <tr>
                    <td><strong><?= ucwords(str_replace('_', ' ', $pago['metodo_pago'])) ?></strong></td>
                    <td class="text-end">
                      <?php if (in_array($pago['metodo_pago'], ['efectivo_bs', 'pago_movil', 'punto_venta'])): ?>
                        Bs. <?= number_format($pago['monto_ingresado'], 2, ',', '.') ?>
                      <?php else: ?>
                        $<?= number_format($pago['monto_ingresado'], 2) ?>
                      <?php endif; ?>
                    </td>
                    <td class="text-end fw-bold text-success">$<?= number_format($pago['monto_equivalente_usd'], 2) ?></td>
                    <td><?= htmlspecialchars($pago['referencia'] ?: 'N/A') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pie de Página / Agradecimiento -->
        <div class="text-center text-muted small border-top pt-3 mt-4">
          <p class="mb-1">¡Gracias por preferirnos! Conserve este comprobante para cualquier cambio o garantía.</p>
          <p class="mb-0 text-xs">ANA BOUTIQUE COSMETICS, C.A — Sistema POS Integrado</p>
        </div>

      </div>
    </div>

  </div>
</div>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>