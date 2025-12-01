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
<style>
/* Ocultar cualquier botón no deseado en el dropdown del usuario */
.dropdown-user .user-box .btn:not(.btn-info),
.dropdown-user .u-text .btn,
.dropdown-user .user-box .btn-secondary,
.dropdown-user .user-box .btn-primary:not(.btn-info),
.dropdown-user .user-box .btn-xs,
.dropdown-user .user-box .btn-sm:not(.btn-info),
.dropdown-user .user-box a.btn {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
}

/* Asegurar que solo el botón de cámara sea visible */
.dropdown-user .user-box .btn-info {
  display: inline-flex !important;
  visibility: visible !important;
  opacity: 1 !important;
}

/* Ocultar cualquier elemento duplicado o no deseado */
.dropdown-user .user-box > .btn,
.dropdown-user .u-text > .btn,
.dropdown-user .u-text > a.btn {
  display: none !important;
}

/* OCULTAR CUALQUIER AVATAR O BOTÓN GD EN EL HEADER (fuera del dropdown) */
.navbar-header .avatar-sm .avatar-img.rounded-circle.bg-primary:not(:first-child),
.navbar-header .avatar-sm > .avatar-img.rounded-circle.bg-primary + .avatar-img.rounded-circle.bg-primary,
.navbar-header .profile-pic .avatar-sm .bg-primary.rounded-circle:not(:first-of-type),
.navbar-header .profile-pic .avatar-sm > div.bg-primary.rounded-circle:not(:first-child) {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  width: 0 !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
}

/* OCULTAR CUALQUIER AVATAR O BOTÓN GD EN LOS DROPDOWN-ITEMS Y ENTRE ELEMENTOS */
.dropdown-user .dropdown-item .avatar-img,
.dropdown-user .dropdown-item .avatar-sm,
.dropdown-user .dropdown-item .avatar-lg,
.dropdown-user .dropdown-item .avatar,
.dropdown-user .dropdown-item .btn-primary:not(.btn-info),
.dropdown-user .dropdown-item .btn-secondary,
.dropdown-user .dropdown-item .rounded-circle:not(.btn-info),
.dropdown-user .dropdown-item > div:first-child:not(.d-flex),
.dropdown-user .dropdown-item::before,
.dropdown-user .dropdown-item::after {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  width: 0 !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  content: none !important;
}

/* OCULTAR ESPECÍFICAMENTE BOTONES CIRCULARES AZULES CON TEXTOS DE 2 LETRAS - REGLA MÁS AGRESIVA */
.dropdown-user .bg-primary.rounded-circle:not(.btn-info):not(.avatar-lg):not(.avatar-lg *),
.dropdown-user .btn-primary.rounded-circle:not(.btn-info),
.dropdown-user div.rounded-circle.bg-primary:not(.avatar-lg):not(.avatar-lg *):not(.user-box *),
.dropdown-user button.rounded-circle.bg-primary:not(.btn-info),
.dropdown-user a.rounded-circle.bg-primary:not(.btn-info),
.dropdown-user span.rounded-circle.bg-primary:not(.btn-info),
.dropdown-user .rounded-circle.bg-primary:not(.btn-info):not(.avatar-lg):not(.avatar-lg *) {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  width: 0 !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  position: absolute !important;
  left: -9999px !important;
}

/* OCULTAR CUALQUIER ELEMENTO LI QUE NO SEA USER-BOX, DROPDOWN-ITEM O DIVIDER */
.dropdown-user-scroll > li:not(:has(.user-box)):not(:has(.dropdown-item)):not(:has(.dropdown-divider)) {
  display: none !important;
  visibility: hidden !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  overflow: hidden !important;
}

/* OCULTAR CUALQUIER ELEMENTO ENTRE EL USER-BOX Y EL PRIMER DROPDOWN-ITEM */
.dropdown-user-scroll > li:has(.user-box) ~ li:not(:has(.dropdown-item)):not(:has(.dropdown-divider)) {
  display: none !important;
  visibility: hidden !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  overflow: hidden !important;
}

/* Asegurar que solo el icono de cámara y el texto sean visibles en el dropdown-item */
.dropdown-user .dropdown-item {
  display: flex !important;
  align-items: center !important;
}

.dropdown-user .dropdown-item i.fas.fa-camera {
  display: inline-block !important;
  visibility: visible !important;
}

