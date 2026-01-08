<?php
/**
 * Script de instalación: agrega el módulo "Asistente Personal" a la tabla `modulos`
 * y lo asigna a un rol (intenta encontrar 'Administrador', si no usa id=1).
 * Ejecutar desde navegador o CLI (php scripts/instalar_modulo_asistente.php).
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/modulos_por_rol.php';

try {
    $db = getDB();

    // Verificar si ya existe el módulo
    $nombre = 'Gestión de Personal';
    $stmt = $db->prepare("SELECT id FROM modulos WHERE nombre = ? LIMIT 1");
    $stmt->execute([$nombre]);
    $row = $stmt->fetch();

    if ($row) {
        $modulo_id = $row['id'];
        echo "El módulo ya existe con ID: {$modulo_id}\n";
    } else {
        // Insertar nuevo módulo
        $descripcion = 'Gestión del personal de la empresa';
        $icono = 'fas fa-user-tie';
        $orden = 5;
        $estado = 'activo';

        $stmt = $db->prepare("INSERT INTO modulos (nombre, descripcion, icono, orden, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $icono, $orden, $estado]);
        $modulo_id = $db->lastInsertId();
        echo "Módulo creado con ID: {$modulo_id}\n";
    }

    // Buscar rol 'Administrador'
    $stmt = $db->prepare("SELECT id FROM roles WHERE nombre LIKE ? LIMIT 1");
    $stmt->execute(['%Administrador%']);
    $rol = $stmt->fetch();

    if ($rol) {
        $rol_id = $rol['id'];
        echo "Rol encontrado: ID {$rol_id}\n";
    } else {
        // Fallback a id=1
        $rol_id = 1;
        echo "Rol 'Administrador' no encontrado. Usando rol_id = 1 (por defecto).\n";
    }

    // Asignar módulo al rol
    $result = asignarModuloARol($rol_id, $modulo_id);
    if ($result['success']) {
        echo "Módulo asignado al rol {$rol_id} correctamente.\n";
    } else {
        echo "Error asignando módulo: " . $result['message'] . "\n";
    }

    echo "Instalación finalizada.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
