<?php
/**
 * API para gestión de ventas
 * Sistema de Gestión de Reciclaje
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: application/json; charset=utf-8');
ob_start();

try {
    require_once __DIR__ . '/../config/auth.php';
    require_once __DIR__ . '/../config/ErrorHandler.php';

    $auth = new Auth();
    if (!$auth->isAuthenticated()) {
        ob_end_clean();
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit;
    }

    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    if (empty($action)) {
        throw new Exception('Acción no especificada');
    }

    $db = getDB();
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    
    switch ($method) {
        case 'GET':
            if ($action === 'listar') {
                // Obtener sucursal del usuario logueado
                $sucursal_usuario = null;
                
                // Buscar si es responsable de una sucursal
                $stmt = $db->prepare("SELECT id FROM sucursales WHERE responsable_id = ? AND estado = 'activa' LIMIT 1");
                $stmt->execute([$usuario_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result) {
                    $sucursal_usuario = $result['id'];
                } else {
                    // Buscar en perfil de usuario
                    $stmt = $db->prepare("SELECT sucursal_id FROM usuarios WHERE id = ?");
                    $stmt->execute([$usuario_id]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($result && $result['sucursal_id']) {
                        $sucursal_usuario = $result['sucursal_id'];
                    }
                }
                
                $sucursal_id = $_GET['sucursal_id'] ?? $sucursal_usuario;
                $filtro_estado = $_GET['estado'] ?? 'activos'; // Por defecto solo activos
                
                // Verificar que la tabla ventas existe
                try {
                    $db->query("SELECT 1 FROM ventas LIMIT 1");
                } catch (PDOException $e) {
                    if ($e->getCode() == '42S02') { // Table doesn't exist
                        ob_end_clean();
                        echo json_encode([
                            'success' => true,
                            'data' => [],
                            'message' => 'La tabla de ventas no existe aún. No hay ventas registradas.'
                        ], JSON_UNESCAPED_UNICODE);
                        exit;
                    }
                    throw $e;
                }
                
                // Verificar si la tabla sucursales existe para hacer JOIN
                $tablaSucursalesExiste = false;
                try {
                    $db->query("SELECT 1 FROM sucursales LIMIT 1");
                    $tablaSucursalesExiste = true;
                } catch (PDOException $e) {
                    if ($e->getCode() != '42S02') {
                        throw $e;
                    }
                }
                
                // Construir la consulta según la estructura real de la tabla
                // La tabla ventas tiene cliente_nombre directamente, no cliente_id
                if ($tablaSucursalesExiste) {
                    $sql = "
                        SELECT v.*, 
                               COALESCE(v.cliente_nombre, 'Sin cliente') as cliente_nombre,
                               COALESCE(s.nombre, 'Sucursal eliminada') as sucursal_nombre 
                        FROM ventas v 
                        LEFT JOIN sucursales s ON v.sucursal_id = s.id 
                        WHERE 1=1
                    ";
                } else {
                    // Si la tabla sucursales no existe, solo obtener ventas sin JOIN
                    $sql = "
                        SELECT v.*, 
                               COALESCE(v.cliente_nombre, 'Sin cliente') as cliente_nombre,
                               CAST(v.sucursal_id AS CHAR) as sucursal_nombre 
                        FROM ventas v 
                        WHERE 1=1
                    ";
                }
                
                $params = [];
                
                if ($sucursal_id) {
                    $sql .= " AND v.sucursal_id = ?";
                    $params[] = $sucursal_id;
                }

                if ($filtro_estado === 'activos') {
                    $sql .= " AND v.estado <> 'cancelada'";
                } elseif ($filtro_estado !== 'todos') {
                    $sql .= " AND v.estado = ?";
                    $params[] = $filtro_estado;
                }
                
                $sql .= " ORDER BY v.fecha_venta DESC, v.id DESC";
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $ventas = $stmt->fetchAll();
                
                // Cargar detalles para cada venta
                foreach ($ventas as &$venta) {
                    try {
                        $stmtDetalles = $db->prepare("
                            SELECT vd.*, 
                                   p.nombre as producto_nombre,
                                   u.simbolo as unidad_simbolo
                            FROM ventas_detalle vd
                            LEFT JOIN productos p ON vd.producto_id = p.id
                            LEFT JOIN unidades u ON p.unidad_id = u.id
                            WHERE vd.venta_id = ?
                        ");
                        $stmtDetalles->execute([$venta['id']]);
                        $detalles = $stmtDetalles->fetchAll();
                        
                        // Asegurar que cada detalle tenga precio_unitario
                        foreach ($detalles as &$det) {
                            if (!isset($det['precio_unitario']) || floatval($det['precio_unitario']) <= 0) {
                                if (floatval($det['cantidad']) > 0) {
                                    $det['precio_unitario'] = floatval($det['subtotal']) / floatval($det['cantidad']);
                                } else {
                                    $det['precio_unitario'] = 0;
                                }
                            }
                        }
                        $venta['detalles'] = $detalles;
                    } catch (PDOException $e) {
                        error_log("Error al cargar detalles de venta " . $venta['id'] . ": " . $e->getMessage());
                        $venta['detalles'] = [];
                    }
                }
                unset($venta);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $ventas], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'inventarios') {
                // Obtener inventarios disponibles por sucursal
                $sucursal_id = $_GET['sucursal_id'] ?? null;
                
                $sql = "
                    SELECT i.id as inventario_id,
                           SUM(i.cantidad) as cantidad,
                           p.id as producto_id,
                           p.nombre as producto_nombre,
                           m.nombre as material_nombre,
                           c.nombre as categoria_nombre,
                           u.simbolo as unidad,
                           MAX(pr.id) as precio_id,
                           MAX(pr.precio_unitario) as precio_unitario
                    FROM inventarios i
                    INNER JOIN productos p ON i.producto_id = p.id
                    INNER JOIN materiales m ON p.material_id = m.id
                    LEFT JOIN categorias c ON m.categoria_id = c.id
                    INNER JOIN unidades u ON p.unidad_id = u.id
                    LEFT JOIN precios pr ON p.id = pr.producto_id AND pr.tipo_precio = 'venta' AND pr.estado = 'activo'
                    WHERE i.estado <> 'inactivo' AND i.cantidad > 0
                ";
                $params = [];
                
                if ($sucursal_id) {
                    $sql .= " AND i.sucursal_id = ?";
                    $params[] = $sucursal_id;
                }
                
                $sql .= " GROUP BY p.id, p.nombre, m.nombre, c.nombre, u.simbolo ORDER BY p.nombre ASC";
                
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $inventarios = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $inventarios], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                
                // Obtener venta
                $stmt = $db->prepare("
                    SELECT v.*, 
                           COALESCE(v.cliente_nombre, 'Cliente General') as cliente_nombre, 
                           COALESCE(s.nombre, 'Sucursal eliminada') as sucursal_nombre 
                    FROM ventas v 
                    LEFT JOIN sucursales s ON v.sucursal_id = s.id 
                    WHERE v.id = ?
                ");
                $stmt->execute([$id]);
                $venta = $stmt->fetch();
                
                if ($venta) {
                    // Obtener detalles con información de productos
                    $stmt = $db->prepare("
                        SELECT vd.*, 
                               p.nombre as producto_nombre,
                               m.nombre as material_nombre,
                               c.nombre as categoria_nombre,
                               u.nombre as unidad_nombre,
                               u.simbolo as unidad_simbolo
                        FROM ventas_detalle vd
                        LEFT JOIN productos p ON vd.producto_id = p.id
                        LEFT JOIN materiales m ON p.material_id = m.id
                        LEFT JOIN categorias c ON m.categoria_id = c.id
                        LEFT JOIN unidades u ON p.unidad_id = u.id
                        WHERE vd.venta_id = ?
                    ");
                    $stmt->execute([$id]);
                    $detalles = $stmt->fetchAll();

                    // Asegurar que cada detalle tenga precio_unitario
                    foreach ($detalles as &$det) {
                        if (!isset($det['precio_unitario']) || floatval($det['precio_unitario']) <= 0) {
                            if (floatval($det['cantidad']) > 0) {
                                $det['precio_unitario'] = floatval($det['subtotal']) / floatval($det['cantidad']);
                            } else {
                                $det['precio_unitario'] = 0;
                            }
                        }
                    }
                    $venta['detalles'] = $detalles;
                }
                
                ob_end_clean();
                if ($venta) {
                    echo json_encode(['success' => true, 'data' => $venta]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Venta no encontrada']);
                }
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $db->beginTransaction();
                
                try {
                    // Obtener nombre del cliente desde el select
                    $cliente_nombre = trim($_POST['cliente_nombre'] ?? '');
                    // Si viene cliente_id, obtener el nombre del cliente
                    $cliente_id = intval($_POST['cliente_id'] ?? 0);
                    if ($cliente_id > 0 && empty($cliente_nombre)) {
                        // Intentar obtener el nombre del cliente si existe la tabla clientes
                        try {
                            $stmtCliente = $db->prepare("SELECT nombre FROM clientes WHERE id = ?");
                            $stmtCliente->execute([$cliente_id]);
                            $cliente = $stmtCliente->fetch();
                            if ($cliente) {
                                $cliente_nombre = $cliente['nombre'];
                            }
                        } catch (PDOException $e) {
                            // Si la tabla clientes no existe, usar el ID como nombre
                            if ($e->getCode() == '42S02') {
                                $cliente_nombre = 'Cliente #' . $cliente_id;
                            } else {
                                throw $e;
                            }
                        }
                    }
                    
                    $sucursal_id = intval($_POST['sucursal_id'] ?? 0);
                    $fecha_venta = $_POST['fecha_venta'] ?? date('Y-m-d');
                    $numero_factura = trim($_POST['numero_factura'] ?? '');
                    $tipo_comprobante = $_POST['tipo_comprobante'] ?? 'factura';
                    $subtotal = floatval($_POST['subtotal'] ?? 0);
                    $iva = floatval($_POST['iva'] ?? 0);
                    $descuento = floatval($_POST['descuento'] ?? 0);
                    $total = floatval($_POST['total'] ?? 0);
                    $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';
                    $estado = $_POST['estado'] ?? 'pendiente';
                    $notas = trim($_POST['notas'] ?? '');
                    
                    if (empty($cliente_nombre) || $sucursal_id <= 0) {
                        throw new Exception('Cliente y sucursal son obligatorios');
                    }
                    
                    // Insertar venta según la estructura real de la tabla
                    $stmt = $db->prepare("
                        INSERT INTO ventas 
                        (numero_factura, cliente_nombre, sucursal_id, fecha_venta, tipo_comprobante, subtotal, iva, descuento, total, metodo_pago, estado, notas, creado_por) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->execute([
                        $numero_factura ?: null,
                        $cliente_nombre,
                        $sucursal_id,
                        $fecha_venta,
                        $tipo_comprobante,
                        $subtotal,
                        $iva,
                        $descuento,
                        $total,
                        $metodo_pago,
                        $estado,
                        $notas ?: null,
                        $usuario_id
                    ]);
                    
                    $venta_id = $db->lastInsertId();
                    
                    // Insertar detalles y actualizar inventario
                    $detalles = [];
                    if (isset($_POST['detalles'])) {
                        if (is_string($_POST['detalles'])) {
                            $detalles = json_decode($_POST['detalles'], true);
                            if (!is_array($detalles)) {
                                $detalles = [];
                            }
                        } elseif (is_array($_POST['detalles'])) {
                            $detalles = $_POST['detalles'];
                        }
                    }
                    
                    if (!empty($detalles)) {
                        foreach ($detalles as $detalle) {
                            $inventario_id = intval($detalle['inventario_id'] ?? 0);
                            $producto_id = intval($detalle['producto_id'] ?? 0);
                            $precio_id = !empty($detalle['precio_id']) ? intval($detalle['precio_id']) : null;
                            $cantidad = floatval($detalle['cantidad'] ?? 0);
                            $precio_unitario_detalle = floatval($detalle['precio_unitario'] ?? 0);
                            $subtotal_detalle = floatval($detalle['subtotal'] ?? 0);
                            
                            if ($inventario_id <= 0 || $producto_id <= 0) {
                                throw new Exception('Inventario y producto son obligatorios para cada detalle');
                            }
                            
                            // Verificar stock disponible
                            $stmt = $db->prepare("SELECT cantidad, producto_id FROM inventarios WHERE id = ?");
                            $stmt->execute([$inventario_id]);
                            $inventario = $stmt->fetch();
                            
                            if (!$inventario) {
                                throw new Exception('Inventario no encontrado');
                            }
                            
                            // Verificar que el producto coincida con el inventario
                            if ($inventario['producto_id'] != $producto_id) {
                                throw new Exception('El producto no coincide con el inventario seleccionado');
                            }
                            
                            if ($inventario['cantidad'] < $cantidad && $estado === 'completada') {
                                throw new Exception('Stock insuficiente para este producto');
                            }
                            
                            // Obtener precio de venta si no se proporciona precio_id
                            if (!$precio_id) {
                                $stmt = $db->prepare("
                                    SELECT id, precio_unitario FROM precios 
                                    WHERE producto_id = ? AND tipo_precio = 'venta' AND estado = 'activo' 
                                    LIMIT 1
                                ");
                                $stmt->execute([$producto_id]);
                                $precio = $stmt->fetch();
                                if ($precio) {
                                    $precio_id = $precio['id'];
                                    if ($precio_unitario_detalle <= 0) {
                                        $precio_unitario_detalle = floatval($precio['precio_unitario']);
                                    }
                                }
                            }
                            
                            $stmt = $db->prepare("
                                INSERT INTO ventas_detalle 
                                (venta_id, inventario_id, producto_id, precio_id, cantidad, subtotal) 
                                VALUES (?, ?, ?, ?, ?, ?)
                            ");
                            
                            $stmt->execute([
                                $venta_id,
                                $inventario_id,
                                $producto_id,
                                $precio_id,
                                $cantidad,
                                $subtotal_detalle
                            ]);
                            
                            // Actualizar inventario si la venta está completada
                            if ($estado === 'completada') {
                                $stmt = $db->prepare("UPDATE inventarios SET cantidad = cantidad - ? WHERE id = ?");
                                $stmt->execute([$cantidad, $inventario_id]);
                            }
                        }
                    }

                    // NUEVO: Actualizar saldo de la sucursal si la venta está completada
                    if ($estado === 'completada') {
                        $stmtSaldo = $db->prepare("UPDATE sucursales SET saldo = saldo + ? WHERE id = ?");
                        $stmtSaldo->execute([$total, $sucursal_id]);
                    }
                    
                    $db->commit();
                    
                    ob_end_clean();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Venta creada exitosamente',
                        'id' => $venta_id
                    ]);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            } elseif ($action === 'actualizar') {
                $id = intval($_POST['id'] ?? 0);
                $cliente_id = intval($_POST['cliente_id'] ?? 0);
                $sucursal_id = intval($_POST['sucursal_id'] ?? 0);
                $fecha_venta = $_POST['fecha_venta'] ?? date('Y-m-d');
                $numero_factura = trim($_POST['numero_factura'] ?? '');
                $tipo_comprobante = $_POST['tipo_comprobante'] ?? 'factura';
                $subtotal = floatval($_POST['subtotal'] ?? 0);
                $iva = floatval($_POST['iva'] ?? 0);
                $descuento = floatval($_POST['descuento'] ?? 0);
                $total = floatval($_POST['total'] ?? 0);
                $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';
                $estado = $_POST['estado'] ?? 'pendiente';
                $notas = trim($_POST['notas'] ?? '');
                
                $stmt = $db->prepare("
                    UPDATE ventas 
                    SET numero_factura = ?, cliente_id = ?, sucursal_id = ?, fecha_venta = ?, tipo_comprobante = ?, 
                        subtotal = ?, iva = ?, descuento = ?, total = ?, metodo_pago = ?, estado = ?, notas = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $numero_factura ?: null,
                    $cliente_id,
                    $sucursal_id,
                    $fecha_venta,
                    $tipo_comprobante,
                    $subtotal,
                    $iva,
                    $descuento,
                    $total,
                    $metodo_pago,
                    $estado,
                    $notas ?: null,
                    $id
                ]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Venta actualizada exitosamente']);
            } elseif ($action === 'eliminar') {
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de venta inválido');
                }
                
                $db->beginTransaction();
                try {
                    $stmt = $db->prepare("SELECT total, sucursal_id, estado FROM ventas WHERE id = ?");
                    $stmt->execute([$id]);
                    $venta = $stmt->fetch();
                    
                    if (!$venta) {
                        throw new Exception('Venta no encontrada');
                    }
                    
                    if ($venta['estado'] === 'cancelada') {
                        throw new Exception('La venta ya está cancelada');
                    }

                    // NUEVO: Si la venta estaba completada, restar el dinero de la caja
                    if ($venta['estado'] === 'completada') {
                        $stmtSaldo = $db->prepare("UPDATE sucursales SET saldo = saldo - ? WHERE id = ?");
                        $stmtSaldo->execute([$venta['total'], $venta['sucursal_id']]);
                    }
                    
                    if (in_array($venta['estado'], ['pendiente', 'completada', 'devuelta'])) {
                        $stmt = $db->prepare("SELECT inventario_id, cantidad FROM ventas_detalle WHERE venta_id = ?");
                        $stmt->execute([$id]);
                        $detalles = $stmt->fetchAll();
                        
                        foreach ($detalles as $detalle) {
                            if (!empty($detalle['inventario_id'])) {
                                $stmt = $db->prepare("UPDATE inventarios SET cantidad = cantidad + ? WHERE id = ?");
                                $stmt->execute([$detalle['cantidad'], $detalle['inventario_id']]);
                            }
                        }
                    }
                    
                    $stmt = $db->prepare("UPDATE ventas SET estado = 'cancelada', fecha_actualizacion = NOW() WHERE id = ?");
                    $stmt->execute([$id]);
                    
                    $db->commit();
                    
                    ob_end_clean();
                    echo json_encode(['success' => true, 'message' => 'Venta cancelada exitosamente']);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }
            break;
            
        default:
            ob_end_clean();
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleDatabaseError($e, 'ventas/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage(), 'code' => $e->getCode()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 500, $debug);
} catch (Exception $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleException($e, 'ventas/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 400, $debug);
}
?>

