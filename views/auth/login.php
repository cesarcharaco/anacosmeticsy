<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Ana Boutique Cosmetics, C.A.</title>
    
    <!-- Favicon de la Empresa -->
    <link rel="icon" href="/anacosmeticsy/public/assets/img/logo_ana.ico" type="image/x-icon"/>
    
    <!-- CSS Local de Tabler -->
    <link href="/anacosmeticsy/public/assets/css/tabler.min.css" rel="stylesheet"/>

    <style>
      /* Fondo con marca de agua corporativa sutil */
      body.d-flex {
        position: relative;
        background-color: #f1f5f9;
      }
      body.d-flex::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-image: url('/anacosmeticsy/public/assets/img/logo_ana.jpeg');
        background-repeat: no-repeat;
        background-position: center center;
        background-size: 450px auto;
        opacity: 0.05;
        pointer-events: none;
        z-index: 0;
      }
      .page-center {
        position: relative;
        z-index: 1;
      }
      /* Efecto glassmorphism elegante en la tarjeta de login */
      .card-login-glass {
        background: rgba(255, 255, 255, 0.88) !important;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
      }
    </style>
  </head>
  <body class="d-flex flex-column">
    <div class="page page-center">
      <div class="container container-tight py-4">
        
        <!-- Logo e Identidad Corporativa -->
        <div class="text-center mb-4">
          <div class="avatar avatar-xl rounded bg-white border p-1 shadow-sm mx-auto mb-3" style="width: 72px; height: 72px;">
            <img src="/anacosmeticsy/public/assets/img/logo_ana.jpeg" alt="Logo Ana Boutique Cosmetics" class="w-100 h-100 object-fit-contain rounded">
          </div>
          <h1 class="navbar-brand navbar-brand-autodark fw-bold fs-2 text-dark m-0">ANA BOUTIQUE COSMETICS, C.A.</h1>
          <div class="text-muted small mt-1">RIF: J508365690</div>
        </div>
        
        <form class="card card-md card-login-glass shadow-lg rounded-3" action="/anacosmeticsy/public/auth/login" method="POST" autocomplete="off">
          <div class="card-body p-4">
            <h2 class="card-title text-center mb-4 fw-bold text-dark">Acceso al Sistema</h2>

            <?php if (isset($error)): ?>
              <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label required fw-bold small text-dark">Correo Electrónico</label>
              <input type="email" name="email" class="form-control" placeholder="usuario@anacosmetics.com" required autofocus>
            </div>
            
            <div class="mb-3">
              <label class="form-label required fw-bold small text-dark">Contraseña</label>
              <div class="input-group input-group-flat">
                <input type="password" name="password" id="password-input" class="form-control" placeholder="••••••••" required>
                <span class="input-group-text">
                  <a href="#" id="toggle-password" class="link-secondary" title="Mostrar contraseña" data-bs-toggle="tooltip">
                    <!-- Icono de Ojo (Tabler Icons) -->
                    <svg xmlns="http://www.w3.org/2000/svg" id="eye-icon" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                  </a>
                </span>
              </div>
            </div>

            <div class="form-footer mt-4">
              <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">Ingresar al Sistema</button>
            </div>
          </div>
        </form>

        <div class="text-center text-muted small mt-4 p-3 rounded bg-white bg-opacity-75 shadow-sm border">
          <span class="fw-bold text-dark">Credenciales de prueba:</span><br>
          Admin: <code>admin@anacosmetics.com</code> / <code>123456</code><br>
          Operador: <code>operador@anacosmetics.com</code> / <code>123456</code>
        </div>
      </div>
    </div>

    <!-- JS Local de Tabler -->
    <script src="/anacosmeticsy/public/assets/js/tabler.min.js"></script>

    <!-- Script JavaScript para alternar la visibilidad de la contraseña -->
    <script>
      document.getElementById('toggle-password').addEventListener('click', function (e) {
        e.preventDefault();
        const passwordInput = document.getElementById('password-input');
        const eyeIcon = document.getElementById('eye-icon');

        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          // Cambiar icono a ojo tachado
          eyeIcon.innerHTML = '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" /><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 3.012 -3.788 5.187 -4.666m2.813 -1.334c3.38 0 6.27 1.83 8.35 4.88c-.69 1.03 -1.48 1.95 -2.35 2.74" /><path d="M3 3l18 18" />';
        } else {
          passwordInput.type = 'password';
          // Restaurar icono de ojo normal
          eyeIcon.innerHTML = '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />';
        }
      });
    </script>
  </body>
</html>