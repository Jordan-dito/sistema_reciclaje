<?php
// Componente reutilizable para el logo del sidebar
// Variables esperadas:
// - $basePath: prefijo para las rutas ('' o '../')
$basePath = isset($basePath) ? rtrim($basePath, '/') : '';
$basePath = $basePath !== '' ? $basePath . '/' : '';
$logoPath = $basePath . 'assets/img/logo.jpg';
$dashboardPath = $basePath . 'Dashboard.php';
?>
<div class="sidebar-logo">
  <div class="logo-header" data-background-color="dark">
    <a href="<?php echo $dashboardPath; ?>" class="logo">
      <img
        src="<?php echo $logoPath; ?>"
        alt="HNOSYÁNEZ S.A."
        class="navbar-brand"
        height="50"
        style="object-fit: contain; border-radius: 8px;"
      />
    </a>
    <div class="nav-toggle">
      <button class="btn btn-toggle toggle-sidebar">
        <i class="gg-menu-right"></i>
      </button>
      <?php if ($basePath === ''): ?>
        <button class="btn btn-toggle sidenav-toggler">
          <i class="gg-menu-left"></i>
        </button>
      <?php endif; ?>
    </div>
    <?php if ($basePath === ''): ?>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    <?php endif; ?>
  </div>
</div>

