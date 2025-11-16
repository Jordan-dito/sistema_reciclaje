<?php
// Sidebar de navegación para el panel de administración
// Variables esperadas:
// - $basePath: prefijo para las rutas ('' o '../')
// - $currentRoute: identificador de la pantalla actual (dashboard, usuarios, etc.)

$basePath = isset($basePath) ? rtrim($basePath, '/') : '';
$basePath = $basePath !== '' ? $basePath . '/' : '';
$currentRoute = $currentRoute ?? '';

// Obtener rol del usuario desde la sesión
$usuarioRol = isset($_SESSION['usuario_rol']) ? trim(strtolower($_SESSION['usuario_rol'])) : '';

$gestionRoutes = ['usuarios', 'roles', 'sucursales', 'categorias', 'materiales', 'productos', 'unidades'];
$inventarioRoutes = ['inventarios', 'clientes', 'proveedores', 'compras', 'ventas'];

$gestionOpen = in_array($currentRoute, $gestionRoutes, true);
$inventarioOpen = in_array($currentRoute, $inventarioRoutes, true);

// Determinar qué menús mostrar según el rol
// Administrador: SÍ ve Gestión (Dashboard, Gestión, Inventario, Reportes)
// Gerente: SÍ ve Gestión (Dashboard, Gestión, Inventario, Reportes)
$mostrarGestion = ($usuarioRol === 'administrador' || $usuarioRol === 'gerente' || $usuarioRol === 'super administrador');
$mostrarInventario = true; // Todos los roles ven inventario
$mostrarReportes = true; // Todos los roles ven reportes
?>
<ul class="nav nav-secondary">
  <li class="nav-item <?php echo $currentRoute === 'dashboard' ? 'active' : ''; ?>">
    <a href="<?php echo $basePath; ?>Dashboard.php">
      <i class="fas fa-home"></i>
      <p>Dashboard</p>
    </a>
  </li>

  <?php if ($mostrarGestion): ?>
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
        <li class="<?php echo $currentRoute === 'categorias' ? 'active' : ''; ?>">
          <a href="<?php echo $basePath; ?>categorias/index.php">
            <span class="sub-item">Categorías</span>
          </a>
        </li>
        <li class="<?php echo $currentRoute === 'materiales' ? 'active' : ''; ?>">
          <a href="<?php echo $basePath; ?>materiales/index.php">
            <span class="sub-item">Materiales</span>
          </a>
        </li>
        <li class="<?php echo $currentRoute === 'productos' ? 'active' : ''; ?>">
          <a href="<?php echo $basePath; ?>productos/index.php">
            <span class="sub-item">Productos</span>
          </a>
        </li>
        <li class="<?php echo $currentRoute === 'unidades' ? 'active' : ''; ?>">
          <a href="<?php echo $basePath; ?>unidades/index.php">
            <span class="sub-item">Unidades</span>
          </a>
        </li>
      </ul>
    </div>
  </li>
  <?php endif; ?>

  <?php if ($mostrarInventario): ?>
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
  <?php endif; ?>

  <?php if ($mostrarReportes): ?>
  <li class="nav-section">
    <span class="sidebar-mini-icon">
      <i class="fa fa-ellipsis-h"></i>
    </span>
    <h4 class="text-section">Reportes</h4>
  </li>
  <li class="nav-item <?php echo $currentRoute === 'reportes' ? 'active' : ''; ?>">
    <a href="<?php echo $basePath; ?>reportes/index.php">
      <i class="fas fa-chart-line"></i>
      <p>Reportes</p>
    </a>
  </li>
  <?php endif; ?>
</ul>
