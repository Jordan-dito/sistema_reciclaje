<?php
// Componente reutilizable para el logo del header principal
// Variables esperadas:
// - $basePath: prefijo para las rutas ('' o '../')
$basePath = isset($basePath) ? rtrim($basePath, '/') : '';
$basePath = $basePath !== '' ? $basePath . '/' : '';
$logoPath = $basePath . 'assets/img/logo.jpg';
$dashboardPath = $basePath . 'Dashboard.php';
?>
<style>
.main-header-logo .logo-header {
  width: 100% !important;
  min-width: 100% !important;
  max-width: 100% !important;
  padding-left: 15px !important;
  padding-right: 15px !important;
  overflow: visible !important;
}

.main-header-logo .logo-header .nav-toggle {
  position: relative !important;
  right: auto !important;
  margin-right: 10px !important;
}

.main-header-logo .logo-header > div:nth-child(2) {
  flex: 1 !important;
  min-width: 0 !important;
  overflow: visible !important;
}

.main-header-logo .logo-header span {
  white-space: nowrap !important;
  overflow: visible !important;
  text-overflow: clip !important;
}
</style>
<div class="main-header-logo">
  <!-- Logo Header -->
  <div class="logo-header" data-background-color="dark" style="display: flex; justify-content: space-between; align-items: center; width: 100%; padding: 0 15px;">
    <div class="nav-toggle" style="order: 1; flex-shrink: 0; margin-right: 10px;">
      <button class="btn btn-toggle toggle-sidebar">
        <i class="gg-menu-right"></i>
      </button>
      <button class="btn btn-toggle sidenav-toggler">
        <i class="gg-menu-left"></i>
      </button>
    </div>
    <div style="order: 2; flex: 1; display: flex; align-items: center; justify-content: flex-end; gap: 10px; min-width: 0; overflow: visible; padding-right: 15px;">
      <span style="color: #fff; font-weight: 600; font-size: 16px; white-space: nowrap; overflow: visible;">Hermanos Yanez</span>
      <a href="<?php echo $dashboardPath; ?>" class="logo" style="display: inline-block; flex-shrink: 0;">
        <img
          src="<?php echo $logoPath; ?>"
          alt="Hermanos Yanez"
          class="navbar-brand"
          height="50"
          style="object-fit: contain; border-radius: 8px; max-width: 50px;"
        />
      </a>
    </div>
    <button class="topbar-toggler more" style="order: 3; flex-shrink: 0; margin-left: 5px;">
      <i class="gg-more-vertical-alt"></i>
    </button>
  </div>
  <!-- End Logo Header -->
</div>