/* Mejorar el diseño del dropdown */
.dropdown-user {
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.dropdown-user .dropdown-item:hover {
  background-color: #f8f9fa;
}

/* REGLA ULTRA ESPECÍFICA PARA ELIMINAR EL BOTÓN GD - APLICAR A CUALQUIER LI QUE CONTENGA ELEMENTOS NO DESEADOS */
.dropdown-user-scroll > li:has(> .bg-primary.rounded-circle:not(.btn-info):not(.avatar-lg):not(.avatar-lg *)),
.dropdown-user-scroll > li:has(> .btn-primary.rounded-circle:not(.btn-info)),
.dropdown-user-scroll > li:has(> div.rounded-circle.bg-primary:not(.btn-info):not(.avatar-lg):not(.avatar-lg *):not(.user-box *)),
.dropdown-user-scroll > li:has(> button.rounded-circle.bg-primary:not(.btn-info)),
.dropdown-user-scroll > li:has(> a.rounded-circle.bg-primary:not(.btn-info)),
.dropdown-user-scroll > li:has(> span.rounded-circle.bg-primary:not(.btn-info)),
/* OCULTAR CUALQUIER LI QUE ESTÉ DESPUÉS DE UN DROPDOWN-ITEM Y ANTES DE UN DIVIDER O SIGUIENTE ITEM */
.dropdown-user-scroll > li:has(.dropdown-item) + li:not(:has(.dropdown-item)):not(:has(.dropdown-divider)),
.dropdown-user-scroll > li:has(.dropdown-item) + li:has(> .bg-primary.rounded-circle),
.dropdown-user-scroll > li:has(.dropdown-item) + li:has(> .btn-primary.rounded-circle),
.dropdown-user-scroll > li:has(.dropdown-item) + li:has(> div.rounded-circle.bg-primary),
.dropdown-user-scroll > li:has(.dropdown-item) + li:has(> button.rounded-circle.bg-primary),
.dropdown-user-scroll > li:has(.dropdown-item) + li:has(> a.rounded-circle.bg-primary),
.dropdown-user-scroll > li:has(.dropdown-item) + li:has(> span.rounded-circle.bg-primary) {
  display: none !important;
  visibility: hidden !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  overflow: hidden !important;
  position: absolute !important;
  left: -9999px !important;
}

/* OCULTAR CUALQUIER ELEMENTO DENTRO DE UN LI QUE TENGA UN DROPDOWN-ITEM Y TAMBIÉN TENGA UN BOTÓN GD */
.dropdown-user-scroll > li:has(.dropdown-item) .bg-primary.rounded-circle:not(.btn-info):not(.avatar-lg):not(.avatar-lg *),
.dropdown-user-scroll > li:has(.dropdown-item) .btn-primary.rounded-circle:not(.btn-info),
.dropdown-user-scroll > li:has(.dropdown-item) div.rounded-circle.bg-primary:not(.btn-info):not(.avatar-lg):not(.avatar-lg *),
.dropdown-user-scroll > li:has(.dropdown-item) button.rounded-circle.bg-primary:not(.btn-info),
.dropdown-user-scroll > li:has(.dropdown-item) a.rounded-circle.bg-primary:not(.btn-info),
.dropdown-user-scroll > li:has(.dropdown-item) span.rounded-circle.bg-primary:not(.btn-info),
/* OCULTAR CUALQUIER ELEMENTO DENTRO DEL DROPDOWN-ITEM MISMO */
.dropdown-item .bg-primary.rounded-circle:not(.btn-info),
.dropdown-item .btn-primary.rounded-circle:not(.btn-info),
.dropdown-item div.rounded-circle.bg-primary:not(.btn-info),
.dropdown-item button.rounded-circle.bg-primary:not(.btn-info),
.dropdown-item a.rounded-circle.bg-primary:not(.btn-info),
.dropdown-item span.rounded-circle.bg-primary:not(.btn-info),
.dropdown-item > .bg-primary.rounded-circle:not(.btn-info),
.dropdown-item > .btn-primary.rounded-circle:not(.btn-info),
.dropdown-item > div.rounded-circle.bg-primary:not(.btn-info),
.dropdown-item > button.rounded-circle.bg-primary:not(.btn-info),
.dropdown-item > a.rounded-circle.bg-primary:not(.btn-info),
.dropdown-item > span.rounded-circle.bg-primary:not(.btn-info) {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  width: 0 !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  position: absolute !important;
  left: -9999px !important;
  pointer-events: none !important;
}

/* REGLA ESPECÍFICA PARA ELIMINAR EL BOTÓN GD QUE APARECE JUNTO A "Cambiar Foto de Perfil" */
.cambiar-foto-item .bg-primary.rounded-circle:not(.btn-info),
.cambiar-foto-item .btn-primary.rounded-circle:not(.btn-info),
.cambiar-foto-item div.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-item button.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-item a.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-item span.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-item > .bg-primary.rounded-circle:not(.btn-info),
.cambiar-foto-item > .btn-primary.rounded-circle:not(.btn-info),
.cambiar-foto-item > div.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-item > button.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-item > a.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-item > span.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-link .bg-primary.rounded-circle:not(.btn-info),
.cambiar-foto-link .btn-primary.rounded-circle:not(.btn-info),
.cambiar-foto-link div.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-link button.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-link a.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-link span.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-link > .bg-primary.rounded-circle:not(.btn-info),
.cambiar-foto-link > .btn-primary.rounded-circle:not(.btn-info),
.cambiar-foto-link > div.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-link > button.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-link > a.rounded-circle.bg-primary:not(.btn-info),
.cambiar-foto-link > span.rounded-circle.bg-primary:not(.btn-info) {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  width: 0 !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  position: absolute !important;
  left: -9999px !important;
  pointer-events: none !important;
}

/* OCULTAR CUALQUIER ELEMENTO HERMANO DEL LI DE CAMBIAR FOTO */
.cambiar-foto-item + li:has(.bg-primary.rounded-circle),
.cambiar-foto-item + li:has(.btn-primary.rounded-circle),
.cambiar-foto-item + li:has(div.rounded-circle.bg-primary),
.cambiar-foto-item + li:has(button.rounded-circle.bg-primary),
.cambiar-foto-item + li:has(a.rounded-circle.bg-primary),
.cambiar-foto-item + li:has(span.rounded-circle.bg-primary),
/* OCULTAR CUALQUIER LI QUE CONTENGA SOLO "GD" O DOS LETRAS MAYÚSCULAS */
.dropdown-user-scroll > li:not(:has(.user-box)):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):has(.bg-primary.rounded-circle),
.dropdown-user-scroll > li:not(:has(.user-box)):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):has(.btn-primary.rounded-circle),
.dropdown-user-scroll > li:not(:has(.user-box)):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):has(div.rounded-circle.bg-primary),
.dropdown-user-scroll > li:not(:has(.user-box)):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):has(button.rounded-circle.bg-primary),
.dropdown-user-scroll > li:not(:has(.user-box)):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):has(a.rounded-circle.bg-primary),
.dropdown-user-scroll > li:not(:has(.user-box)):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):has(span.rounded-circle.bg-primary),
/* REGLA ULTRA ESPECÍFICA: OCULTAR CUALQUIER LI ENTRE DIVIDERS QUE CONTENGA UN BOTÓN CIRCULAR AZUL */
.dropdown-user-scroll > li:has(.dropdown-divider) + li:not(:has(.dropdown-item)):not(:has(.dropdown-divider)):not(:has(.user-box)),
.dropdown-user-scroll > li:has(.dropdown-divider) + li:has(.bg-primary.rounded-circle):not(:has(.dropdown-item)),
.dropdown-user-scroll > li:has(.dropdown-divider) + li:has(.btn-primary.rounded-circle):not(:has(.dropdown-item)),
.dropdown-user-scroll > li:has(.dropdown-divider) + li:has(div.rounded-circle.bg-primary):not(:has(.dropdown-item)),
.dropdown-user-scroll > li:has(.dropdown-divider) + li:has(button.rounded-circle.bg-primary):not(:has(.dropdown-item)),
.dropdown-user-scroll > li:has(.dropdown-divider) + li:has(a.rounded-circle.bg-primary):not(:has(.dropdown-item)),
.dropdown-user-scroll > li:has(.dropdown-divider) + li:has(span.rounded-circle.bg-primary):not(:has(.dropdown-item)),
/* REGLA ADICIONAL: OCULTAR CUALQUIER LI QUE ESTÉ ENTRE EL PRIMER DIVIDER Y EL CAMBIAR-FOTO-ITEM */
.dropdown-user-scroll > li:has(.dropdown-divider):nth-of-type(2) ~ li:not(.cambiar-foto-item):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):not(:has(.user-box)),
.dropdown-user-scroll > li:has(.dropdown-divider):nth-of-type(2) ~ li:has(.bg-primary.rounded-circle):not(.cambiar-foto-item):not(:has(.dropdown-item)),
.dropdown-user-scroll > li:has(.dropdown-divider):nth-of-type(2) ~ li:has(.btn-primary.rounded-circle):not(.cambiar-foto-item):not(:has(.dropdown-item)),
.dropdown-user-scroll > li:has(.dropdown-divider):nth-of-type(2) ~ li:has(div.rounded-circle.bg-primary):not(.cambiar-foto-item):not(:has(.dropdown-item)),
.dropdown-user-scroll > li:has(.dropdown-divider):nth-of-type(2) ~ li:has(button.rounded-circle.bg-primary):not(.cambiar-foto-item):not(:has(.dropdown-item)),
.dropdown-user-scroll > li:has(.dropdown-divider):nth-of-type(2) ~ li:has(a.rounded-circle.bg-primary):not(.cambiar-foto-item):not(:has(.dropdown-item)),
.dropdown-user-scroll > li:has(.dropdown-divider):nth-of-type(2) ~ li:has(span.rounded-circle.bg-primary):not(.cambiar-foto-item):not(:has(.dropdown-item)),
/* REGLA FINAL: OCULTAR CUALQUIER LI QUE NO SEA VÁLIDO Y ESTÉ ANTES DE CAMBIAR-FOTO-ITEM */
.dropdown-user-scroll > li:not(.cambiar-foto-item):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):not(:has(.user-box)):has(.bg-primary.rounded-circle):not(:has(.avatar-lg)),
.dropdown-user-scroll > li:not(.cambiar-foto-item):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):not(:has(.user-box)):has(.btn-primary.rounded-circle),
.dropdown-user-scroll > li:not(.cambiar-foto-item):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):not(:has(.user-box)):has(div.rounded-circle.bg-primary):not(:has(.avatar-lg)),
.dropdown-user-scroll > li:not(.cambiar-foto-item):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):not(:has(.user-box)):has(button.rounded-circle.bg-primary),
.dropdown-user-scroll > li:not(.cambiar-foto-item):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):not(:has(.user-box)):has(a.rounded-circle.bg-primary),
.dropdown-user-scroll > li:not(.cambiar-foto-item):not(:has(.dropdown-item)):not(:has(.dropdown-divider)):not(:has(.user-box)):has(span.rounded-circle.bg-primary) {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  overflow: hidden !important;
  position: absolute !important;
  left: -9999px !important;
  width: 0 !important;
}
</style>
<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
  <div class="container-fluid">
    <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
      <li class="nav-item topbar-user dropdown hidden-caret">
        <a class="dropdown-toggle profile-pic d-flex align-items-center" data-bs-toggle="dropdown" href="#" aria-expanded="false" style="text-decoration: none;">
          <div class="avatar-sm me-2" style="position: relative;">
            <?php if ($fotoPerfil): ?>
              <img src="<?php echo htmlspecialchars($basePath . $fotoPerfil); ?>" 
                   alt="Foto de perfil" 
                   class="avatar-img rounded-circle" 
                   style="width: 40px; height: 40px; object-fit: cover; display: block;"
                   onerror="this.src='<?php echo $basePath; ?>assets/img/default-avatar.png';">
            <?php else: ?>
              <img src="<?php echo $basePath; ?>assets/img/default-avatar.png" 
                   alt="Foto de perfil" 
                   class="avatar-img rounded-circle" 
                   style="width: 40px; height: 40px; object-fit: cover; display: block;">
            <?php endif; ?>
          </div>
          <span class="profile-username">
            <span class="op-7 text-muted">Hola,</span>
            <span class="fw-bold text-dark"><?php echo htmlspecialchars(explode(' ', $usuarioNombre)[0]); ?></span>
          </span>
        </a>
        <ul class="dropdown-menu dropdown-user animated fadeIn" style="min-width: 280px; padding: 0; border-radius: 8px;" id="dropdownUsuario">
          <div class="dropdown-user-scroll scrollbar-outer" style="max-height: 400px;">
            <li>
              <div class="user-box d-flex align-items-center p-3" style="border-bottom: 1px solid #e9ecef; background-color: #f8f9fa;">
                <div class="avatar-lg position-relative me-3" style="flex-shrink: 0;">
                  <?php if ($fotoPerfil): ?>
                    <img src="<?php echo htmlspecialchars($basePath . $fotoPerfil); ?>" 
                         alt="Foto de perfil" 
                         class="avatar-img rounded-circle" 
                         style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #e0e0e0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                         onerror="this.src='<?php echo $basePath; ?>assets/img/default-avatar.png';">
                  <?php else: ?>
                    <img src="<?php echo $basePath; ?>assets/img/default-avatar.png" 
                         alt="Foto de perfil" 
                         class="avatar-img rounded-circle" 
                         style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #e0e0e0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                  <?php endif; ?>
                  <button class="btn btn-sm btn-info position-absolute bottom-0 end-0 rounded-circle shadow" 
                          style="width: 28px; height: 28px; padding: 0; font-size: 11px; border: 2px solid white; z-index: 10; display: flex; align-items: center; justify-content: center;"
                          onclick="abrirModalFotoPerfil(); event.stopPropagation(); return false;"
                          title="Cambiar foto de perfil">
                    <i class="fas fa-camera"></i>
                  </button>
                </div>
                <div class="u-text flex-grow-1">
                  <h4 class="mb-1" style="font-size: 16px; font-weight: 600; color: #212529; line-height: 1.3;"><?php echo htmlspecialchars($usuarioNombre); ?></h4>
                  <p class="text-muted mb-1" style="font-size: 13px; line-height: 1.4; margin-bottom: 4px;"><?php echo htmlspecialchars($usuarioEmail); ?></p>
                  <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.4;">
                    <i class="fas fa-user-tag me-1"></i> <?php echo htmlspecialchars($usuarioRol); ?>
                  </p>
                </div>
              </div>
            </li>
            <li>
              <div class="dropdown-divider my-1"></div>
            </li>
            <li style="position: relative;" class="cambiar-foto-item">
              <a class="dropdown-item d-flex align-items-center py-2 px-3 cambiar-foto-link" 
                 href="#" 
                 onclick="abrirModalFotoPerfil(); event.stopPropagation(); return false;" 
                 style="transition: background-color 0.2s; text-decoration: none; position: relative; overflow: visible;">
                <i class="fas fa-camera me-3" style="width: 18px; text-align: center; color: #6c757d; z-index: 10; position: relative; flex-shrink: 0;"></i> 
                <span style="color: #212529; z-index: 10; position: relative; flex: 1;">Cambiar Foto de Perfil</span>
              </a>
            </li>
            <li style="position: relative;" class="cambiar-password-item">
              <a class="dropdown-item d-flex align-items-center py-2 px-3 cambiar-password-link" 
                 href="#" 
                 onclick="abrirModalCambiarPassword(); event.stopPropagation(); return false;" 
                 style="transition: background-color 0.2s; text-decoration: none; position: relative; overflow: visible;">
                <i class="fas fa-key me-3" style="width: 18px; text-align: center; color: #f39c12; z-index: 10; position: relative; flex-shrink: 0;"></i> 
                <span style="color: #212529; z-index: 10; position: relative; flex: 1;">Cambiar Contraseña</span>
              </a>
            </li>
            <li>
              <div class="dropdown-divider my-1"></div>
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center py-2 px-3" 
                 href="<?php echo $basePath; ?>config/logout.php" 
                 style="transition: background-color 0.2s; color: #dc3545; text-decoration: none;">
                <i class="fas fa-sign-out-alt me-3" style="width: 18px; text-align: center;"></i> 
                <span>Cerrar Sesión</span>
              </a>
            </li>
          </div>
        </ul>
      </li>
    </ul>
  </div>
