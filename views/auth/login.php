<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Iniciar Sesión — Ana Cosmetics</title>
    <!-- CSS Local de Tabler -->
    <link href="/anacosmeticsy/public/assets/css/tabler.min.css" rel="stylesheet"/>
  </head>
  <body class="d-flex flex-column">
    <div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
          <h1 class="navbar-brand navbar-brand-autodark"><b>Ana Cosmetics</b></h1>
        </div>
        
        <form class="card card-md" action="/anacosmeticsy/public/auth/login" method="POST" autocomplete="off">
          <div class="card-body">
            <h2 class="card-title text-center mb-4">Acceso al Sistema</h2>

            <?php if (isset($error)): ?>
              <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label">Correo Electrónico</label>
              <input type="email" name="email" class="form-control" placeholder="admin@anacosmetics.com" required>
            </div>
            
            <div class="mb-2">
              <label class="form-label">Contraseña</label>
              <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </div>
          </div>
        </form>

        <div class="text-center text-muted mt-3">
          Credenciales de prueba:<br>
          Admin: <code>admin@anacosmetics.com</code> / <code>123456</code><br>
          Operador: <code>operador@anacosmetics.com</code> / <code>123456</code>
        </div>
      </div>
    </div>
    <!-- JS Local de Tabler -->
    <script src="/anacosmeticsy/public/assets/js/tabler.min.js"></script>
  </body>
</html>