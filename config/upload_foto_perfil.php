<?php
/**
 * Endpoint para subir foto de perfil del usuario
 * Sistema de Gestión de Reciclaje
 */

session_start();
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/auth.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar autenticación
$auth = new Auth();
if (!$auth->isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$usuarioId = $_SESSION['usuario_id'] ?? null;

if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuario no identificado']);
    exit;
}

// Verificar que se haya enviado un archivo
if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No se recibió ningún archivo o hubo un error en la subida']);
    exit;
}

$file = $_FILES['foto_perfil'];

// Validar tipo de archivo
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$fileType = mime_content_type($file['tmp_name']);

if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG o GIF']);
    exit;
}

// Validar tamaño (máximo 2MB)
$maxSize = 2 * 1024 * 1024; // 2MB
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande. Tamaño máximo: 2MB']);
    exit;
}

try {
    $db = getDB();
    
    // Obtener la foto anterior si existe
    $stmt = $db->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
    $stmt->execute([$usuarioId]);
    $usuario = $stmt->fetch();
    $fotoAnterior = $usuario['foto_perfil'] ?? null;
    
    // Crear directorio si no existe
    $uploadDir = __DIR__ . '/../assets/img/perfiles/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generar nombre único para el archivo
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nombreArchivo = 'perfil_' . $usuarioId . '_' . time() . '.' . $extension;
    $rutaCompleta = $uploadDir . $nombreArchivo;
    
    // Mover el archivo
    if (!move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
        throw new Exception('Error al guardar el archivo');
    }
    
    // Ruta relativa para guardar en la base de datos
    $rutaRelativa = 'assets/img/perfiles/' . $nombreArchivo;
    
    // Actualizar en la base de datos
    $stmt = $db->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
    $stmt->execute([$rutaRelativa, $usuarioId]);
    
    // Eliminar foto anterior si existe
    if ($fotoAnterior && file_exists(__DIR__ . '/../' . $fotoAnterior)) {
        @unlink(__DIR__ . '/../' . $fotoAnterior);
    }
    
    // Actualizar la sesión
    $_SESSION['usuario_foto_perfil'] = $rutaRelativa;
    
    echo json_encode([
        'success' => true,
        'message' => 'Foto de perfil actualizada exitosamente',
        'foto_perfil' => $rutaRelativa
    ]);
    
} catch (Exception $e) {
    error_log("Error al subir foto de perfil: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al procesar la foto: ' . $e->getMessage()]);
}

