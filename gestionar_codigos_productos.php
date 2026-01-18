<?php
/**
 * Herramienta web para gestionar códigos de productos
 */

require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/database.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: index.php');
    exit;
}

$db = getDB();
$mensaje = '';
$tipoMensaje = '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    try {
        if ($accion === 'normalizar') {
            $db->beginTransaction();
            
            // Obtener todos los productos
            $stmt = $db->query("SELECT id, nombre FROM productos ORDER BY id ASC");
            $productos = $stmt->fetchAll();
            
            $contador = 1;
            $actualizados = 0;
            
            foreach ($productos as $prod) {
                $codigoNuevo = str_pad($contador, 4, '0', STR_PAD_LEFT);
                
                if ($prod['nombre'] !== $codigoNuevo) {
                    $stmt = $db->prepare("UPDATE productos SET nombre = ? WHERE id = ?");
                    $stmt->execute([$codigoNuevo, $prod['id']]);
                    $actualizados++;
                }
                
                $contador++;
            }
            
            $db->commit();
            $mensaje = "Se normalizaron $actualizados productos correctamente. Próximo código: " . str_pad($contador, 4, '0', STR_PAD_LEFT);
            $tipoMensaje = 'success';
            
        } elseif ($accion === 'eliminar_no_numericos') {
            $db->beginTransaction();
            
            $stmt = $db->prepare("UPDATE productos SET estado = 'inactivo' WHERE nombre NOT REGEXP '^[0-9]{4}$'");
            $stmt->execute();
            $eliminados = $stmt->rowCount();
            
            $db->commit();
            $mensaje = "Se desactivaron $eliminados productos con códigos no numéricos.";
            $tipoMensaje = 'success';
        }
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $mensaje = "Error: " . $e->getMessage();
        $tipoMensaje = 'error';
    }
}

// Obtener estadísticas
$stmt = $db->query("SELECT COUNT(*) as total FROM productos WHERE estado = 'activo'");
$totalActivos = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM productos WHERE nombre REGEXP '^[0-9]{4}$' AND estado = 'activo'");
$totalNumericos = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM productos WHERE nombre NOT REGEXP '^[0-9]{4}$' AND estado = 'activo'");
$totalNoNumericos = $stmt->fetch()['total'];

// Obtener último código
$stmt = $db->query("SELECT nombre FROM productos WHERE nombre REGEXP '^[0-9]{4}$' ORDER BY CAST(nombre AS UNSIGNED) DESC LIMIT 1");
$ultimoCodigo = $stmt->fetch();
$proximoCodigo = $ultimoCodigo ? str_pad(intval($ultimoCodigo['nombre']) + 1, 4, '0', STR_PAD_LEFT) : '0001';

// Listar productos
$stmt = $db->query("SELECT id, nombre, estado FROM productos ORDER BY id ASC");
$productos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Códigos de Productos</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body { padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; background: white; padding: 30px; border-radius: 8px; }
        .stat-card { background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 20px; }
        .codigo-numerico { color: #28a745; font-weight: bold; }
        .codigo-texto { color: #dc3545; font-weight: bold; }
        .table-container { max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Códigos de Productos</h1>
        <p class="text-muted">Herramienta para normalizar y verificar códigos de productos</p>
        
        <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipoMensaje === 'success' ? 'success' : 'danger'; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Total Activos</h6>
                    <h2><?php echo $totalActivos; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Códigos Numéricos</h6>
                    <h2 class="codigo-numerico"><?php echo $totalNumericos; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Códigos No Numéricos</h6>
                    <h2 class="codigo-texto"><?php echo $totalNoNumericos; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Próximo Código</h6>
                    <h2><?php echo $proximoCodigo; ?></h2>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Normalizar Códigos</h5>
                        <p>Convierte todos los productos a formato 0001, 0002, 0003...</p>
                        <form method="POST" onsubmit="return confirm('¿Estás seguro? Esto cambiará todos los códigos de productos.');">
                            <input type="hidden" name="accion" value="normalizar">
                            <button type="submit" class="btn btn-primary">Normalizar Todos</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Desactivar No Numéricos</h5>
                        <p>Desactiva productos con nombres descriptivos (no códigos numéricos).</p>
                        <form method="POST" onsubmit="return confirm('¿Estás seguro? Esto desactivará productos con nombres no numéricos.');">
                            <input type="hidden" name="accion" value="eliminar_no_numericos">
                            <button type="submit" class="btn btn-warning">Desactivar No Numéricos</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <h4 class="mt-4">Lista de Productos</h4>
        <div class="table-container">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código/Nombre</th>
                        <th>Estado</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                    <tr>
                        <td><?php echo $prod['id']; ?></td>
                        <td>
                            <?php 
                            $esNumerico = preg_match('/^[0-9]{4}$/', $prod['nombre']);
                            $clase = $esNumerico ? 'codigo-numerico' : 'codigo-texto';
                            echo "<span class='$clase'>" . htmlspecialchars($prod['nombre']) . "</span>";
                            ?>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $prod['estado'] === 'activo' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($prod['estado']); ?>
                            </span>
                        </td>
                        <td><?php echo $esNumerico ? 'Numérico ✓' : 'Texto'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            <a href="productos/index.php" class="btn btn-secondary">← Volver a Productos</a>
        </div>
    </div>
</body>
</html>
