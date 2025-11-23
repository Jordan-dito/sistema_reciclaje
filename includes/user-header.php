<?php
// Componente reutilizable para el header del usuario
// Variables esperadas:
// - $basePath: prefijo para las rutas ('' o '../')

$basePath = isset($basePath) ? rtrim($basePath, '/') : '';
$basePath = $basePath !== '' ? $basePath . '/' : '';

// Obtener datos del usuario desde la sesión
$usuarioNombre = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : 'Usuario';
$usuarioEmail = isset($_SESSION['usuario_email']) ? $_SESSION['usuario_email'] : '';
$usuarioRol = isset($_SESSION['usuario_rol']) ? $_SESSION['usuario_rol'] : '';

// Obtener iniciales del nombre
$iniciales = '';
if (!empty($usuarioNombre)) {
    $nombres = explode(' ', $usuarioNombre);
    $iniciales = strtoupper(substr($nombres[0], 0, 1));
    if (isset($nombres[1])) {
        $iniciales .= strtoupper(substr($nombres[1], 0, 1));
    } else {
        $iniciales .= strtoupper(substr($nombres[0], 1, 1));
    }
}
?>
<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
  <div class="container-fluid">
    <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
      <li class="nav-item topbar-user dropdown hidden-caret">
        <a class="dropdown-toggle profile-pic d-flex align-items-center" data-bs-toggle="dropdown" href="#" aria-expanded="false" style="text-decoration: none;">
          <div class="avatar-sm me-2">
            <div class="avatar-img rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px; font-size: 14px; line-height: 40px;">
              <?php echo htmlspecialchars($iniciales); ?>
            </div>
          </div>
          <span class="profile-username">
            <span class="op-7 text-muted">Hola,</span>
            <span class="fw-bold text-dark"><?php echo htmlspecialchars(explode(' ', $usuarioNombre)[0]); ?></span>
          </span>
        </a>
        <ul class="dropdown-menu dropdown-user animated fadeIn">
          <div class="dropdown-user-scroll scrollbar-outer">
            <li>
              <div class="user-box">
                <div class="avatar-lg">
                  <div class="avatar-img rounded bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 60px; height: 60px; font-size: 24px; line-height: 60px;">
                    <?php echo htmlspecialchars($iniciales); ?>
                  </div>
                </div>
                <div class="u-text">
                  <h4><?php echo htmlspecialchars($usuarioNombre); ?></h4>
                  <p class="text-muted mb-1"><?php echo htmlspecialchars($usuarioEmail); ?></p>
                  <p class="text-muted small">
                    <i class="fas fa-user-tag"></i> <?php echo htmlspecialchars($usuarioRol); ?>
                  </p>
                </div>
              </div>
            </li>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <li>
              <a class="dropdown-item" href="<?php echo $basePath; ?>config/logout.php">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
              </a>
            </li>
          </div>
        </ul>
      </li>
    </ul>
  </div>
</nav>

