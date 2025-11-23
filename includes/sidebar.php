<?php
// Sidebar de navegación para el panel de administración
// Variables esperadas:
// - $basePath: prefijo para las rutas ('' o '../')
// - $currentRoute: identificador de la pantalla actual (dashboard, usuarios, etc.)

$basePath = isset($basePath) ? rtrim($basePath, '/') : '';
$basePath = $basePath !== '' ? $basePath . '/' : '';
$currentRoute = $currentRoute ?? '';

// Obtener rol del usuario desde la sesión
$usuarioRolNombre = isset($_SESSION['usuario_rol']) ? trim($_SESSION['usuario_rol']) : '';
$usuarioRol = isset($_SESSION['usuario_rol']) ? trim(strtolower($_SESSION['usuario_rol'])) : '';

$gestionRoutes = ['usuarios', 'roles', 'sucursales', 'categorias', 'materiales', 'productos', 'unidades'];
$inventarioRoutes = ['inventarios', 'proveedores', 'compras', 'ventas', 'clientes'];
$gestionInventarioRoutes = ['compras', 'ventas'];
$relacionesComercialesRoutes = ['proveedores', 'clientes'];

$gestionOpen = in_array($currentRoute, $gestionRoutes, true);
$inventarioOpen = in_array($currentRoute, $inventarioRoutes, true);
$gestionInventarioOpen = in_array($currentRoute, $gestionInventarioRoutes, true);
$relacionesComercialesOpen = in_array($currentRoute, $relacionesComercialesRoutes, true);

// Determinar qué menús mostrar según el rol
// Administrador: SÍ ve Gestión (Dashboard, Gestión, Inventario, Reportes)
// Gerente: Menú personalizado
$esGerente = ($usuarioRolNombre === 'Gerente');
$mostrarGestion = ($usuarioRol === 'administrador' || $usuarioRol === 'super administrador');
$mostrarInventario = true; // Todos los roles ven inventario
$mostrarReportes = true; // Todos los roles ven reportes

// Rutas para menú de Gerente
$gerenteGestionUsuarioRoutes = ['usuarios', 'roles'];
$gerenteParametrosRoutes = ['categorias', 'materiales', 'unidades', 'productos'];
$gerenteSucursalesRoutes = ['sucursales'];
$gerenteInventarioRoutes = ['inventarios'];
$gerenteReportesRoutes = ['reportes'];
$gerenteGestionInventarioRoutes = ['compras', 'ventas'];
$gerenteRelacionesComercialesRoutes = ['proveedores', 'clientes'];