</nav>
<script>
// Eliminar cualquier botón GD o avatar no deseado del dropdown
(function() {
  function removeGDButtons() {
    const dropdown = document.querySelector('.dropdown-user');
    if (!dropdown) return;
    
    // Buscar TODOS los elementos en el dropdown
    const allElements = dropdown.querySelectorAll('*');
    allElements.forEach(function(el) {
      const text = (el.textContent || el.innerText || '').trim();
      const classes = (el.className || '').toString();
      const tagName = el.tagName || '';
      
      // Eliminar elementos que contengan "GD" o dos letras mayúsculas
      if (text === 'GD' || (text.match(/^[A-Z]{2}$/) && text.length === 2)) {
        if (classes.includes('rounded-circle') || classes.includes('bg-primary') || classes.includes('avatar')) {
          if (!el.closest('.user-box') && !el.closest('.avatar-lg')) {
            el.style.display = 'none';
            el.style.visibility = 'hidden';
            el.style.opacity = '0';
            el.style.width = '0';
            el.style.height = '0';
            el.style.margin = '0';
            el.style.padding = '0';
            try { el.remove(); } catch(e) {}
          }
        }
      }
      
      // Eliminar botones/divs circulares azules que no sean el botón de cámara
      if ((tagName === 'BUTTON' || tagName === 'DIV' || tagName === 'A' || tagName === 'SPAN') &&
          classes.includes('rounded-circle') && 
          (classes.includes('bg-primary') || classes.includes('btn-primary')) && 
          !classes.includes('btn-info') &&
          !el.closest('.user-box .avatar-lg')) {
        el.style.display = 'none';
        el.style.visibility = 'hidden';
        el.style.opacity = '0';
        try { el.remove(); } catch(e) {}
      }
    });
    
    // Buscar elementos li que no sean dropdown-item o divider
    const listItems = dropdown.querySelectorAll('li');
    listItems.forEach(function(li) {
      const hasDropdownItem = li.querySelector('.dropdown-item');
      const hasDivider = li.querySelector('.dropdown-divider');
      const hasUserBox = li.querySelector('.user-box');
      
      if (!hasDropdownItem && !hasDivider && !hasUserBox) {
        // Este li no debería existir, ocultarlo y eliminarlo
        li.style.display = 'none';
        li.style.visibility = 'hidden';
        li.style.height = '0';
        li.style.margin = '0';
        li.style.padding = '0';
        li.style.overflow = 'hidden';
        try { li.remove(); } catch(e) {}
      }
      
      // Buscar dentro de cada li elementos no deseados
      const unwantedElements = li.querySelectorAll('.bg-primary.rounded-circle:not(.btn-info):not(.avatar-lg):not(.avatar-lg *), .btn-primary.rounded-circle:not(.btn-info), div.rounded-circle.bg-primary:not(.btn-info):not(.avatar-lg):not(.avatar-lg *):not(.user-box *), button.rounded-circle.bg-primary:not(.btn-info), a.rounded-circle.bg-primary:not(.btn-info), span.rounded-circle.bg-primary:not(.btn-info)');
      unwantedElements.forEach(function(el) {
        el.style.display = 'none';
        el.style.visibility = 'hidden';
        el.style.opacity = '0';
        el.style.width = '0';
        el.style.height = '0';
        el.style.margin = '0';
        el.style.padding = '0';
        try { el.remove(); } catch(e) {}
      });
      
      // Si este li tiene un dropdown-item, asegurarse de que no tenga elementos adicionales no deseados
      if (li.querySelector('.dropdown-item')) {
        const dropdownItem = li.querySelector('.dropdown-item');
        // Buscar y eliminar cualquier elemento GD dentro del dropdown-item
        const gdElements = dropdownItem.querySelectorAll('.bg-primary.rounded-circle:not(.btn-info), .btn-primary.rounded-circle:not(.btn-info), div.rounded-circle.bg-primary:not(.btn-info), button.rounded-circle.bg-primary:not(.btn-info), a.rounded-circle.bg-primary:not(.btn-info), span.rounded-circle.bg-primary:not(.btn-info)');
        gdElements.forEach(function(el) {
          el.style.display = 'none';
          el.style.visibility = 'hidden';
          el.style.opacity = '0';
          el.style.width = '0';
          el.style.height = '0';
          try { el.remove(); } catch(e) {}
        });
        
        // También buscar elementos hijos directos del dropdown-item
        const children = Array.from(dropdownItem.children);
        children.forEach(function(child) {
          if ((child.classList.contains('bg-primary') || child.classList.contains('btn-primary') || child.classList.contains('rounded-circle')) &&
              !child.classList.contains('fas') && 
              !child.classList.contains('fa-camera') &&
              !child.tagName.toLowerCase() === 'i') {
            child.style.display = 'none';
            child.style.visibility = 'hidden';
            try { child.remove(); } catch(e) {}
          }
        });
        
      // Buscar elementos con texto "GD" - SOLUCIÓN MÁS AGRESIVA
      const allText = dropdownItem.textContent || dropdownItem.innerText || '';
      if (allText.includes('GD') && !allText.includes('Gerente del Sistema')) {
        // Buscar cualquier elemento que contenga exactamente "GD"
        const gdTextElements = Array.from(dropdownItem.querySelectorAll('*'));
        gdTextElements.forEach(function(el) {
          const text = el.textContent ? el.textContent.trim() : '';
          if (text === 'GD' || text === 'G D' || (text.length === 2 && text.match(/^[A-Z]{2}$/))) {
            // Eliminar el elemento y su padre si es circular
            const parent = el.parentElement;
            if (parent && (parent.classList.contains('rounded-circle') || parent.classList.contains('bg-primary') || parent.classList.contains('btn-primary'))) {
              parent.style.display = 'none';
              parent.style.visibility = 'hidden';
              parent.style.opacity = '0';
              parent.style.width = '0';
              parent.style.height = '0';
              try { parent.remove(); } catch(e) {}
            }
            el.style.display = 'none';
            el.style.visibility = 'hidden';
            el.style.opacity = '0';
            try { el.remove(); } catch(e) {}
          }
        });
        
        // Buscar elementos hermanos del dropdown-item que contengan GD
        const parentLi = dropdownItem.closest('li');
        if (parentLi) {
          const siblings = Array.from(parentLi.children);
          siblings.forEach(function(sibling) {
            if (sibling !== dropdownItem && (sibling.classList.contains('bg-primary') || sibling.classList.contains('btn-primary') || sibling.classList.contains('rounded-circle'))) {
              const siblingText = sibling.textContent ? sibling.textContent.trim() : '';
              if (siblingText === 'GD' || siblingText.match(/^[A-Z]{2}$/)) {
                sibling.style.display = 'none';
                sibling.style.visibility = 'hidden';
                sibling.style.opacity = '0';
                try { sibling.remove(); } catch(e) {}
              }
            }
          });
        }
                try { parent.remove(); } catch(e) {}
              } else {
                el.style.display = 'none';
                el.style.visibility = 'hidden';
                try { el.remove(); } catch(e) {}
              }
            }
          });
        }
      }
    });
    
    // Buscar específicamente elementos con texto "GD" o dos letras mayúsculas
    const allTextElements = dropdown.querySelectorAll('*');
    allTextElements.forEach(function(el) {
      const text = (el.textContent || el.innerText || '').trim();
      // Si el texto es "GD" o dos letras mayúsculas y no está en el user-box
      if (text === 'GD' || (text.match(/^[A-Z]{2}$/) && text.length === 2 && !el.closest('.user-box'))) {
        const parent = el.parentElement;
        // Si el padre es un elemento circular azul, eliminar el padre
        if (parent && (parent.classList.contains('rounded-circle') || parent.classList.contains('bg-primary') || parent.classList.contains('btn-primary'))) {
          // Si el padre está dentro de un li que tiene dropdown-item, eliminar todo el li
          const liParent = parent.closest('li');
          if (liParent && liParent.querySelector('.dropdown-item')) {
            // Si este li tiene dropdown-item, solo eliminar el elemento no deseado, no el li completo
            parent.style.display = 'none';
            parent.style.visibility = 'hidden';
            try { parent.remove(); } catch(e) {}
          } else {
            parent.style.display = 'none';
            parent.style.visibility = 'hidden';
            try { parent.remove(); } catch(e) {}
          }
        } else {
          // Si el elemento mismo es circular azul, eliminarlo
          if (el.classList.contains('rounded-circle') && (el.classList.contains('bg-primary') || el.classList.contains('btn-primary'))) {
            el.style.display = 'none';
            el.style.visibility = 'hidden';
            try { el.remove(); } catch(e) {}
          } else {
            el.style.display = 'none';
            el.style.visibility = 'hidden';
            try { el.remove(); } catch(e) {}
          }
        }
      }
    });
    
    // Buscar específicamente li que estén después de un dropdown-item y contengan botones GD
    const dropdownItems = dropdown.querySelectorAll('li:has(.dropdown-item)');
    dropdownItems.forEach(function(itemLi) {
      const nextLi = itemLi.nextElementSibling;
      if (nextLi && !nextLi.querySelector('.dropdown-item') && !nextLi.querySelector('.dropdown-divider')) {
        const hasGD = nextLi.querySelector('.bg-primary.rounded-circle, .btn-primary.rounded-circle, div.rounded-circle.bg-primary, button.rounded-circle.bg-primary');
        if (hasGD || nextLi.textContent.trim() === 'GD' || nextLi.textContent.trim().match(/^[A-Z]{2}$/)) {
          nextLi.style.display = 'none';
          nextLi.style.visibility = 'hidden';
          try { nextLi.remove(); } catch(e) {}
        }
      }
    });
  }
  
  // También limpiar el header (fuera del dropdown)
  function removeGDFromHeader() {
    const header = document.querySelector('.navbar-header');
    if (header) {
      const avatarSm = header.querySelector('.avatar-sm');
      if (avatarSm) {
        const avatares = avatarSm.querySelectorAll('.avatar-img.rounded-circle.bg-primary');
        if (avatares.length > 1) {
          // Si hay más de un avatar, mantener solo el primero (el que tiene la foto o el fallback)
          for (let i = 1; i < avatares.length; i++) {
            avatares[i].style.display = 'none';
            avatares[i].style.visibility = 'hidden';
            try { avatares[i].remove(); } catch(e) {}
          }
        }
        
        // Buscar elementos con texto "GD" en el header
        const allElements = header.querySelectorAll('*');
        allElements.forEach(function(el) {
          const text = (el.textContent || el.innerText || '').trim();
          if (text === 'GD' && el.closest('.avatar-sm')) {
            const parent = el.parentElement;
            if (parent && parent.classList.contains('rounded-circle')) {
              parent.style.display = 'none';
              parent.style.visibility = 'hidden';
              try { parent.remove(); } catch(e) {}
            }
          }
        });
      }
    }
  }
  
  // Ejecutar limpieza del header también
  removeGDFromHeader();
  }
  
  // Función específica para eliminar GD del dropdown-item de "Cambiar Foto de Perfil"
  function removeGDFromCambiarFoto() {
    // Buscar TODOS los li dentro del dropdown que no sean user-box, dropdown-item o divider
    const allLis = document.querySelectorAll('.dropdown-user-scroll > li');
    allLis.forEach(function(li) {
      // Si el li no tiene user-box, dropdown-item ni dropdown-divider
      if (!li.querySelector('.user-box') && 
          !li.querySelector('.dropdown-item') && 
          !li.querySelector('.dropdown-divider')) {
        // Verificar si contiene un botón circular azul con GD
        const circularElements = li.querySelectorAll('.bg-primary.rounded-circle, .btn-primary.rounded-circle, div.rounded-circle.bg-primary, button.rounded-circle.bg-primary, a.rounded-circle.bg-primary, span.rounded-circle.bg-primary');
        let shouldRemove = false;
        
        circularElements.forEach(function(el) {
          const text = el.textContent ? el.textContent.trim() : '';
          // Si el elemento contiene GD o dos letras mayúsculas, y no está en el user-box
          if ((text === 'GD' || text === 'G D' || (text.length === 2 && text.match(/^[A-Z]{2}$/))) &&
              !el.closest('.user-box') && !el.closest('.avatar-lg')) {
            shouldRemove = true;
          }
        });
        
        // También verificar el texto completo del li
        const liText = li.textContent ? li.textContent.trim() : '';
        if (liText === 'GD' || liText === 'G D' || (liText.length === 2 && liText.match(/^[A-Z]{2}$/))) {
          shouldRemove = true;
        }
        
        // Si tiene un elemento circular azul pero no tiene dropdown-item, también eliminarlo
        if (circularElements.length > 0 && !li.querySelector('.dropdown-item')) {
          shouldRemove = true;
        }
        
        if (shouldRemove) {
          // Eliminar todo el li
          li.style.display = 'none';
          li.style.visibility = 'hidden';
          li.style.opacity = '0';
          li.style.height = '0';
          li.style.margin = '0';
          li.style.padding = '0';
          li.style.overflow = 'hidden';
          li.style.width = '0';
          try { li.remove(); } catch(e) {}
        }
      }
    });
    
    // Buscar el dropdown-item que contiene "Cambiar Foto de Perfil"
    const cambiarFotoItems = document.querySelectorAll('.dropdown-item');
    cambiarFotoItems.forEach(function(item) {
      if (item.textContent && item.textContent.includes('Cambiar Foto de Perfil')) {
        // Buscar cualquier elemento dentro del item que no sea el icono ni el span
        const allChildren = item.querySelectorAll('*');
        allChildren.forEach(function(child) {
          const text = child.textContent ? child.textContent.trim() : '';
          // Si es un elemento circular azul o contiene GD
          if ((child.classList.contains('bg-primary') || 
               child.classList.contains('btn-primary') || 
               child.classList.contains('rounded-circle')) &&
              !child.classList.contains('fas') &&
              !child.classList.contains('fa-camera') &&
              child.tagName !== 'I' &&
              (text === 'GD' || text.match(/^[A-Z]{2}$/) || text.match(/GD/))) {
            child.style.display = 'none';
            child.style.visibility = 'hidden';
            child.style.opacity = '0';
            child.style.width = '0';
            child.style.height = '0';
            child.style.margin = '0';
            child.style.padding = '0';
            try { child.remove(); } catch(e) {}
          }
        });
        
        // Buscar elementos hermanos dentro del mismo li
        const parentLi = item.closest('li');
        if (parentLi) {
          const siblings = Array.from(parentLi.children);
          siblings.forEach(function(sibling) {
            if (sibling !== item) {
              const siblingText = sibling.textContent ? sibling.textContent.trim() : '';
              if ((sibling.classList.contains('bg-primary') || 
                   sibling.classList.contains('btn-primary') || 
                   sibling.classList.contains('rounded-circle')) &&
                  (siblingText === 'GD' || siblingText.match(/^[A-Z]{2}$/) || siblingText.match(/GD/))) {
                sibling.style.display = 'none';
                sibling.style.visibility = 'hidden';
                sibling.style.opacity = '0';
                sibling.style.width = '0';
                sibling.style.height = '0';
                try { sibling.remove(); } catch(e) {}
              }
            }
          });
        }
        
        // Buscar el siguiente li hermano que pueda contener GD
        if (parentLi) {
          const nextLi = parentLi.nextElementSibling;
          if (nextLi && !nextLi.querySelector('.dropdown-item') && !nextLi.querySelector('.dropdown-divider')) {
            const nextLiText = nextLi.textContent ? nextLi.textContent.trim() : '';
            if (nextLiText === 'GD' || nextLiText.match(/^[A-Z]{2}$/) || nextLiText.match(/GD/)) {
              nextLi.style.display = 'none';
              nextLi.style.visibility = 'hidden';
              nextLi.style.opacity = '0';
              nextLi.style.height = '0';
              nextLi.style.margin = '0';
              nextLi.style.padding = '0';
              try { nextLi.remove(); } catch(e) {}
            }
          }
        }
      }
    });
  }
  
  // Ejecutar múltiples veces para asegurar
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      removeGDButtons();
      removeGDFromHeader();
      removeGDFromCambiarFoto();
      setTimeout(function() { removeGDButtons(); removeGDFromHeader(); removeGDFromCambiarFoto(); }, 100);
      setTimeout(function() { removeGDButtons(); removeGDFromHeader(); removeGDFromCambiarFoto(); }, 500);
    });
  } else {
    removeGDButtons();
    removeGDFromHeader();
    removeGDFromCambiarFoto();
    setTimeout(function() { removeGDButtons(); removeGDFromHeader(); removeGDFromCambiarFoto(); }, 100);
    setTimeout(function() { removeGDButtons(); removeGDFromHeader(); removeGDFromCambiarFoto(); }, 500);
  }
  
  // Ejecutar cuando se abra el dropdown usando eventos de Bootstrap
  const dropdownElement = document.querySelector('#dropdownUsuario');
  if (dropdownElement) {
    dropdownElement.addEventListener('show.bs.dropdown', function() {
      removeGDButtons();
      removeGDFromCambiarFoto();
    });
    dropdownElement.addEventListener('shown.bs.dropdown', function() {
      removeGDButtons();
      removeGDFromHeader();
      removeGDFromCambiarFoto();
      setTimeout(function() { removeGDButtons(); removeGDFromHeader(); removeGDFromCambiarFoto(); }, 50);
      setTimeout(function() { removeGDButtons(); removeGDFromHeader(); removeGDFromCambiarFoto(); }, 200);
      setTimeout(function() { removeGDButtons(); removeGDFromHeader(); removeGDFromCambiarFoto(); }, 500);
    });
  }
  
  // También ejecutar cuando se haga clic
  document.addEventListener('click', function(e) {
    if (e.target.closest('.dropdown-toggle.profile-pic')) {
      setTimeout(function() { removeGDButtons(); removeGDFromHeader(); removeGDFromCambiarFoto(); }, 50);
      setTimeout(function() { removeGDButtons(); removeGDFromHeader(); removeGDFromCambiarFoto(); }, 200);
      setTimeout(function() { removeGDButtons(); removeGDFromHeader(); removeGDFromCambiarFoto(); }, 500);
      setTimeout(function() { removeGDButtons(); removeGDFromHeader(); removeGDFromCambiarFoto(); }, 1000);
    }
  });
  
  // Observar cambios en el DOM
  const observer = new MutationObserver(function(mutations) {
    removeGDButtons();
    removeGDFromHeader();
    removeGDFromCambiarFoto();
  });
  
  const dropdown = document.querySelector('.dropdown-user');
  if (dropdown) {
    observer.observe(dropdown, {
      childList: true,
      subtree: true,
      attributes: true
    });
  }
  
  // Observar cambios en el header también
  const headerObserver = new MutationObserver(function(mutations) {
    removeGDFromHeader();
  });
  
  const header = document.querySelector('.navbar-header');
  if (header) {
    headerObserver.observe(header, {
      childList: true,
      subtree: true,
      attributes: true
    });
  }
  
  // También observar el body por si el dropdown se agrega dinámicamente
  const bodyObserver = new MutationObserver(function(mutations) {
    if (document.querySelector('.dropdown-user')) {
      removeGDButtons();
      removeGDFromCambiarFoto();
    }
    if (document.querySelector('.navbar-header')) {
      removeGDFromHeader();
    }
  });
  
  if (document.body) {
    bodyObserver.observe(document.body, {
      childList: true,
      subtree: true
    });
  }
  
  // Función ultra agresiva para eliminar botones GD - ejecutar cada 100ms
  setInterval(function() {
    removeGDFromCambiarFoto();
    removeGDButtons();
    
    // Buscar específicamente li que contengan solo GD - SOLUCIÓN MÁS AGRESIVA
    const allLis = document.querySelectorAll('.dropdown-user-scroll > li');
    allLis.forEach(function(li) {
      if (!li.querySelector('.user-box') && 
          !li.querySelector('.dropdown-item') && 
          !li.querySelector('.dropdown-divider')) {
        const liText = li.textContent ? li.textContent.trim() : '';
        const hasCircular = li.querySelector('.bg-primary.rounded-circle, .btn-primary.rounded-circle, div.rounded-circle.bg-primary, button.rounded-circle.bg-primary, a.rounded-circle.bg-primary, span.rounded-circle.bg-primary');
        
        // Si tiene un elemento circular azul y no tiene dropdown-item, eliminarlo
        if (hasCircular && !li.querySelector('.dropdown-item') && !li.querySelector('.user-box')) {
          li.style.display = 'none';
          li.style.visibility = 'hidden';
          li.style.opacity = '0';
          li.style.height = '0';
          li.style.margin = '0';
          li.style.padding = '0';
          li.style.overflow = 'hidden';
          li.style.width = '0';
          try { li.remove(); } catch(e) {}
        } else if (liText === 'GD' || liText === 'G D' || (liText.length === 2 && liText.match(/^[A-Z]{2}$/))) {
          li.style.display = 'none';
          li.style.visibility = 'hidden';
          li.style.opacity = '0';
          li.style.height = '0';
          li.style.margin = '0';
          li.style.padding = '0';
          li.style.overflow = 'hidden';
          li.style.width = '0';
          try { li.remove(); } catch(e) {}
        }
      }
    });
    
    // Buscar en todo el documento elementos con texto "GD" que sean botones circulares azules
    const allElements = document.querySelectorAll('.dropdown-user *');
    allElements.forEach(function(el) {
      const text = (el.textContent || el.innerText || '').trim();
      const classes = (el.className || '').toString();
      
      // Si el texto es exactamente "GD" y está en un elemento circular azul
      if (text === 'GD' || text === 'G D' || (text.length === 2 && text.match(/^[A-Z]{2}$/))) {
        const parent = el.parentElement;
        if (parent && (parent.classList.contains('rounded-circle') || parent.classList.contains('bg-primary') || parent.classList.contains('btn-primary'))) {
          // Si el padre no está en el user-box, eliminarlo
          if (!parent.closest('.user-box') && !parent.closest('.avatar-lg')) {
            parent.style.display = 'none';
            parent.style.visibility = 'hidden';
            parent.style.opacity = '0';
            parent.style.width = '0';
            parent.style.height = '0';
            try { parent.remove(); } catch(e) {}
          }
        } else if (el.classList.contains('rounded-circle') && (el.classList.contains('bg-primary') || el.classList.contains('btn-primary'))) {
          // Si el elemento mismo es circular azul y no está en user-box
          if (!el.closest('.user-box') && !el.closest('.avatar-lg')) {
            el.style.display = 'none';
            el.style.visibility = 'hidden';
            el.style.opacity = '0';
            el.style.width = '0';
            el.style.height = '0';
            try { el.remove(); } catch(e) {}
          }
        }
      }
    });
    
    // También buscar elementos circulares azules que no deberían estar
    const unwantedCircles = document.querySelectorAll('.bg-primary.rounded-circle:not(.btn-info):not(.avatar-lg):not(.avatar-lg *), .btn-primary.rounded-circle:not(.btn-info)');
    unwantedCircles.forEach(function(el) {
      if (!el.closest('.user-box') && !el.closest('.avatar-lg')) {
        const text = (el.textContent || el.innerText || '').trim();
        // Si tiene texto de 2 letras mayúsculas (como GD), eliminarlo
        if (text.length === 2 && text.match(/^[A-Z]{2}$/)) {
          el.style.display = 'none';
          el.style.visibility = 'hidden';
          el.style.opacity = '0';
          el.style.width = '0';
          el.style.height = '0';
          try { el.remove(); } catch(e) {}
        }
      }
    });
  }, 100); // Ejecutar cada 100ms para eliminar GD más rápido
})();
</script>

