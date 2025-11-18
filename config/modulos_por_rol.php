<?php
/**
 * Definición estática de módulos por rol
 * Los módulos están definidos aquí, no en la base de datos
 */

// Módulos disponibles en el sistema (definidos estáticamente)
$MODULOS_DISPONIBLES = [
    [
        'id' => 1,
        'nombre' => 'Dashboard',
        'ruta' => 'Dashboard.php',
        'icono' => 'fas fa-home',
        'descripcion' => 'Panel principal del sistema'
    ],
    [
        'id' => 2,
        'nombre' => 'Usuarios',
        'ruta' => 'usuarios/index.php',
        'icono' => 'fas fa-users',
        'descripcion' => 'Gestión de usuarios del sistema'
    ],
    [
        'id' => 3,
        'nombre' => 'Roles',
        'ruta' => 'roles/index.php',
        'icono' => 'fas fa-user-tag',
        'descripcion' => 'Gestión de roles y permisos'
    ],
    [
        'id' => 4,
        'nombre' => 'Categorías',
        'ruta' => 'categorias/index.php',
        'icono' => 'fas fa-tags',
        'descripcion' => 'Gestión de categorías'
    ],
    [
        'id' => 5,
        'nombre' => 'Materiales',
        'ruta' => 'materiales/index.php',
        'icono' => 'fas fa-cube',
        'descripcion' => 'Gestión de materiales'
    ],
    [
        'id' => 6,
        'nombre' => 'Unidades',
        'ruta' => 'unidades/index.php',
        'icono' => 'fas fa-ruler',
        'descripcion' => 'Gestión de unidades de medida'
    ],
    [
        'id' => 7,
        'nombre' => 'Sucursales',
        'ruta' => 'sucursales/index.php',
        'icono' => 'fas fa-building',
        'descripcion' => 'Gestión de sucursales'
    ],
    [
        'id' => 8,
        'nombre' => 'Inventarios',
        'ruta' => 'inventarios/index.php',
        'icono' => 'fas fa-boxes',
        'descripcion' => 'Control de inventarios'
    ],
    [
        'id' => 9,
        'nombre' => 'Proveedores',
        'ruta' => 'proveedores/index.php',
        'icono' => 'fas fa-truck',
        'descripcion' => 'Gestión de proveedores'
    ],
    [
        'id' => 10,
        'nombre' => 'Clientes',
        'ruta' => 'clientes/index.php',
        'icono' => 'fas fa-user-tie',
        'descripcion' => 'Gestión de clientes'
    ],
    [
        'id' => 11,
        'nombre' => 'Compras',
        'ruta' => 'compras/index.php',
        'icono' => 'fas fa-shopping-cart',
        'descripcion' => 'Registro de compras de materiales'
    ],
    [
        'id' => 12,
        'nombre' => 'Ventas',
        'ruta' => 'ventas/index.php',
        'icono' => 'fas fa-cash-register',
        'descripcion' => 'Registro de ventas de materiales'
    ],
    [
        'id' => 13,
        'nombre' => 'Reportes',
        'ruta' => 'reportes/index.php',
        'icono' => 'fas fa-chart-bar',
        'descripcion' => 'Reportes y estadísticas'
    ],
    [
        'id' => 14,
        'nombre' => 'Productos',
        'ruta' => 'productos/index.php',
        'icono' => 'fas fa-box',
        'descripcion' => 'Gestión de productos'
    ]
];

// Módulos asignados al GERENTE (rol_id = 2)
// Basado en las rutas definidas en sidebar.php
$MODULOS_GERENTE = [
    'Dashboard.php',
    'usuarios/index.php',
    'roles/index.php',
    'categorias/index.php',
    'materiales/index.php',
    'unidades/index.php',
    'sucursales/index.php',
    'inventarios/index.php',
    'proveedores/index.php',
    'clientes/index.php',
    'compras/index.php',
    'ventas/index.php',
    'reportes/index.php'
];

// Módulos asignados al ADMINISTRADOR (rol_id = 1)
// El administrador tiene acceso a TODOS los módulos
$MODULOS_ADMINISTRADOR = [
    'Dashboard.php',
    'usuarios/index.php',
    'roles/index.php',
    'categorias/index.php',
    'materiales/index.php',
    'unidades/index.php',
    'sucursales/index.php',
    'inventarios/index.php',
    'proveedores/index.php',
    'clientes/index.php',
    'compras/index.php',
    'ventas/index.php',
    'reportes/index.php',
    'productos/index.php'
];

/**
 * Obtener módulos asignados a un rol
 */
function getModulosPorRol($rol_id) {
    global $MODULOS_GERENTE, $MODULOS_ADMINISTRADOR;
    
    if ($rol_id == 1) {
        return $MODULOS_ADMINISTRADOR;
    } elseif ($rol_id == 2) {
        return $MODULOS_GERENTE;
    }
    
    return [];
}

/**
 * Obtener todos los módulos disponibles con información de asignación
 */
function getModulosConAsignacion($rol_id = null) {
    global $MODULOS_DISPONIBLES;
    
    $rutasAsignadas = [];
    if ($rol_id) {
        $rutasAsignadas = getModulosPorRol($rol_id);
    }
    
    $modulos = [];
    foreach ($MODULOS_DISPONIBLES as $modulo) {
        $modulo['asignado'] = in_array($modulo['ruta'], $rutasAsignadas) ? 1 : 0;
        $modulo['estado'] = 'activo';
        $modulos[] = $modulo;
    }
    
    return $modulos;
}

