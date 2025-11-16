<?php
/**
 * Script de prueba para diagnosticar problemas en usuarios/api.php
 * Acceder desde: usuarios/test_api.php
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h2>Diagnóstico de API de Usuarios</h2>";

// Paso 1: Verificar archivo .env
echo "<h3>1. Verificando archivo .env</h3>";
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    echo "✓ Archivo .env existe<br>";
    $envContent = file_get_contents($envPath);
    if (strpos($envContent, 'DB_HOST') !== false) {
        echo "✓ DB_HOST encontrado<br>";
    } else {
        echo "✗ DB_HOST NO encontrado<br>";
    }
    if (strpos($envContent, 'DB_NAME') !== false) {
        echo "✓ DB_NAME encontrado<br>";
    } else {
        echo "✗ DB_NAME NO encontrado<br>";
    }
    if (strpos($envContent, 'DB_USER') !== false) {
        echo "✓ DB_USER encontrado<br>";
    } else {
        echo "✗ DB_USER NO encontrado<br>";
    }
} else {
    echo "✗ Archivo .env NO existe en: $envPath<br>";
}

// Paso 2: Cargar configuración
echo "<h3>2. Cargando configuración</h3>";
try {
    require_once __DIR__ . '/../config/database.php';
    echo "✓ Configuración cargada<br>";
    echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NO DEFINIDO') . "<br>";
    echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NO DEFINIDO') . "<br>";
    echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NO DEFINIDO') . "<br>";
} catch (Exception $e) {
    echo "✗ Error al cargar configuración: " . $e->getMessage() . "<br>";
    exit;
}

// Paso 3: Probar conexión
echo "<h3>3. Probando conexión a base de datos</h3>";
try {
    $db = getDB();
    echo "✓ Conexión exitosa<br>";
} catch (Exception $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "<br>";
    exit;
}

// Paso 4: Verificar tablas
echo "<h3>4. Verificando tablas</h3>";
try {
    $stmt = $db->query("SHOW TABLES LIKE 'usuarios'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Tabla 'usuarios' existe<br>";
    } else {
        echo "✗ Tabla 'usuarios' NO existe<br>";
    }
    
    $stmt = $db->query("SHOW TABLES LIKE 'roles'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Tabla 'roles' existe<br>";
    } else {
        echo "✗ Tabla 'roles' NO existe<br>";
    }
} catch (PDOException $e) {
    echo "✗ Error al verificar tablas: " . $e->getMessage() . "<br>";
}

// Paso 5: Probar consulta
echo "<h3>5. Probando consulta de usuarios</h3>";
try {
    $stmt = $db->prepare("
        SELECT u.*, r.nombre as rol_nombre 
        FROM usuarios u 
        INNER JOIN roles r ON u.rol_id = r.id 
        WHERE u.estado <> 'inactivo'
        ORDER BY u.id DESC
        LIMIT 5
    ");
    $stmt->execute();
    $usuarios = $stmt->fetchAll();
    echo "✓ Consulta exitosa. Usuarios encontrados: " . count($usuarios) . "<br>";
    if (count($usuarios) > 0) {
        echo "<pre>";
        print_r($usuarios);
        echo "</pre>";
    }
} catch (PDOException $e) {
    echo "✗ Error en consulta: " . $e->getMessage() . "<br>";
    echo "Código SQL: " . $e->getCode() . "<br>";
}

// Paso 6: Verificar autenticación
echo "<h3>6. Verificando sistema de autenticación</h3>";
try {
    require_once __DIR__ . '/../config/auth.php';
    echo "✓ Sistema de autenticación cargado<br>";
} catch (Exception $e) {
    echo "✗ Error al cargar autenticación: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>Resumen</h3>";
echo "Si todos los pasos muestran ✓, el problema puede estar en la sesión o en el flujo del código.<br>";
echo "Revisa los errores marcados con ✗ para identificar el problema.<br>";
?>

