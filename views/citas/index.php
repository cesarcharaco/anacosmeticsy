<?php require_once BASE_PATH . 'views/layouts/header.php'; ?>

<style>
  .calendar-header { display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: 600; padding: 0.5rem 0; background: #f8fafc; border-radius: 6px; }
  .calendar-body { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-top: 6px; }
  .calendar-day { min-height: 95px; border: 1px solid #e6e8e9; border-radius: 8px; padding: 6px; cursor: pointer; transition: all 0.2s ease; background-color: #fff; display: flex; flex-direction: column; justify-content: space-between; position: relative; }
  .calendar-day:hover { background-color: #f1f5f9; border-color: #cbd5e1; transform: translateY(-1px); }
  .calendar-day.other-month { background-color: #fafafa; opacity: 0.35; }
  .calendar-day.today { border: 2px solid #206bc4; background-color: #f0f6ff; }
  .calendar-day.has-citas { background-color: #fffafd; border-color: #f6ceed; }
  .calendar-day .day-number { font-weight: bold; font-size: 0.85rem; color: #495057; }
  
  /* Contenedor de insignias múltiples de estado en el día */
  .badges-citas-container {
    display: flex;
    gap: 3px;
    justify-content: flex-end;
    flex-wrap: wrap;
    margin-top: auto;
  }
  .badge-estado-cita {
    font-size: 0.65rem;
    font-weight: bold;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    box-shadow: 0 1px 2px rgba(0,0,0,0.15);
  }
  .badge-pendiente { background-color: #f59f00; } /* Naranja */
  .badge-realizada { background-color: #2fb344; } /* Verde */
  .badge-cancelada { background-color: #d63939; } /* Rojo */

  @media (max-width: 576px) {
    .calendar-day { min-height: 70px; padding: 4px; }
    .calendar-day .day-number { font-size: 0.75rem; }
    .badge-estado-cita { width: 16px; height: 16px; font-size: 0.55rem; }
  }
</style>

<div class="page-header d-print-none mb-3 border-bottom pb-3">
  <div class="container-fluid px-3 px-lg-4">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar bg-pink-lt text-pink font-weight-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M8 15h2v2h-2z" /></svg>
          </span>
          <div>
            <h2 class="page-title mb-0 fs-2">Citas & Servicios de Salón</h2>
            <div class="text-muted small">Gestión de servicios y agenda visual de atención</div>
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
    
    <?php if (isset($_SESSION['mensaje'])): ?>
      <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <div><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <div><?= htmlspecialchars($_SESSION['error']) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row row-cards">
      
      <!-- Catálogo de Servicios -->
      <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 rounded-3 mb-3">
          <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h3 class="card-title fw-bold mb-0">Catálogo de Servicios</h3>
            <button class="btn btn-sm btn-pink d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modal-servicio">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
              Nuevo
            </button>
          </div>
          <div class="card-body p-2 border-bottom bg-light-subtle">
            <div class="input-icon">
              <span class="input-icon-addon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
              </span>
              <input type="text" id="buscador-servicios" class="form-control form-control-sm" placeholder="Buscar servicio..." onkeyup="filtrarServicios()">
            </div>
          </div>
          <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
            <table class="table table-vcenter card-table align-middle" id="tabla-servicios">
              <thead>
                <tr>
                  <th>Servicio</th>
                  <th class="text-end">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($servicios)): ?>
                  <?php foreach ($servicios as $serv): ?>
                    <tr class="fila-servicio">
                      <td>
                        <div class="fw-bold text-dark nombre-serv"><?= htmlspecialchars($serv['nombre']) ?></div>
                        <div class="small text-muted text-truncate" style="max-width: 180px;"><?= htmlspecialchars($serv['descripcion'] ?? '') ?></div>
                        <div class="text-success fw-bold mt-1">
                          $<?= number_format($serv['precio_usd'], 2) ?> 
                          <span class="text-xs text-muted fw-normal">(Bs. <?= number_format($serv['precio_usd'] * $tasaUsd, 2, ',', '.') ?>)</span>
                        </div>
                      </td>
                      <td class="text-end">
                        <div class="d-flex justify-content-end gap-1">
                          <button class="btn btn-icon btn-sm btn-outline-primary" title="Editar" onclick='abrirModalEditar(<?= json_encode($serv) ?>)'>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                          </button>
                          <button class="btn btn-icon btn-sm btn-outline-danger" title="Eliminar" onclick="abrirModalEliminar(<?= $serv['id'] ?>, '<?= htmlspecialchars($serv['nombre'], ENT_QUOTES) ?>')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="2" class="text-center py-4 text-muted small">No hay servicios registrados.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Calendario Interactivo -->
      <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0 rounded-3 mb-3">
          <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2 py-2">
            <div class="d-flex align-items-center gap-2">
              <button class="btn btn-icon btn-sm btn-outline-secondary" type="button" onclick="cambiarMes(-1)">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>
              </button>
              <h3 class="card-title fw-bold mb-0 text-capitalize" id="lbl-mes-ano" style="min-width: 160px; text-align: center;">Cargando...</h3>
              <button class="btn btn-icon btn-sm btn-outline-secondary" type="button" onclick="cambiarMes(1)">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
              </button>
            </div>
            
            <!-- Leyenda de Colores de Citas -->
            <div class="d-flex align-items-center gap-3 text-xs fw-bold">
              <span class="d-flex align-items-center gap-1"><span class="badge-estado-cita badge-pendiente" style="width:12px;height:12px;"></span> Pendientes</span>
              <span class="d-flex align-items-center gap-1"><span class="badge-estado-cita badge-realizada" style="width:12px;height:12px;"></span> Realizadas</span>
              <span class="d-flex align-items-center gap-1"><span class="badge-estado-cita badge-cancelada" style="width:12px;height:12px;"></span> Canceladas</span>
            </div>

            <button class="btn btn-sm btn-success d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-cita" type="button">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
              Agendar Cita
            </button>
          </div>
          <div class="card-body p-2 p-md-3">
            <div class="calendar-header text-muted small text-uppercase">
              <div>Dom</div><div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div>
            </div>
            <div class="calendar-body" id="grid-calendario"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal: Agenda del Día con Servicios, Cancelar y Reagendar -->
<div class="modal modal-blur fade" id="modal-citas-dia" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon text-pink" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /></svg>
          Agenda del día: <span id="lbl-modal-fecha" class="text-pink fw-extrabold"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3" id="contenedor-citas-modal" style="max-height: 480px; overflow-y: auto;">
        <!-- Inyectado por JS -->
      </div>
      <div class="modal-footer bg-light py-2">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Confirmar Cancelación de Cita -->
<div class="modal modal-blur fade" id="modal-cancelar-cita" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-body text-center py-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="36" height="36" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5.07 19h13.86a1.5 1.5 0 0 0 1.3 -2.25l-6.93 -12a1.5 1.5 0 0 0 -2.6 0l-6.93 12a1.5 1.5 0 0 0 1.3 2.25z" /></svg>
        <h3>¿Cancelar Cita?</h3>
        <p class="text-muted small">¿Estás seguro de que deseas cancelar esta cita agendada? Esta acción no se puede deshacer.</p>
      </div>
      <div class="modal-footer bg-light py-2">
        <div class="w-100 row g-2">
          <div class="col"><button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Volver</button></div>
          <div class="col"><a id="btn-ejecutar-cancelar" href="#" class="btn btn-danger w-100">Sí, cancelar</a></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Reagendar Cita -->
<div class="modal modal-blur fade" id="modal-reagendar-cita" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="/anacosmeticsy/public/citas/reagendarCita" method="POST">
        <input type="hidden" name="cita_id" id="reagendar-cita-id">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-dark">📅 Reagendar Cita</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold required">Nueva Fecha y Hora de Atención</label>
            <input type="datetime-local" class="form-control" name="nueva_fecha_cita" id="input-nueva-fecha" min="<?= date('Y-m-d\TH:i') ?>" required>
            <small class="text-muted">Seleccione el nuevo horario disponible para el cliente.</small>
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary px-4">Guardar Nueva Fecha</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modales auxiliares de Servicios y Citas -->
<div class="modal modal-blur fade" id="modal-servicio" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="/anacosmeticsy/public/citas/guardarServicio" method="POST">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-dark">Nuevo Servicio de Salón</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold required">Nombre del Servicio</label>
            <input type="text" class="form-control" name="nombre" placeholder="Ej. Manicura Semipermanente" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Descripción corta</label>
            <textarea class="form-control" name="descripcion" rows="2" placeholder="Detalles..."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold required">Precio en Dólares ($ USD)</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" step="0.01" min="0" class="form-control" name="precio_usd" placeholder="0.00" required>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-pink px-4">Guardar Servicio</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modal-editar-servicio" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="/anacosmeticsy/public/citas/actualizarServicio" method="POST">
        <input type="hidden" name="id" id="edit-id">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-dark">Editar Servicio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold required">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="edit-nombre" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Descripción</label>
            <textarea class="form-control" name="descripcion" id="edit-descripcion" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold required">Precio ($ USD)</label>
            <input type="number" step="0.01" min="0" class="form-control" name="precio_usd" id="edit-precio" required>
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modal-eliminar-servicio" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-body text-center py-4">
        <h3>¿Estás seguro?</h3>
        <p class="text-muted">Vas a eliminar el servicio: <strong id="lbl-eliminar-nombre"></strong>.</p>
      </div>
      <div class="modal-footer bg-light py-2">
        <div class="w-100 row">
          <div class="col"><button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Cancelar</button></div>
          <div class="col"><a id="btn-confirmar-eliminar" href="#" class="btn btn-danger w-100">Sí, eliminar</a></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modal-cita" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="/anacosmeticsy/public/citas/guardarCita" method="POST">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-dark">Agendar Nueva Cita de Salón</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold required">Seleccionar Cliente</label>
              <select name="cliente_id" class="form-select" required>
                <option value="">-- Seleccionar --</option>
                <?php if (!empty($clientes)): ?>
                  <?php foreach ($clientes as $cli): ?>
                    <option value="<?= $cli['id'] ?>"><?= htmlspecialchars($cli['nombre']) ?> (<?= htmlspecialchars($cli['telefono'] ?? 'Sin telf') ?>)</option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold required">Fecha y Hora</label>
              <input type="datetime-local" class="form-control" name="fecha_cita" value="<?= date('Y-m-d\TH:i') ?>" min="<?= date('Y-m-d\TH:i') ?>" required>
            </div>
            <div class="col-12 border-top pt-3">
              <label class="form-label fw-bold text-pink">Servicios a Realizar</label>
              <div class="row g-2 mb-2">
                <div class="col">
                  <select id="select-servicio-agregar" class="form-select">
                    <option value="">-- Seleccionar servicio --</option>
                    <?php if (!empty($servicios)): ?>
                      <?php foreach ($servicios as $s): ?>
                        <option value="<?= $s['id'] ?>" data-nombre="<?= htmlspecialchars($s['nombre']) ?>" data-precio="<?= $s['precio_usd'] ?>"><?= htmlspecialchars($s['nombre']) ?> — $<?= number_format($s['precio_usd'], 2) ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="col-auto">
                  <button type="button" class="btn btn-pink" onclick="agregarServicioAlCarrito()">Agregar</button>
                </div>
              </div>
              <div class="table-responsive border rounded-3 bg-light-subtle">
                <table class="table table-vcenter table-sm card-table mb-0">
                  <thead><tr class="bg-light"><th>Servicio</th><th class="text-end">Precio</th><th class="text-center">X</th></tr></thead>
                  <tbody id="carrito-servicios-body">
                    <tr id="tr-carrito-vacio"><td colspan="3" class="text-center py-3 text-muted small">No hay servicios agregados.</td></tr>
                  </tbody>
                </table>
              </div>
              <div class="card bg-pink-lt border-0 mt-3 p-3 rounded-3">
                <div class="d-flex justify-content-between align-items-center">
                  <div><span class="text-uppercase small fw-bold text-muted d-block">Total Estimado</span><span class="fs-4 fw-extrabold text-success" id="lbl-total-bs">Bs. 0,00</span></div>
                  <div class="text-end"><span class="fs-1 fw-extrabold text-dark" id="lbl-total-usd">$0.00</span><input type="hidden" name="total_estimado_usd" id="input-total-usd" value="0.00"></div>
                </div>
              </div>
            </div>
            <div class="col-12 mt-2">
              <label class="form-label fw-bold">Observaciones</label>
              <textarea class="form-control" name="observaciones" rows="2" placeholder="Notas especiales..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success px-4">Confirmar Cita</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const tasaBCV = <?= floatval($tasaUsd ?? 0) ?>;
  let serviciosSeleccionados = [];

  function agregarServicioAlCarrito() {
    const select = document.getElementById('select-servicio-agregar');
    const servicioId = select.value;
    if (!servicioId) return;
    const option = select.options[select.selectedIndex];
    const nombre = option.getAttribute('data-nombre');
    const precio = parseFloat(option.getAttribute('data-precio'));

    if (serviciosSeleccionados.some(s => s.id == servicioId)) {
      alert('Este servicio ya está agregado.');
      return;
    }
    serviciosSeleccionados.push({ id: servicioId, nombre, precio });
    select.value = '';
    actualizarCarritoUI();
  }

  function removerServicioDelCarrito(id) {
    serviciosSeleccionados = serviciosSeleccionados.filter(s => s.id != id);
    actualizarCarritoUI();
  }

  function actualizarCarritoUI() {
    const tbody = document.getElementById('carrito-servicios-body');
    tbody.innerHTML = '';
    if (serviciosSeleccionados.length === 0) {
      tbody.innerHTML = `<tr id="tr-carrito-vacio"><td colspan="3" class="text-center py-3 text-muted small">No hay servicios agregados.</td></tr>`;
      actualizarTotales(0);
      return;
    }
    let totalUsd = 0;
    serviciosSeleccionados.forEach(serv => {
      totalUsd += serv.precio;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><div class="fw-bold text-dark">${serv.nombre}</div><input type="hidden" name="servicios[]" value="${serv.id}"></td>
        <td class="text-end fw-bold text-success">$${serv.precio.toFixed(2)}</td>
        <td class="text-center"><button type="button" class="btn btn-icon btn-sm btn-outline-danger" onclick="removerServicioDelCarrito(${serv.id})">×</button></td>
      `;
      tbody.appendChild(tr);
    });
    actualizarTotales(totalUsd);
  }

  function actualizarTotales(totalUsd) {
    const totalBs = totalUsd * tasaBCV;
    document.getElementById('lbl-total-usd').innerText = `$${totalUsd.toFixed(2)}`;
    document.getElementById('input-total-usd').value = totalUsd.toFixed(2);
    document.getElementById('lbl-total-bs').innerText = `Bs. ${totalBs.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }

  function filtrarServicios() {
    const input = document.getElementById('buscador-servicios').value.toLowerCase();
    document.querySelectorAll('.fila-servicio').forEach(fila => {
      const nombre = fila.querySelector('.nombre-serv').innerText.toLowerCase();
      fila.style.display = nombre.includes(input) ? '' : 'none';
    });
  }

  function abrirModalEditar(serv) {
    document.getElementById('edit-id').value = serv.id;
    document.getElementById('edit-nombre').value = serv.nombre;
    document.getElementById('edit-descripcion').value = serv.descripcion || '';
    document.getElementById('edit-precio').value = serv.precio_usd;
    new bootstrap.Modal(document.getElementById('modal-editar-servicio')).show();
  }

  function abrirModalEliminar(id, nombre) {
    document.getElementById('lbl-eliminar-nombre').innerText = nombre;
    document.getElementById('btn-confirmar-eliminar').href = `/anacosmeticsy/public/citas/eliminarServicio/${id}`;
    new bootstrap.Modal(document.getElementById('modal-eliminar-servicio')).show();
  }

  function abrirModalCancelarCita(citaId) {
    document.getElementById('btn-ejecutar-cancelar').href = `/anacosmeticsy/public/citas/cancelarCita/${citaId}`;
    new bootstrap.Modal(document.getElementById('modal-cancelar-cita')).show();
  }

  function abrirModalReagendar(citaId, fechaActual) {
    document.getElementById('reagendar-cita-id').value = citaId;
    if (fechaActual) {
      document.getElementById('input-nueva-fecha').value = fechaActual.replace(' ', 'T').substring(0, 16);
    }
    new bootstrap.Modal(document.getElementById('modal-reagendar-cita')).show();
  }

  const citasBD = <?= json_encode($citas ?? []) ?>;
  let fechaActual = new Date();

  document.addEventListener('DOMContentLoaded', () => { renderizarCalendario(); });

  function renderizarCalendario() {
    const grid = document.getElementById('grid-calendario');
    const lblMesAno = document.getElementById('lbl-mes-ano');
    if (!grid || !lblMesAno) return;
    grid.innerHTML = '';

    const ano = fechaActual.getFullYear();
    const mes = fechaActual.getMonth();
    lblMesAno.innerText = new Intl.DateTimeFormat('es-ES', { month: 'long', year: 'numeric' }).format(fechaActual);

    const primerDiaMes = new Date(ano, mes, 1).getDay();
    const totalDiasMes = new Date(ano, mes + 1, 0).getDate();
    const totalDiasMesAnt = new Date(ano, mes, 0).getDate();

    for (let i = primerDiaMes - 1; i >= 0; i--) {
      const el = document.createElement('div');
      el.className = 'calendar-day other-month';
      el.innerHTML = `<span class="day-number">${totalDiasMesAnt - i}</span>`;
      grid.appendChild(el);
    }

    const hoyStr = new Date().toISOString().split('T')[0];

    for (let d = 1; d <= totalDiasMes; d++) {
      const mesStr = String(mes + 1).padStart(2, '0');
      const diaStr = String(d).padStart(2, '0');
      const fechaClave = `${ano}-${mesStr}-${diaStr}`;
      
      const citasDelDia = citasBD.filter(c => c.fecha_cita && c.fecha_cita.startsWith(fechaClave));
      
      // Conteo independiente por estado
      const cantPendientes = citasDelDia.filter(c => c.estado === 'pendiente').length;
      const cantRealizadas = citasDelDia.filter(c => c.estado === 'realizada').length;
      const cantCanceladas = citasDelDia.filter(c => c.estado === 'cancelada').length;

      let clasesDia = 'calendar-day';
      if (fechaClave === hoyStr) clasesDia += ' today';
      if (citasDelDia.length > 0) clasesDia += ' has-citas';

      const el = document.createElement('div');
      el.className = clasesDia;

      // Construir indicadores visuales condicionales (> 0)
      let indicadoresHtml = '<div class="badges-citas-container">';
      if (cantPendientes > 0) {
        indicadoresHtml += `<span class="badge-estado-cita badge-pendiente" title="${cantPendientes} citas pendientes">${cantPendientes}</span>`;
      }
      if (cantRealizadas > 0) {
        indicadoresHtml += `<span class="badge-estado-cita badge-realizada" title="${cantRealizadas} citas realizadas">${cantRealizadas}</span>`;
      }
      if (cantCanceladas > 0) {
        indicadoresHtml += `<span class="badge-estado-cita badge-cancelada" title="${cantCanceladas} citas canceladas">${cantCanceladas}</span>`;
      }
      indicadoresHtml += '</div>';

      el.innerHTML = `
        <span class="day-number">${d}</span>
        ${citasDelDia.length > 0 ? indicadoresHtml : ''}
      `;

      el.onclick = () => abrirModalDia(fechaClave, d, mes + 1, ano, citasDelDia);
      grid.appendChild(el);
    }
  }

  function cambiarMes(delta) {
    fechaActual.setMonth(fechaActual.getMonth() + delta);
    renderizarCalendario();
  }

  function abrirModalDia(fechaClave, dia, mes, ano, citas) {
    document.getElementById('lbl-modal-fecha').innerText = `${String(dia).padStart(2, '0')}/${String(mes).padStart(2, '0')}/${ano}`;
    const contenedor = document.getElementById('contenedor-citas-modal');

    if (citas.length === 0) {
      contenedor.innerHTML = `<div class="text-center py-4 text-muted">No hay citas agendadas para este día.</div>`;
    } else {
      let html = '';
      citas.forEach(c => {
        const hora = c.fecha_cita.split(' ')[1] ? c.fecha_cita.split(' ')[1].substring(0, 5) : 'Sin hora';
        
        let estadoBadge = '<span class="badge bg-warning-lt">Pendiente</span>';
        if (c.estado === 'realizada') estadoBadge = '<span class="badge bg-success-lt">Realizada</span>';
        if (c.estado === 'cancelada') estadoBadge = '<span class="badge bg-danger-lt">Cancelada</span>';

        let accionesHtml = '';
        if (c.estado === 'pendiente') {
          accionesHtml = `
            <div class="d-flex gap-1 mt-2">
              <button type="button" class="btn btn-xs btn-outline-primary" onclick="abrirModalReagendar(${c.id}, '${c.fecha_cita}')">📅 Reagendar</button>
              <button type="button" class="btn btn-xs btn-outline-danger" onclick="abrirModalCancelarCita(${c.id})">❌ Cancelar Cita</button>
            </div>
          `;
        }

        let serviciosHtml = '';
        if (c.servicios && c.servicios.length > 0) {
          serviciosHtml = '<div class="mt-2 small"><strong>Servicios contratados:</strong><ul class="mb-1 ps-3">';
          c.servicios.forEach(s => {
            serviciosHtml += `<li>${s.nombre} — <span class="text-success">$${parseFloat(s.precio_usd).toFixed(2)}</span></li>`;
          });
          serviciosHtml += '</ul></div>';
        }

        html += `
          <div class="card card-body mb-2 shadow-xs border-1 rounded-3 p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="fw-bold text-primary fs-3">⏱️ ${hora}</span>
              <div>${estadoBadge}</div>
            </div>
            <div class="fw-bold fs-3 text-dark mb-1">${c.cliente_nombre}</div>
            <div class="small text-muted mb-1">📞 Teléfono: <strong>${c.cliente_telefono || 'No registrado'}</strong></div>
            
            ${serviciosHtml}

            <div class="p-2 bg-light rounded text-secondary small mt-2">
              <strong>Total Estimado:</strong> $${parseFloat(c.total_estimado_usd).toFixed(2)} USD
              ${c.observaciones ? `<br><em>Nota: ${c.observaciones}</em>` : ''}
            </div>
            ${accionesHtml}
          </div>
        `;
      });
      contenedor.innerHTML = html;
    }

    new bootstrap.Modal(document.getElementById('modal-citas-dia')).show();
  }
</script>

<?php require_once BASE_PATH . 'views/layouts/footer.php'; ?>