$gerenteGestionUsuarioOpen = in_array($currentRoute, $gerenteGestionUsuarioRoutes, true);
$gerenteParametrosOpen = in_array($currentRoute, $gerenteParametrosRoutes, true);
$gerenteSucursalesOpen = in_array($currentRoute, $gerenteSucursalesRoutes, true);
$gerenteInventarioOpen = in_array($currentRoute, $gerenteInventarioRoutes, true);
$gerenteReportesOpen = in_array($currentRoute, $gerenteReportesRoutes, true);
$gerenteGestionInventarioOpen = in_array($currentRoute, $gerenteGestionInventarioRoutes, true);
$gerenteRelacionesComercialesOpen = in_array($currentRoute, $gerenteRelacionesComercialesRoutes, true);
?>
<ul class="nav nav-secondary">
  <?php if ($esGerente): ?>
    <!-- MENÚ ESPECIAL PARA GERENTE -->
    <li class="nav-item <?php echo $currentRoute === 'dashboard' ? 'active' : ''; ?>">
      <a href="<?php echo $basePath; ?>Dashboard.php">
        <i class="fas fa-home"></i>
        <p>Dashboard</p>
      </a>
    </li>

    <!-- Gestión de Usuario -->
    <li class="nav-item submenu <?php echo $gerenteGestionUsuarioOpen ? 'active' : ''; ?>">
      <a data-bs-toggle="collapse" href="#menuGestionUsuario" class="<?php echo $gerenteGestionUsuarioOpen ? '' : 'collapsed'; ?>" aria-expanded="<?php echo $gerenteGestionUsuarioOpen ? 'true' : 'false'; ?>">
        <i class="fas fa-users-cog"></i>
        <p>Gestión de Usuario</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?php echo $gerenteGestionUsuarioOpen ? 'show' : ''; ?>" id="menuGestionUsuario">
        <ul class="nav nav-collapse">
          <li class="<?php echo $currentRoute === 'usuarios' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>usuarios/index.php">
              <span class="sub-item">Usuario</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'roles' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>roles/index.php">
              <span class="sub-item">Roles</span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    <!-- Gestión de Parámetros o Configuración de Material -->
    <li class="nav-item submenu <?php echo $gerenteParametrosOpen ? 'active' : ''; ?>">
      <a data-bs-toggle="collapse" href="#menuParametros" class="<?php echo $gerenteParametrosOpen ? '' : 'collapsed'; ?>" aria-expanded="<?php echo $gerenteParametrosOpen ? 'true' : 'false'; ?>">
        <i class="fas fa-cog"></i>
        <p>Gestión de Parámetros</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?php echo $gerenteParametrosOpen ? 'show' : ''; ?>" id="menuParametros">
        <ul class="nav nav-collapse">
          <li class="<?php echo $currentRoute === 'categorias' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>categorias/index.php">
              <span class="sub-item">Categorías de Materiales</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'materiales' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>materiales/index.php">
              <span class="sub-item">Materiales Reciclables</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'unidades' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>unidades/index.php">
              <span class="sub-item">Unidades de Medida</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'productos' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>productos/index.php">
              <span class="sub-item">Material Comercializable</span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    <!-- Control de Sucursales -->
    <li class="nav-item submenu <?php echo ($gerenteSucursalesOpen || $gerenteInventarioOpen) ? 'active' : ''; ?>">
      <a data-bs-toggle="collapse" href="#menuSucursales" class="<?php echo ($gerenteSucursalesOpen || $gerenteInventarioOpen) ? '' : 'collapsed'; ?>" aria-expanded="<?php echo ($gerenteSucursalesOpen || $gerenteInventarioOpen) ? 'true' : 'false'; ?>">
        <i class="fas fa-building"></i>
        <p>Control de Sucursales</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?php echo ($gerenteSucursalesOpen || $gerenteInventarioOpen) ? 'show' : ''; ?>" id="menuSucursales">
        <ul class="nav nav-collapse">
          <li class="<?php echo $currentRoute === 'sucursales' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>sucursales/index.php">
              <span class="sub-item">Sucursales</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'inventarios' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>inventarios/index.php">
              <span class="sub-item">Gestión de Materiales por Sucursal </span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    <!-- Administración de Personal -->
    <li class="nav-item submenu">
      <a data-bs-toggle="collapse" href="#menuPersonal" class="collapsed" aria-expanded="false">
        <i class="fas fa-user-tie"></i>
        <p>Administración de Personal</p>
        <span class="caret"></span>
      </a>
      <div class="collapse" id="menuPersonal">
        <ul class="nav nav-collapse">
          <li>
            <a href="#" onclick="return false;" style="cursor: not-allowed; opacity: 0.6;">
              <span class="sub-item">Gestión de Personal <small class="text-muted">(Próximamente)</small></span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    <!-- Reporte -->
    <li class="nav-section">
      <span class="sidebar-mini-icon">
        <i class="fa fa-ellipsis-h"></i>
      </span>
      <h4 class="text-section">Reporte</h4>
    </li>
    <li class="nav-item submenu <?php echo $gerenteReportesOpen ? 'active' : ''; ?>">
      <a data-bs-toggle="collapse" href="#menuReportesGerente" class="<?php echo $gerenteReportesOpen ? '' : 'collapsed'; ?>" aria-expanded="<?php echo $gerenteReportesOpen ? 'true' : 'false'; ?>">
        <i class="fas fa-chart-line"></i>
        <p>Reporte</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?php echo $gerenteReportesOpen ? 'show' : ''; ?>" id="menuReportesGerente">
        <ul class="nav nav-collapse">
          <li class="<?php echo $currentRoute === 'reportes' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>reportes/index.php">
              <span class="sub-item">Reporte de Inventarios</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'reportes' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>reportes/index.php">
              <span class="sub-item">Reporte de Compras</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'reportes' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>reportes/index.php">
              <span class="sub-item">Reporte de Ventas</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'reportes' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>reportes/index.php">
              <span class="sub-item">Reporte de Productos</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'reportes' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>reportes/index.php">
              <span class="sub-item">Reporte de Materiales por Categoría</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'reportes' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>reportes/index.php">
              <span class="sub-item">Reporte de Sucursales</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'reportes' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>reportes/index.php">
              <span class="sub-item">Reporte de Usuarios por Rol</span>
            </a>
          </li>
        </ul>
      </div>
    </li>

  <?php else: ?>
    <!-- MENÚ PARA ADMINISTRADOR -->
    <li class="nav-item <?php echo $currentRoute === 'dashboard' ? 'active' : ''; ?>">
      <a href="<?php echo $basePath; ?>Dashboard.php">
        <i class="fas fa-home"></i>
        <p>Dashboard</p>
      </a>
    </li>

    <!-- Gestión de Inventario -->
    <li class="nav-item submenu <?php echo $gestionInventarioOpen ? 'active' : ''; ?>">
      <a data-bs-toggle="collapse" href="#menuGestionInventario" class="<?php echo $gestionInventarioOpen ? '' : 'collapsed'; ?>" aria-expanded="<?php echo $gestionInventarioOpen ? 'true' : 'false'; ?>">
        <i class="fas fa-warehouse"></i>
        <p>Gestión de Inventario</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?php echo $gestionInventarioOpen ? 'show' : ''; ?>" id="menuGestionInventario">
        <ul class="nav nav-collapse">
          <li class="<?php echo $currentRoute === 'compras' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>compras/index.php">
              <span class="sub-item">Compra</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'ventas' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>ventas/index.php">
              <span class="sub-item">Venta</span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    <!-- Relaciones Comerciales -->
    <li class="nav-item submenu <?php echo $relacionesComercialesOpen ? 'active' : ''; ?>">
      <a data-bs-toggle="collapse" href="#menuRelacionesComerciales" class="<?php echo $relacionesComercialesOpen ? '' : 'collapsed'; ?>" aria-expanded="<?php echo $relacionesComercialesOpen ? 'true' : 'false'; ?>">
        <i class="fas fa-handshake"></i>
        <p>Relaciones Comerciales</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?php echo $relacionesComercialesOpen ? 'show' : ''; ?>" id="menuRelacionesComerciales">
        <ul class="nav nav-collapse">
          <li class="<?php echo $currentRoute === 'proveedores' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>proveedores/index.php">
              <span class="sub-item">Proveedores</span>
            </a>
          </li>
          <li class="<?php echo $currentRoute === 'clientes' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>clientes/index.php">
              <span class="sub-item">Cliente</span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    <!-- Reporte -->
    <li class="nav-section">
      <span class="sidebar-mini-icon">
        <i class="fa fa-ellipsis-h"></i>
      </span>
      <h4 class="text-section">Reporte</h4>
    </li>
    <li class="nav-item submenu <?php echo ($currentRoute === 'reportes') ? 'active' : ''; ?>">
      <a data-bs-toggle="collapse" href="#menuReportesAdmin" class="<?php echo ($currentRoute === 'reportes') ? '' : 'collapsed'; ?>" aria-expanded="<?php echo ($currentRoute === 'reportes') ? 'true' : 'false'; ?>">
        <i class="fas fa-chart-line"></i>
        <p>Reporte</p>
        <span class="caret"></span>
      </a>
      <div class="collapse <?php echo ($currentRoute === 'reportes') ? 'show' : ''; ?>" id="menuReportesAdmin">
        <ul class="nav nav-collapse">
          <li class="<?php echo $currentRoute === 'reportes' ? 'active' : ''; ?>">
            <a href="<?php echo $basePath; ?>reportes/index.php?sucursal=all">
              <span class="sub-item">Reportes de Sucursal</span>
            </a>
          </li>
        </ul>
      </div>
    </li>
  <?php endif; ?>
</ul>
