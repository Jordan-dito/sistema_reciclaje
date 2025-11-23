<?php
// Componente reutilizable para el logo del header principal
// Variables esperadas:
// - $basePath: prefijo para las rutas ('' o '../')
$basePath = isset($basePath) ? rtrim($basePath, '/') : '';
$basePath = $basePath !== '' ? $basePath . '/' : '';
$logoPath = $basePath . 'assets/img/logo.jpg';
$dashboardPath = $basePath . 'Dashboard.php';
?>
<div class="main-header-logo">
  <!-- Logo Header -->
  <div class="logo-header" data-background-color="dark" style="display: flex; justify-content: space-between; align-items: center;">
    <div class="nav-toggle" style="order: 1;">
      <button class="btn btn-toggle toggle-sidebar">
        <i class="gg-menu-right"></i>
      </button>
      <button class="btn btn-toggle sidenav-toggler">
        <i class="gg-menu-left"></i>
      </button>
    </div>
    <div style="order: 2; flex-grow: 1; text-align: right; display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding-right: 15px;">
      <span style="color: #fff; font-weight: 600; font-size: 16px;">Hermanos Yanez</span>
      <a href="<?php echo $dashboardPath; ?>" class="logo" style="display: inline-block;">
        <img
          src="<?php echo $logoPath; ?>"
          alt="Hermanos Yanez"
          class="navbar-brand"
          height="50"
          style="object-fit: contain; border-radius: 8px;"
        />
      </a>
    </div>
    <button class="topbar-toggler more" style="order: 3;">
      <i class="gg-more-vertical-alt"></i>
    </button>
  </div>
  <!-- End Logo Header -->
</div>

