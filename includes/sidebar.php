<?php
// Sidebar de navegación para el panel de administración
// Variables esperadas:
// - $basePath: prefijo para las rutas ('' o '../')
// - $currentRoute: identificador de la pantalla actual (dashboard, usuarios, etc.)

$basePath = isset($basePath) ? rtrim($basePath, '/') : '';
$basePath = $basePath !== '' ? $basePath . '/' : '';
$currentRoute = $currentRoute ?? '';

$gestionRoutes = ['usuarios', 'roles', 'sucursales'];
$inventarioRoutes = ['inventarios', 'clientes', 'proveedores', 'compras', 'ventas'];

$gestionOpen = in_array($currentRoute, $gestionRoutes, true);
$inventarioOpen = in_array($currentRoute, $inventarioRoutes, true);
?>
<ul class="nav nav-secondary">
  <li class="nav-item <?php echo $currentRoute === 'dashboard' ? 'active' : ''; ?>">
    <a href="<?php echo $basePath; ?>Dashboard.php">
      <i class="fas fa-home"></i>
      <p>Dashboard</p>
    </a>
  </li>

  <li class="nav-section">
    <span class="sidebar-mini-icon">
      <i class="fa fa-ellipsis-h"></i>
    </span>
    <h4 class="text-section">Administrador</h4>
  </li>

  <li class="nav-item submenu <?php echo $gestionOpen ? 'active' : ''; ?>">
    <a data-bs-toggle="collapse" href="#menuGestion" class="<?php echo $gestionOpen ? '' : 'collapsed'; ?>" aria-expanded="<?php echo $gestionOpen ? 'true' : 'false'; ?>">
      <i class="fas fa-user-cog"></i>
      <p>Gestión</p>
      <span class="caret"></span>
    </a>
    <div class="collapse <?php echo $gestionOpen ? 'show' : ''; ?>" id="menuGestion">
      <ul class="nav nav-collapse">
        <li class="<?php echo $currentRoute === 'usuarios' ? 'active' : ''; ?>">
          <a href="<?php echo $basePath; ?>usuarios/index.php">
            <span class="sub-item">Usuarios</span>
          </a>
        </li>
        <li class="<?php echo $currentRoute === 'roles' ? 'active' : ''; ?>">
          <a href="<?php echo $basePath; ?>roles/index.php">
            <span class="sub-item">Roles</span>
          </a>
        </li>
        <li class="<?php echo $currentRoute === 'sucursales' ? 'active' : ''; ?>">
          <a href="<?php echo $basePath; ?>sucursales/index.php">
            <span class="sub-item">Sucursales</span>
          </a>
        </li>
      </ul>
    </div>
  </li>

  <li class="nav-item submenu <?php echo $inventarioOpen ? 'active' : ''; ?>">
    <a data-bs-toggle="collapse" href="#menuInventario" class="<?php echo $inventarioOpen ? '' : 'collapsed'; ?>" aria-expanded="<?php echo $inventarioOpen ? 'true' : 'false'; ?>">
      <i class="fas fa-warehouse"></i>
      <p>Inventario</p>
      <span class="caret"></span>
    </a>
    <div class="collapse <?php echo $inventarioOpen ? 'show' : ''; ?>" id="menuInventario">
      <ul class="nav nav-collapse">
        <li class="<?php echo $currentRoute === 'inventarios' ? 'active' : ''; ?>">
          <a href="<?php echo $basePath; ?>inventarios/index.php">
            <span class="sub-item">Inventario</span>
          </a>
        </li>
        <li class="<?php echo $currentRoute === 'clientes' ? 'active' : ''; ?>">
          <a href="<?php echo $basePath; ?>clientes/index.php">
            <span class="sub-item">Clientes</span>
          </a>
        </li>
        <li class="<?php echo $currentRoute === 'proveedores' ? 'active' : ''; ?>">
          <a href="<?php echo $basePath; ?>proveedores/index.php">
            <span class="sub-item">Proveedores</span>
          </a>
        </li>
        <li class="<?php echo $currentRoute === 'compras' ? 'active' : ''; ?>">
          <a href="<?php echo $basePath; ?>compras/index.php">
            <span class="sub-item">Compras</span>
          </a>
        </li>
        <li class="<?php echo $currentRoute === 'ventas' ? 'active' : ''; ?>">
          <a href="<?php echo $basePath; ?>ventas/index.php">
            <span class="sub-item">Ventas</span>
          </a>
        </li>
      </ul>
    </div>
  </li>

  <li class="nav-section">
    <span class="sidebar-mini-icon">
      <i class="fa fa-ellipsis-h"></i>
    </span>
    <h4 class="text-section">Reportes</h4>
  </li>
  <li class="nav-item submenu">
    <a data-bs-toggle="collapse" href="#menuReportes" class="collapsed" aria-expanded="false">
      <i class="fas fa-chart-line"></i>
      <p>Reportes</p>
      <span class="caret"></span>
    </a>
    <div class="collapse" id="menuReportes">
      <ul class="nav nav-collapse">
        <li>
          <a href="#" class="text-muted">
            <span class="sub-item">Transportistas</span>
          </a>
        </li>
        <li>
          <a href="#" class="text-muted">
            <span class="sub-item">Stock de Materiales (Kardex)</span>
          </a>
        </li>
      </ul>
    </div>
  </li>
</ul>
