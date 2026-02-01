<?php
/**
 * API para gestión de compras
 * Sistema de Gestión de Reciclaje
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: application/json; charset=utf-8');
ob_start();

try {
    require_once __DIR__ . '/../config/auth.php';

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
                $filtro_fecha = $_GET['fecha'] ?? '';
                
                $sql = "
                    SELECT c.*, p.nombre as proveedor_nombre, s.nombre as sucursal_nombre 
                    FROM compras c 
                    INNER JOIN proveedores p ON c.proveedor_id = p.id 
                    INNER JOIN sucursales s ON c.sucursal_id = s.id 
                    WHERE 1=1
                ";
                $params = [];
                
                if ($sucursal_id) {
                    $sql .= " AND c.sucursal_id = ?";
                    $params[] = $sucursal_id;
                }

                if ($filtro_estado === 'activos') {
                    $sql .= " AND c.estado <> 'cancelada'";
                } elseif ($filtro_estado !== 'todos') {
                    $sql .= " AND c.estado = ?";
                    $params[] = $filtro_estado;
                }

                if ($filtro_fecha) {
                    $sql .= " AND c.fecha_compra = ?";
                    $params[] = $filtro_fecha;
                }
                
                $sql .= " ORDER BY c.fecha_compra DESC, c.id DESC";

                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $compras = $stmt->fetchAll();
                
                // Obtener detalles de cada compra
                foreach ($compras as &$compra) {
                    $stmt = $db->prepare("
                        SELECT cd.*, 
                               p.nombre as producto_nombre,
                               m.nombre as material_nombre,
                               c.nombre as categoria_nombre,
                               u.nombre as unidad_nombre,
                               u.simbolo as unidad_simbolo,
                               pr.precio_unitario
                        FROM compras_detalle cd
                        INNER JOIN productos p ON cd.producto_id = p.id
                        INNER JOIN materiales m ON p.material_id = m.id
                        LEFT JOIN categorias c ON m.categoria_id = c.id
                        INNER JOIN unidades u ON p.unidad_id = u.id
                        LEFT JOIN precios pr ON cd.precio_id = pr.id
                        WHERE cd.compra_id = ?
                    ");
                    $stmt->execute([$compra['id']]);
                    $compra['detalles'] = $stmt->fetchAll();
                }
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $compras], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'productos') {
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
                
                // Obtener productos activos con precios de compra filtrados por sucursal
                if ($sucursal_usuario) {
                    $stmt = $db->prepare("
                        SELECT p.id, 
                               p.nombre, 
                               m.nombre as material_nombre,
                               c.nombre as categoria_nombre,
                               u.simbolo as unidad,
                               MAX(pr.id) as precio_id,
                               MAX(pr.precio_unitario) as precio_unitario
                        FROM productos p 
                        INNER JOIN materiales m ON p.material_id = m.id
                        LEFT JOIN categorias c ON m.categoria_id = c.id
                        INNER JOIN unidades u ON p.unidad_id = u.id
                        LEFT JOIN precios pr ON p.id = pr.producto_id AND pr.tipo_precio = 'compra' AND pr.estado = 'activo'
                        INNER JOIN inventarios i ON p.id = i.producto_id AND i.sucursal_id = ?
                        WHERE p.estado = 'activo'
                        GROUP BY p.id, p.nombre, m.nombre, c.nombre, u.simbolo
                        ORDER BY p.nombre ASC
                    ");
                    $stmt->execute([$sucursal_usuario]);
                } else {
                    // Si no tiene sucursal, mostrar todos
                    $stmt = $db->query("
                        SELECT p.id, 
                               p.nombre, 
                               m.nombre as material_nombre,
                               c.nombre as categoria_nombre,
                               u.simbolo as unidad,
                               pr.id as precio_id,
                               pr.precio_unitario
                        FROM productos p 
                        INNER JOIN materiales m ON p.material_id = m.id
                        LEFT JOIN categorias c ON m.categoria_id = c.id
                        INNER JOIN unidades u ON p.unidad_id = u.id
                        LEFT JOIN precios pr ON p.id = pr.producto_id AND pr.tipo_precio = 'compra' AND pr.estado = 'activo'
                        WHERE p.estado = 'activo'
                        ORDER BY p.nombre ASC
                    ");
                }
                
                $productos = $stmt->fetchAll();
                
                ob_end_clean();
                echo json_encode(['success' => true, 'data' => $productos], JSON_UNESCAPED_UNICODE);
            } elseif ($action === 'obtener') {
                $id = $_GET['id'] ?? 0;
                
                // Obtener compra
                $stmt = $db->prepare("
                    SELECT c.*, p.nombre as proveedor_nombre, s.nombre as sucursal_nombre 
                    FROM compras c 
                    INNER JOIN proveedores p ON c.proveedor_id = p.id 
                    INNER JOIN sucursales s ON c.sucursal_id = s.id 
                    WHERE c.id = ?
                ");
                $stmt->execute([$id]);
                $compra = $stmt->fetch();
                
                if ($compra) {
                    // Obtener detalles con información de productos
                    $stmt = $db->prepare("
                        SELECT cd.*, 
                               p.nombre as producto_nombre,
                               m.nombre as material_nombre,
                               c.nombre as categoria_nombre,
                               u.nombre as unidad_nombre,
                               u.simbolo as unidad_simbolo,
                               pr.precio_unitario
                        FROM compras_detalle cd
                        INNER JOIN productos p ON cd.producto_id = p.id
                        INNER JOIN materiales m ON p.material_id = m.id
                        LEFT JOIN categorias c ON m.categoria_id = c.id
                        INNER JOIN unidades u ON p.unidad_id = u.id
                        LEFT JOIN precios pr ON cd.precio_id = pr.id
                        WHERE cd.compra_id = ?
                    ");
                    $stmt->execute([$id]);
                    $compra['detalles'] = $stmt->fetchAll();
                }
                
                ob_end_clean();
                if ($compra) {
                    echo json_encode(['success' => true, 'data' => $compra]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Compra no encontrada']);
                }
            } elseif ($action === 'siguiente_numero_factura') {
                // Obtener el siguiente número de factura disponible
                // Consulta directa a la base de datos para obtener el último número
                $siguienteNumero = 1;
                
                try {
                    // Obtener todos los números de factura que sean numéricos
                    $stmt = $db->query("
                        SELECT numero_factura 
                        FROM compras 
                        WHERE numero_factura IS NOT NULL 
                          AND numero_factura <> ''
                          AND estado <> 'cancelada'
                        ORDER BY id DESC
                    ");
                    $todos = $stmt->fetchAll();
                    
                    // Buscar el número más alto
                    foreach ($todos as $row) {
                        if (!empty($row['numero_factura'])) {
                            // Limpiar el número (quitar espacios, guiones, etc.) y extraer solo dígitos
                            $numeroLimpio = preg_replace('/[^0-9]/', '', $row['numero_factura']);
                            if (!empty($numeroLimpio) && is_numeric($numeroLimpio)) {
                                $num = intval($numeroLimpio);
                                if ($num >= $siguienteNumero) {
                                    $siguienteNumero = $num + 1;
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Si hay error, usar 1 como valor por defecto
                    error_log("Error al obtener siguiente número de factura: " . $e->getMessage());
                    $siguienteNumero = 1;
                }
                
                // Formatear con ceros a la izquierda (5 dígitos: 00001, 00002, etc.)
                $numeroFormateado = str_pad($siguienteNumero, 5, '0', STR_PAD_LEFT);
                
                ob_end_clean();
                echo json_encode([
                    'success' => true, 
                    'numero_factura' => $numeroFormateado
                ], JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'POST':
            if ($action === 'crear') {
                $db->beginTransaction();
                
                try {
                    $proveedor_id = intval($_POST['proveedor_id'] ?? 0);
                    $sucursal_id = intval($_POST['sucursal_id'] ?? 0);
                    $fecha_compra = $_POST['fecha_compra'] ?? date('Y-m-d');
                    $numero_factura = trim($_POST['numero_factura'] ?? '');
                    $tipo_comprobante = $_POST['tipo_comprobante'] ?? 'factura';
                    $subtotal = floatval($_POST['subtotal'] ?? 0);
                    $iva = floatval($_POST['iva'] ?? 0);
                    $descuento = floatval($_POST['descuento'] ?? 0);
                    $total = floatval($_POST['total'] ?? 0);
                    $estado = $_POST['estado'] ?? 'pendiente';
                    $notas = trim($_POST['notas'] ?? '');
                    
                    if ($proveedor_id <= 0 || $sucursal_id <= 0) {
                        throw new Exception('Proveedor y sucursal son obligatorios');
                    }

                    // VALIDACIÓN: Verificar si hay saldo suficiente en la sucursal
                    if ($estado === 'completada') {
                        $stmtSaldo = $db->prepare("SELECT saldo, nombre FROM sucursales WHERE id = ?");
                        $stmtSaldo->execute([$sucursal_id]);
                        $sucursal = $stmtSaldo->fetch();
                        
                        if (!$sucursal) {
                            throw new Exception('Sucursal no encontrada');
                        }
                        
                        if ($sucursal['saldo'] < $total) {
                            throw new Exception("Saldo insuficiente en la sucursal '{$sucursal['nombre']}'. Saldo disponible: $" . number_format($sucursal['saldo'], 2) . ", Total compra: $" . number_format($total, 2));
                        }
                    }
                    
                    // Insertar compra
                    $stmt = $db->prepare("
                        INSERT INTO compras 
                        (numero_factura, proveedor_id, sucursal_id, fecha_compra, tipo_comprobante, subtotal, iva, descuento, total, estado, notas, creado_por) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->execute([
                        $numero_factura ?: null,
                        $proveedor_id,
                        $sucursal_id,
                        $fecha_compra,
                        $tipo_comprobante,
                        $subtotal,
                        $iva,
                        $descuento,
                        $total,
                        $estado,
                        $notas ?: null,
                        $usuario_id
                    ]);
                    
                    $compra_id = $db->lastInsertId();
                    
                    // Insertar detalles
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
                            $producto_id = intval($detalle['producto_id'] ?? 0);
                            $precio_id = !empty($detalle['precio_id']) ? intval($detalle['precio_id']) : null;
                            $cantidad = floatval($detalle['cantidad'] ?? 0);
                            $subtotal_detalle = floatval($detalle['subtotal'] ?? 0);
                            
                            if ($producto_id <= 0) {
                                throw new Exception('Producto es obligatorio en los detalles');
                            }
                            
                            // Obtener precio de compra si no se proporciona precio_id
                            if (!$precio_id) {
                                $stmt = $db->prepare("
                                    SELECT id, precio_unitario FROM precios 
                                    WHERE producto_id = ? AND tipo_precio = 'compra' AND estado = 'activo' 
                                    LIMIT 1
                                ");
                                $stmt->execute([$producto_id]);
                                $precio = $stmt->fetch();
                                if ($precio) {
                                    $precio_id = $precio['id'];
                                }
                            }
                            
                            $stmt = $db->prepare("
                                INSERT INTO compras_detalle 
                                (compra_id, producto_id, precio_id, cantidad, subtotal) 
                                VALUES (?, ?, ?, ?, ?)
                            ");
                            
                            $stmt->execute([
                                $compra_id,
                                $producto_id,
                                $precio_id,
                                $cantidad,
                                $subtotal_detalle
                            ]);
                            
                            // Si la compra está completada, actualizar inventario (el trigger lo hará automáticamente)
                            // Pero por si acaso, también lo hacemos aquí
                            if ($estado === 'completada') {
                                // Buscar o crear inventario para este producto en esta sucursal
                                $stmt = $db->prepare("
                                    SELECT id, cantidad FROM inventarios 
                                    WHERE producto_id = ? AND sucursal_id = ? AND estado <> 'inactivo'
                                ");
                                $stmt->execute([$producto_id, $sucursal_id]);
                                $inventario = $stmt->fetch();
                                
                                if ($inventario) {
                                    $stmt = $db->prepare("UPDATE inventarios SET cantidad = cantidad + ? WHERE id = ?");
                                    $stmt->execute([$cantidad, $inventario['id']]);
                                } else {
                                    $stmt = $db->prepare("
                                        INSERT INTO inventarios (sucursal_id, producto_id, cantidad, estado) 
                                        VALUES (?, ?, ?, 'disponible')
                                    ");
                                    $stmt->execute([$sucursal_id, $producto_id, $cantidad]);
                                }
                            }
                        }
                    }

                    // NUEVO: Actualizar saldo de la sucursal si la compra está completada (Resta dinero)
                    if ($estado === 'completada') {
                        $stmtSaldo = $db->prepare("UPDATE sucursales SET saldo = saldo - ? WHERE id = ?");
                        $stmtSaldo->execute([$total, $sucursal_id]);
                    }
                    
                    $db->commit();
                    
                    ob_end_clean();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Compra creada exitosamente',
                        'id' => $compra_id
                    ]);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            } elseif ($action === 'actualizar') {
                $id = intval($_POST['id'] ?? 0);
                $proveedor_id = intval($_POST['proveedor_id'] ?? 0);
                $sucursal_id = intval($_POST['sucursal_id'] ?? 0);
                $fecha_compra = $_POST['fecha_compra'] ?? date('Y-m-d');
                $numero_factura = trim($_POST['numero_factura'] ?? '');
                $tipo_comprobante = $_POST['tipo_comprobante'] ?? 'factura';
                $subtotal = floatval($_POST['subtotal'] ?? 0);
                $iva = floatval($_POST['iva'] ?? 0);
                $descuento = floatval($_POST['descuento'] ?? 0);
                $total = floatval($_POST['total'] ?? 0);
                $estado = $_POST['estado'] ?? 'pendiente';
                $notas = trim($_POST['notas'] ?? '');
                
                $stmt = $db->prepare("
                    UPDATE compras 
                    SET numero_factura = ?, proveedor_id = ?, sucursal_id = ?, fecha_compra = ?, tipo_comprobante = ?, 
                        subtotal = ?, iva = ?, descuento = ?, total = ?, estado = ?, notas = ?
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $numero_factura ?: null,
                    $proveedor_id,
                    $sucursal_id,
                    $fecha_compra,
                    $tipo_comprobante,
                    $subtotal,
                    $iva,
                    $descuento,
                    $total,
                    $estado,
                    $notas ?: null,
                    $id
                ]);
                
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Compra actualizada exitosamente']);
            } elseif ($action === 'completar') {
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de compra inválido');
                }
                
                $db->beginTransaction();
                try {
                    // Obtener la compra actual
                    $stmt = $db->prepare("SELECT * FROM compras WHERE id = ?");
                    $stmt->execute([$id]);
                    $compra = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$compra) {
                        throw new Exception('Compra no encontrada');
                    }
                    
                    if ($compra['estado'] === 'completada') {
                        throw new Exception('La compra ya está completada');
                    }
                    
                    if ($compra['estado'] === 'cancelada') {
                        throw new Exception('No se puede completar una compra cancelada');
                    }
                    
                    // Verificar saldo suficiente
                    $stmt = $db->prepare("SELECT saldo FROM sucursales WHERE id = ?");
                    $stmt->execute([$compra['sucursal_id']]);
                    $sucursal = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($sucursal && floatval($sucursal['saldo']) < floatval($compra['total'])) {
                        throw new Exception('La sucursal no tiene saldo suficiente para completar esta compra');
                    }
                    
                    // Actualizar el estado a completada
                    $stmt = $db->prepare("UPDATE compras SET estado = 'completada' WHERE id = ?");
                    $stmt->execute([$id]);
                    
                    // Descontar del saldo de la sucursal
                    $stmt = $db->prepare("UPDATE sucursales SET saldo = saldo - ? WHERE id = ?");
                    $stmt->execute([$compra['total'], $compra['sucursal_id']]);
                    
                    // Actualizar inventario: sumar cantidades
                    $stmt = $db->prepare("SELECT * FROM compras_detalle WHERE compra_id = ?");
                    $stmt->execute([$id]);
                    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($detalles as $detalle) {
                        // Verificar si existe en inventarios
                        $stmt = $db->prepare("
                            SELECT id, cantidad FROM inventarios 
                            WHERE producto_id = ? AND sucursal_id = ?
                        ");
                        $stmt->execute([$detalle['producto_id'], $compra['sucursal_id']]);
                        $inventario = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($inventario) {
                            // Actualizar cantidad existente
                            $stmt = $db->prepare("
                                UPDATE inventarios 
                                SET cantidad = cantidad + ?, 
                                    fecha_actualizacion = NOW() 
                                WHERE id = ?
                            ");
                            $stmt->execute([$detalle['cantidad'], $inventario['id']]);
                        } else {
                            // Crear nuevo registro en inventario
                            $stmt = $db->prepare("
                                INSERT INTO inventarios 
                                (producto_id, sucursal_id, cantidad, fecha_actualizacion) 
                                VALUES (?, ?, ?, NOW())
                            ");
                            $stmt->execute([
                                $detalle['producto_id'],
                                $compra['sucursal_id'],
                                $detalle['cantidad']
                            ]);
                        }
                    }
                    
                    $db->commit();
                    
                    ob_end_clean();
                    echo json_encode(['success' => true, 'message' => 'Compra completada exitosamente']);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            } elseif ($action === 'eliminar') {
                $id = intval($_POST['id'] ?? 0);
                
                if ($id <= 0) {
                    throw new Exception('ID de compra inválido');
                }
                
                $db->beginTransaction();
                try {
                    $stmt = $db->prepare("SELECT total, sucursal_id, estado FROM compras WHERE id = ?");
                    $stmt->execute([$id]);
                    $compra = $stmt->fetch();
                    
                    if (!$compra) {
                        throw new Exception('Compra no encontrada');
                    }
                    
                    if ($compra['estado'] === 'cancelada') {
                        throw new Exception('La compra ya está cancelada');
                    }

                    // NUEVO: Si la compra estaba completada, devolver el dinero a la caja
                    if ($compra['estado'] === 'completada') {
                        $stmtSaldo = $db->prepare("UPDATE sucursales SET saldo = saldo + ? WHERE id = ?");
                        $stmtSaldo->execute([$compra['total'], $compra['sucursal_id']]);
                    }
                    
                    if ($compra['estado'] === 'completada') {
                        $stmt = $db->prepare("SELECT producto_id, cantidad FROM compras_detalle WHERE compra_id = ?");
                        $stmt->execute([$id]);
                        $detalles = $stmt->fetchAll();
                        
                        foreach ($detalles as $detalle) {
                            // Al cancelar una compra completada, restamos el stock del producto en esa sucursal
                            $stmt = $db->prepare("UPDATE inventarios SET cantidad = GREATEST(cantidad - ?, 0) WHERE producto_id = ? AND sucursal_id = ?");
                            $stmt->execute([$detalle['cantidad'], $detalle['producto_id'], $compra['sucursal_id']]);
                        }
                    }
                    
                    $stmt = $db->prepare("UPDATE compras SET estado = 'cancelada', fecha_actualizacion = NOW() WHERE id = ?");
                    $stmt->execute([$id]);
                    
                    $db->commit();
                    
                    ob_end_clean();
                    echo json_encode(['success' => true, 'message' => 'Compra cancelada exitosamente']);
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
    $errorInfo = ErrorHandler::handleDatabaseError($e, 'compras/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage(), 'code' => $e->getCode()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 500, $debug);
} catch (Exception $e) {
    ob_end_clean();
    $errorInfo = ErrorHandler::handleException($e, 'compras/api.php');
    ErrorHandler::logError($e->getMessage(), ['exception' => $e->getMessage()]);
    $debug = defined('APP_DEBUG') && APP_DEBUG;
    echo ErrorHandler::jsonResponse($errorInfo, 400, $debug);
}
?>

