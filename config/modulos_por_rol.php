<?php
/**
 * Gestión de módulos por rol desde base de datos
 * Los módulos se almacenan en las tablas: modulos y rol_modulo
 */

require_once __DIR__ . '/database.php';

/**
 * Obtener todos los módulos disponibles desde la base de datos
 */
function getModulosDisponibles() {
    try {
        $db = getDB();
        $stmt = $db->query("
            SELECT id, nombre, descripcion, icono, orden, estado 
            FROM modulos 
            WHERE estado = 'activo' 
            ORDER BY orden ASC, nombre ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener módulos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener módulos asignados a un rol desde la base de datos
 */
function getModulosPorRol($rol_id) {
    try {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT m.id, m.nombre, m.descripcion, m.icono, m.orden
            FROM modulos m
            INNER JOIN rol_modulo rm ON m.id = rm.modulo_id
            WHERE rm.rol_id = ? AND rm.estado = 'asignado' AND m.estado = 'activo'
            ORDER BY m.orden ASC, m.nombre ASC
        ");
        $stmt->execute([$rol_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener módulos por rol: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener todos los módulos con información de asignación para un rol
 */
function getModulosConAsignacion($rol_id = null) {
    try {
        $db = getDB();
        
        if ($rol_id) {
            // Obtener módulos con su estado de asignación para el rol específico
            $stmt = $db->prepare("
                SELECT 
                    m.id,
                    m.nombre,
                    m.descripcion,
                    m.icono,
                    m.orden,
                    m.estado,
                    COALESCE(rm.estado, 'no_asignado') as asignacion,
                    CASE 
                        WHEN rm.estado = 'asignado' THEN 1 
                        ELSE 0 
                    END as asignado
                FROM modulos m
                LEFT JOIN rol_modulo rm ON m.id = rm.modulo_id AND rm.rol_id = ?
                WHERE m.estado = 'activo'
                ORDER BY m.orden ASC, m.nombre ASC
            ");
            $stmt->execute([$rol_id]);
        } else {
            // Obtener todos los módulos sin información de asignación
            $stmt = $db->query("
                SELECT 
                    id,
                    nombre,
                    descripcion,
                    icono,
                    orden,
                    estado,
                    'no_asignado' as asignacion,
                    0 as asignado
                FROM modulos
                WHERE estado = 'activo'
                ORDER BY orden ASC, nombre ASC
            ");
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener módulos con asignación: " . $e->getMessage());
        return [];
    }
}

/**
 * Asignar un módulo a un rol
 */
function asignarModuloARol($rol_id, $modulo_id) {
    try {
        $db = getDB();
        
        // Verificar si ya existe la relación
        $stmt = $db->prepare("SELECT id FROM rol_modulo WHERE rol_id = ? AND modulo_id = ?");
        $stmt->execute([$rol_id, $modulo_id]);
        
        if ($stmt->fetch()) {
            // Actualizar estado a asignado
            $stmt = $db->prepare("
                UPDATE rol_modulo 
                SET estado = 'asignado', fecha_actualizacion = NOW() 
                WHERE rol_id = ? AND modulo_id = ?
            ");
            $stmt->execute([$rol_id, $modulo_id]);
        } else {
            // Crear nueva relación
            $stmt = $db->prepare("
                INSERT INTO rol_modulo (rol_id, modulo_id, estado) 
                VALUES (?, ?, 'asignado')
            ");
            $stmt->execute([$rol_id, $modulo_id]);
        }
        
        return ['success' => true, 'message' => 'Módulo asignado exitosamente'];
    } catch (Exception $e) {
        error_log("Error al asignar módulo: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al asignar módulo: ' . $e->getMessage()];
    }
}

/**
 * Quitar un módulo de un rol
 */
function quitarModuloDeRol($rol_id, $modulo_id) {
    try {
        $db = getDB();
        
        // Actualizar estado a no_asignado en lugar de eliminar
        $stmt = $db->prepare("
            UPDATE rol_modulo 
            SET estado = 'no_asignado', fecha_actualizacion = NOW() 
            WHERE rol_id = ? AND modulo_id = ?
        ");
        $stmt->execute([$rol_id, $modulo_id]);
        
        return ['success' => true, 'message' => 'Módulo removido exitosamente'];
    } catch (Exception $e) {
        error_log("Error al quitar módulo: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al quitar módulo: ' . $e->getMessage()];
    }
}

// Obtener módulos disponibles (para compatibilidad con código existente)
$MODULOS_DISPONIBLES = getModulosDisponibles();
