<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">Reporte de Cuenta por Cobrar / Deuda Pendiente</h2>
        <div class="text-muted small">Estado de cuenta detallado de créditos pendientes y abonos aplicados</div>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <button type="button" class="btn btn-primary d-flex align-items-center gap-1" onclick="window.print();">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9v-2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v2" /><path d="M6 18h12v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2z" /><path d="M6 14h12v-4h-12z" /></svg>
          Imprimir / Guardar PDF
        </button>
        <a href="<?= BASE_URL ?>/creditos?cliente_id=<?= $cliente['id'] ?>" class="btn btn-outline-secondary ms-2">Volver</a>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card card-lg border-0 shadow-sm p-4">
      
      <!-- Cabecera del Reporte -->
      <div class="row align-items-center mb-4 border-bottom pb-3">
        <div class="col-6">
          <h1 class="h3 fw-bold text-dark mb-1">ANA COSMETICS & SALON</h1>
          <div class="text-muted small">Reporte Oficial de Cuentas por Cobrar</div>
        </div>
        <div class="col-6 text-end">
          <div class="text-muted small">Fecha de Emisión: <strong><?= date('d/m/Y H:i') ?></strong></div>
          <div class="text-muted small">Tasa BCV del Día: <strong>Bs. <?= number_format($tasaUsd, 2, ',', '.') ?></strong></div>
        </div>
      </div>

      <!-- Datos del Cliente -->
      <div class="row mb-4 bg-light-subtle p-3 rounded border">
        <div class="col-12 col-md-4">
          <span class="text-muted small d-block">Cliente:</span>
          <strong class="text-dark fs-3"><?= htmlspecialchars($cliente['nombre']) ?></strong>
        </div>
        <div class="col-6 col-md-4">
          <span class="text-muted small d-block">Teléfono:</span>
          <strong><?= htmlspecialchars($cliente['telefono'] ?? 'Sin teléfono') ?></strong>
        </div>
        <div class="col-6 col-md-4 text-md-end">
          <span class="text-muted small d-block">Saldo a Favor Disponible:</span>
          <strong class="text-success fs-3">$<?= number_format($cliente['saldo_favor_usd'], 2) ?></strong>
        </div>
      </div>

      <!-- Listado de Créditos Pendientes -->
      <h3 class="mb-3 text-dark fw-bold">Créditos y Deudas Pendientes</h3>
      
      <?php if (!empty($creditosPendientes)): ?>
        <?php 
          $deudaTotalGeneral = 0;
        ?>
        <?php foreach ($creditosPendientes as $cred): ?>
          <?php 
            $deudaTotalGeneral += floatval($cred['saldo_pendiente_usd']); 
            $idCreditoFmt = str_pad($cred['id'], 6, '0', STR_PAD_LEFT);
          ?>
          <div class="card border mb-3 shadow-none">
            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
              <div>
                <span class="fw-bold">Venta Crédito #<?= $idCreditoFmt ?></span>
                <span class="text-muted small ms-3">📅 Fecha: <?= date('d/m/Y H:i', strtotime($cred['created_at'])) ?></span>
              </div>
              <div class="text-danger fw-bold">
                Saldo Pendiente: $<?= number_format($cred['saldo_pendiente_usd'], 2) ?>
              </div>
            </div>
            
            <div class="card-body p-3">
              <!-- Detalles de productos/servicios de la venta -->
              <div class="mb-3">
                <span class="text-uppercase text-xs fw-bold text-muted d-block mb-1">Ítems de la Venta:</span>
                <table class="table table-sm table-bordered mb-2">
                  <thead class="bg-light">
                    <tr>
                      <th>Descripción / Ítem</th>
                      <th class="text-center" style="width: 80px;">Cant.</th>
                      <th class="text-end" style="width: 100px;">P. Unit</th>
                      <th class="text-end" style="width: 100px;">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($cred['detalles'] as $det): ?>
                      <tr>
                        <td><?= htmlspecialchars($det['item_nombre'] ?? 'Artículo') ?></td>
                        <td class="text-center"><?= $det['cantidad'] ?></td>
                        <td class="text-end">$<?= number_format($det['precio_unitario_usd'], 2) ?></td>
                        <td class="text-end">$<?= number_format($det['subtotal_usd'], 2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <div class="text-end small">
                  <strong>Total Venta:</strong> $<?= number_format($cred['total_general_usd'], 2) ?> | 
                  <strong class="text-danger">Pendiente: $<?= number_format($cred['saldo_pendiente_usd'], 2) ?></strong>
                </div>
              </div>

              <!-- Abonos realizados a este crédito -->
              <?php if (!empty($cred['abonos'])): ?>
                <div>
                  <span class="text-uppercase text-xs fw-bold text-muted d-block mb-1">Abonos Registrados a este Crédito:</span>
                  <table class="table table-sm table-striped border mb-0">
                    <thead class="bg-light-subtle">
                      <tr>
                        <th>Fecha Abono</th>
                        <th>Método(s) de Pago / Ref</th>
                        <th class="text-end">Monto Abonado (USD)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($cred['abonos'] as $ab): ?>
                        <tr>
                          <td><?= date('d/m/Y H:i', strtotime($ab['created_at'])) ?></td>
                          <td>
                            <?php if (!empty($ab['detalles_pagos'])): ?>
                              <?php foreach ($ab['detalles_pagos'] as $detP): ?>
                                <div><span class="badge bg-secondary-lt text-uppercase"><?= $detP['metodo_pago'] ?></span> Ref: <?= $detP['referencia'] ?: '---' ?> (Orig: <?= $detP['monto_ingresado'] ?>)</div>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <?= $ab['metodo_pago'] ?> (Ref: <?= $ab['referencia'] ?: '---' ?>)
                            <?php endif; ?>
                          </td>
                          <td class="text-end fw-bold text-success">+$<?= number_format($ab['monto_abono_usd'], 2) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php else: ?>
                <div class="text-muted small fst-italic">No registra abonos previos para este crédito.</div>
              <?php endif; ?>

            </div>
          </div>
        <?php endforeach; ?>

        <!-- Resumen Total Deuda Pendiente -->
        <div class="card bg-light border p-3 mt-4 text-end">
          <span class="text-muted small text-uppercase fw-bold">Deuda Total Acumulada Pendiente:</span>
          <span class="fs-1 fw-extrabold text-danger">$<?= number_format($deudaTotalGeneral, 2) ?></span>
          <div class="text-muted text-xs">Equivalente aprox. Bs. <?= number_format($deudaTotalGeneral * $tasaUsd, 2, ',', '.') ?></div>
        </div>

      <?php else: ?>
        <div class="text-center py-5 text-muted border rounded bg-light-subtle">
          <h4>¡Excelente noticia!</h4>
          <p class="mb-0">Este cliente no registra deudas ni créditos pendientes de pago en el sistema.</p>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>