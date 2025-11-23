<?php
// Componente reutilizable para el header del usuario
// Variables esperadas:
// - $basePath: prefijo para las rutas ('' o '../')

require_once __DIR__ . '/../config/database.php';

$basePath = isset($basePath) ? rtrim($basePath, '/') : '';
$basePath = $basePath !== '' ? $basePath . '/' : '';

// Obtener datos del usuario desde la sesión
$usuarioId = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
$usuarioNombre = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : 'Usuario';
$usuarioEmail = isset($_SESSION['usuario_email']) ? $_SESSION['usuario_email'] : '';
$usuarioRol = isset($_SESSION['usuario_rol']) ? $_SESSION['usuario_rol'] : '';

// Obtener foto de perfil desde la base de datos
$fotoPerfil = null;
if ($usuarioId) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
        $stmt->execute([$usuarioId]);
        $usuario = $stmt->fetch();
        if ($usuario && !empty($usuario['foto_perfil']) && file_exists(__DIR__ . '/../' . $usuario['foto_perfil'])) {
            $fotoPerfil = $usuario['foto_perfil'];
            // Actualizar sesión
            $_SESSION['usuario_foto_perfil'] = $fotoPerfil;
        }
    } catch (Exception $e) {
        error_log("Error al obtener foto de perfil: " . $e->getMessage());
    }
}

// Si no hay foto en BD, verificar en sesión
if (!$fotoPerfil && isset($_SESSION['usuario_foto_perfil'])) {
    $fotoPerfil = $_SESSION['usuario_foto_perfil'];
}

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
            <?php if ($fotoPerfil): ?>
              <img src="<?php echo htmlspecialchars($basePath . $fotoPerfil); ?>" 
                   alt="Foto de perfil" 
                   class="avatar-img rounded-circle" 
                   style="width: 40px; height: 40px; object-fit: cover;"
                   onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <div class="avatar-img rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px; font-size: 14px; line-height: 40px; display: none;">
                <?php echo htmlspecialchars($iniciales); ?>
              </div>
            <?php else: ?>
              <div class="avatar-img rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px; font-size: 14px; line-height: 40px;">
                <?php echo htmlspecialchars($iniciales); ?>
              </div>
            <?php endif; ?>
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
                <div class="avatar-lg position-relative">
                  <?php if ($fotoPerfil): ?>
                    <img src="<?php echo htmlspecialchars($basePath . $fotoPerfil); ?>" 
                         alt="Foto de perfil" 
                         class="avatar-img rounded" 
                         style="width: 60px; height: 60px; object-fit: cover;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="avatar-img rounded bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 60px; height: 60px; font-size: 24px; line-height: 60px; display: none;">
                      <?php echo htmlspecialchars($iniciales); ?>
                    </div>
                  <?php else: ?>
                    <div class="avatar-img rounded bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 60px; height: 60px; font-size: 24px; line-height: 60px;">
                      <?php echo htmlspecialchars($iniciales); ?>
                    </div>
                  <?php endif; ?>
                  <button class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" 
                          style="width: 24px; height: 24px; padding: 0; font-size: 12px;"
                          data-bs-toggle="modal" 
                          data-bs-target="#modalFotoPerfil"
                          title="Cambiar foto de perfil">
                    <i class="fas fa-camera"></i>
                  </button>
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
              <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalFotoPerfil">
                <i class="fas fa-camera"></i> Cambiar Foto de Perfil
              </a>
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

