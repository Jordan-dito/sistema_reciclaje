<?php
/**
 * Sidebar dinámico basado en módulos de la base de datos
 * Lee los módulos asignados al rol del usuario desde la tabla rol_modulo
 * 
 * Variables esperadas:
 * - $basePath: prefijo para las rutas ('' o '../')
 * - $currentRoute: identificador de la pantalla actual (dashboard, usuarios, etc.)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/modulos_por_rol.php';

$basePath = isset($basePath) ? rtrim($basePath, '/') : '';
$basePath = $basePath !== '' ? $basePath . '/' : '';
$currentRoute = $currentRoute ?? '';

// Obtener rol del usuario desde la sesión
$usuarioRolNombre = isset($_SESSION['usuario_rol']) ? trim($_SESSION['usuario_rol']) : '';
$usuarioRolId = null;

// Obtener ID del rol del usuario desde la base de datos
try {
    $db = getDB();
    if (!empty($usuarioRolNombre)) {
        $stmt = $db->prepare("SELECT id FROM roles WHERE nombre = ? LIMIT 1");
        $stmt->execute([$usuarioRolNombre]);
        $rol = $stmt->fetch();
        $usuarioRolId = $rol ? $rol['id'] : null;
    }
} catch (Exception $e) {
    error_log("Error al obtener rol del usuario en sidebar: " . $e->getMessage());
    // Fallback: usar valores por defecto si hay error
    $usuarioRolId = null;
}

// Obtener módulos asignados al rol del usuario desde la base de datos
$modulosAsignados = [];
if ($usuarioRolId) {
    $modulosAsignados = getModulosPorRol($usuarioRolId);
}

// Mapeo de nombres de módulos a sus sub-items y rutas
// Este mapeo define la estructura de cada módulo (sub-items)
$modulosConfig = [
    'Dashboard' => [
        'ruta' => 'Dashboard.php',
        'subitems' => [],
        'routes' => ['dashboard']
    ],
    'Gestión de Usuario' => [
        'ruta' => '#',
        'subitems' => [
            ['nombre' => 'Usuario', 'ruta' => 'usuarios/index.php', 'route' => 'usuarios'],
            ['nombre' => 'Roles', 'ruta' => 'roles/index.php', 'route' => 'roles']
        ],
        'routes' => ['usuarios', 'roles']
    ],
    'Módulo de Parámetros' => [
        'ruta' => '#',
        'subitems' => [
            ['nombre' => 'Categorías de Materiales', 'ruta' => 'categorias/index.php', 'route' => 'categorias'],
            ['nombre' => 'Materiales Reciclables', 'ruta' => 'materiales/index.php', 'route' => 'materiales'],
            ['nombre' => 'Unidades de Medida', 'ruta' => 'unidades/index.php', 'route' => 'unidades'],
            ['nombre' => 'Material Comercializable', 'ruta' => 'productos/index.php', 'route' => 'productos']
        ],
        'routes' => ['categorias', 'materiales', 'unidades', 'productos']
    ],
    'Control de Sucursales' => [
        'ruta' => '#',
        'subitems' => [
            ['nombre' => 'Sucursales', 'ruta' => 'sucursales/index.php', 'route' => 'sucursales'],
            ['nombre' => 'Gestión de Materiales por Sucursal', 'ruta' => 'inventarios/index.php', 'route' => 'inventarios']
        ],
        'routes' => ['sucursales', 'inventarios']
    ],
    'Administración de Personal' => [
        'ruta' => '#',
        'subitems' => [
            ['nombre' => 'Gestión de Personal', 'ruta' => '#', 'route' => 'personal', 'disabled' => true]
        ],
        'routes' => ['personal']
    ],
    'Reporte' => [
        'ruta' => '#',
        'subitems' => [
            ['nombre' => 'Reporte de Inventarios', 'ruta' => 'reportes/index.php', 'route' => 'reportes'],
            ['nombre' => 'Reporte de Compras', 'ruta' => 'reportes/index.php', 'route' => 'reportes'],
            ['nombre' => 'Reporte de Ventas', 'ruta' => 'reportes/index.php', 'route' => 'reportes'],
            ['nombre' => 'Reporte de Productos', 'ruta' => 'reportes/index.php', 'route' => 'reportes'],
            ['nombre' => 'Reporte de Materiales por Categoría', 'ruta' => 'reportes/index.php', 'route' => 'reportes'],
            ['nombre' => 'Reporte de Sucursales', 'ruta' => 'reportes/index.php', 'route' => 'reportes'],
            ['nombre' => 'Reporte de Usuarios por Rol', 'ruta' => 'reportes/index.php', 'route' => 'reportes']
        ],
        'routes' => ['reportes']
    ],
    'Gestión de Inventario' => [
        'ruta' => '#',
        'subitems' => [
            ['nombre' => 'Compra', 'ruta' => 'compras/index.php', 'route' => 'compras'],
            ['nombre' => 'Venta', 'ruta' => 'ventas/index.php', 'route' => 'ventas']
        ],
        'routes' => ['compras', 'ventas']
    ],
    'Relaciones Comerciales' => [
        'ruta' => '#',
        'subitems' => [
            ['nombre' => 'Proveedores', 'ruta' => 'proveedores/index.php', 'route' => 'proveedores'],
            ['nombre' => 'Cliente', 'ruta' => 'clientes/index.php', 'route' => 'clientes']
        ],
        'routes' => ['proveedores', 'clientes']
    ]
];

// Función para verificar si una ruta está activa
function isRouteActive($config, $currentRoute) {
    if (isset($config['routes'])) {
        return in_array($currentRoute, $config['routes'], true);
    }
    return false;
}
?>
<ul class="nav nav-secondary">
  <?php if (empty($modulosAsignados)): ?>
    <!-- Si no hay módulos asignados, mostrar Dashboard por defecto -->
    <li class="nav-item <?php echo $currentRoute === 'dashboard' ? 'active' : ''; ?>">
      <a href="<?php echo $basePath; ?>Dashboard.php">
        <i class="fas fa-home"></i>
        <p>Dashboard</p>
      </a>
    </li>
  <?php else: ?>
    <?php 
      $mostrarSeccionReporte = false;
      foreach ($modulosAsignados as $modulo): 
        $moduloNombre = $modulo['nombre'];
        $moduloIcono = $modulo['icono'] ?? 'fas fa-circle';
        $config = $modulosConfig[$moduloNombre] ?? null;
        $tieneSubitems = $config && !empty($config['subitems']);
        $isActive = false;
        
        if ($config) {
          $isActive = isRouteActive($config, $currentRoute);
        }
        
        // Verificar si es el módulo de Reporte para mostrar la sección
        if ($moduloNombre === 'Reporte') {
          $mostrarSeccionReporte = true;
        }
    ?>
      
      <?php if ($tieneSubitems): ?>
        <!-- Módulo con sub-items -->
        <li class="nav-item submenu <?php echo $isActive ? 'active' : ''; ?>">
          <a data-bs-toggle="collapse" 
             href="#menu<?php echo preg_replace('/[^a-zA-Z0-9]/', '', $moduloNombre); ?>" 
             class="<?php echo $isActive ? '' : 'collapsed'; ?>" 
             aria-expanded="<?php echo $isActive ? 'true' : 'false'; ?>">
            <i class="<?php echo htmlspecialchars($moduloIcono); ?>"></i>
            <p><?php echo htmlspecialchars($moduloNombre); ?></p>
            <span class="caret"></span>
          </a>
          <div class="collapse <?php echo $isActive ? 'show' : ''; ?>" id="menu<?php echo preg_replace('/[^a-zA-Z0-9]/', '', $moduloNombre); ?>">
            <ul class="nav nav-collapse">
              <?php foreach ($config['subitems'] as $subitem): ?>
                <li class="<?php echo (isset($subitem['route']) && $subitem['route'] === $currentRoute) ? 'active' : ''; ?>">
                  <?php if (isset($subitem['disabled']) && $subitem['disabled']): ?>
                    <a href="#" onclick="return false;" style="cursor: not-allowed; opacity: 0.6;">
                      <span class="sub-item">
                        <?php echo htmlspecialchars($subitem['nombre']); ?>
                        <small class="text-muted">(Próximamente)</small>
                      </span>
                    </a>
                  <?php else: ?>
                    <a href="<?php echo $basePath . htmlspecialchars($subitem['ruta']); ?>">
                      <span class="sub-item"><?php echo htmlspecialchars($subitem['nombre']); ?></span>
                    </a>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </li>
      <?php else: ?>
        <!-- Módulo sin sub-items (como Dashboard) -->
        <li class="nav-item <?php echo $isActive ? 'active' : ''; ?>">
          <a href="<?php echo $basePath . ($config ? $config['ruta'] : 'Dashboard.php'); ?>">
            <i class="<?php echo htmlspecialchars($moduloIcono); ?>"></i>
            <p><?php echo htmlspecialchars($moduloNombre); ?></p>
          </a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
    
    <!-- Sección de Reporte (si existe el módulo Reporte) -->
    <?php if ($mostrarSeccionReporte): ?>
      <li class="nav-section">
        <span class="sidebar-mini-icon">
          <i class="fa fa-ellipsis-h"></i>
        </span>
        <h4 class="text-section">Reporte</h4>
      </li>
    <?php endif; ?>
  <?php endif; ?>
</ul>
