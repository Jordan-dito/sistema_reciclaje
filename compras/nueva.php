<?php
/**
 * Nueva Compra
 * Sistema de Gestión de Reciclaje
 */

// Verificar autenticación
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../index.php');
    exit;
}

// Obtener sucursal del usuario actual
$usuario_id = $_SESSION['usuario_id'];
$db = getDB();
$sucursal_usuario_id = '';

try {
    // 1. Buscar si es responsable de una sucursal
    $stmt = $db->prepare("SELECT id FROM sucursales WHERE responsable_id = ? AND estado = 'activa' LIMIT 1");
    $stmt->execute([$usuario_id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($res) {
        $sucursal_usuario_id = $res['id'];
    } else {
        // 2. Buscar si tiene sucursal asignada en su perfil (si existe la columna sucursal_id)
        // Usamos una consulta que no falle fatalmente si la columna no existe en algunos motores, 
        // pero en MySQL fallará si la columna no existe. Lo envolvemos en try/catch.
        $stmt = $db->prepare("SELECT s.id FROM sucursales s INNER JOIN usuarios u ON u.sucursal_id = s.id WHERE u.id = ? AND s.estado = 'activa' LIMIT 1");
        $stmt->execute([$usuario_id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res) {
            $sucursal_usuario_id = $res['id'];
        }
    }
} catch (Exception $e) {
    // Si falla (por ejemplo, columna sucursal_id no existe), ignoramos silenciosamente
    error_log("Nota: No se pudo verificar sucursal por usuario.sucursal_id: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Nueva Compra - Sistema de Reciclaje</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["../assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
  </head>
  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar" data-background-color="dark">
        <?php
          $basePath = '..';
          include __DIR__ . '/../includes/sidebar-logo.php';
        ?>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <?php
              $basePath = '..';
              $currentRoute = 'compras';
              include __DIR__ . '/../includes/sidebar.php';
            ?>
          </div>
        </div>
      </div>

      <div class="main-panel">
        <div class="main-header">
          <?php
            $basePath = '..';
            include __DIR__ . '/../includes/main-header-logo.php';
          ?>
          <?php
            $basePath = '..';
            include __DIR__ . '/../includes/user-header.php';
            include __DIR__ . '/../includes/modal-foto-perfil.php';
            include __DIR__ . '/../includes/modal-cambiar-password.php';
          ?>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Nueva Compra de Material Reciclable</h3>
                <h6 class="op-7 mb-2">Registra una nueva compra - Actualiza inventario y Kardex (PEPS)</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <a href="index.php" class="btn btn-secondary btn-round">
                  <i class="fa fa-arrow-left"></i> Volver
                </a>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-body">
                    <form id="formNuevaCompra">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Fecha de Compra <span class="text-danger">*</span></label>
                            <input type="date" id="fecha_compra" name="fecha_compra" class="form-control" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Sucursal <span class="text-danger">*</span></label>
                            <select id="sucursal_id" name="sucursal_id" class="form-control" required>
                              <option value="">Seleccione una sucursal</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Proveedor <span class="text-danger">*</span></label>
                            <select id="proveedor_id" name="proveedor_id" class="form-control" required>
                              <option value="">Seleccione un proveedor</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Número de Factura</label>
                            <input type="text" id="numero_factura" name="numero_factura" class="form-control" placeholder="Se generará automáticamente" readonly>
                            <small class="form-text text-muted">Se genera automáticamente al crear la compra</small>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Tipo de Comprobante</label>
                            <select id="tipo_comprobante" name="tipo_comprobante" class="form-control">
                              <option value="factura">Factura</option>
                             
                              <option value="nota_credito">Nota de Compra</option>
                          
                            </select>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Productos <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalBuscarProducto">
                              <i class="fa fa-plus"></i> Agregar Producto
                            </button>
                            <small class="form-text text-muted d-block mb-2"><i class="fa fa-info-circle"></i> Haga clic en "Agregar Producto" para seleccionar productos de la lista. Puede agregar múltiples productos.</small>
                            
                            <!-- Desglose de productos agregados - Cuadro dinámico -->
                            <div id="productosAgregados" style="display: none;">
                              <div class="card card-round mt-3" style="border: 2px solid #e0e0e0; background-color: #ffffff;">
                                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                  <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                      <i class="fa fa-shopping-cart text-primary"></i> Productos Agregados
                                      <span class="badge badge-primary ml-2" id="contadorProductos">0</span>
                                    </h5>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="limpiarTodosProductos()" id="btnLimpiarTodos" style="display: none;">
                                      <i class="fa fa-trash"></i> Limpiar Todo
                                    </button>
                                  </div>
                                </div>
                                <div class="card-body" style="padding: 15px;">
                                  <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm table-hover table-bordered" id="tablaProductosAgregados" style="margin-bottom: 0;">
                                      <thead class="thead-light" style="position: sticky; top: 0; background: white; z-index: 10;">
                                        <tr>
                                          <th style="width: 30px;">#</th>
                                          <th>Producto</th>
                                          <th>Material</th>
                                          <th style="width: 120px;">Cantidad</th>
                                          <th style="width: 130px;">Precio Unitario</th>
                                          <th style="width: 120px;">Subtotal</th>
                                          <th style="width: 60px;">Acción</th>
                                        </tr>
                                      </thead>
                                      <tbody id="tbodyProductosAgregados">
                                        <!-- Los productos se agregarán dinámicamente aquí -->
                                      </tbody>
                                      <tfoot class="table-info" style="position: sticky; bottom: 0; background: #d1ecf1; z-index: 10;">
                                        <tr>
                                          <th colspan="5" class="text-end"><strong>Subtotal Productos:</strong></th>
                                          <th id="subtotalProductos" style="font-size: 1.1em;">$0.00</th>
                                          <th></th>
                                        </tr>
                                      </tfoot>
                                    </table>
                                  </div>
                                  <div class="alert alert-warning mt-2 mb-0" id="alertaSinProductos" style="display: none;">
                                    <i class="fa fa-exclamation-triangle"></i> No hay productos agregados. Haga clic en "Agregar Producto" para comenzar.
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>IVA</label>
                            <input type="number" step="0.01" id="iva" name="iva" class="form-control" placeholder="0.00" value="0">
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>Descuento</label>
                            <input type="number" step="0.01" id="descuento" name="descuento" class="form-control" placeholder="0.00" value="0">
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>Estado</label>
                            <select id="estado" name="estado" class="form-control">
                              <option value="pendiente">Pendiente</option>
                              <option value="completada" selected>Completada</option>
                              <option value="cancelada">Cancelada</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="alert alert-info">
                            <div class="row">
                              <div class="col-md-6">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Subtotal Productos:</strong> <span id="subtotalProductosResumen">$0.00</span><br>
                                <strong>IVA:</strong> <span id="ivaResumen">$0.00</span><br>
                                <strong>Descuento:</strong> <span id="descuentoResumen">$0.00</span>
                              </div>
                              <div class="col-md-6 text-end">
                                <h5 class="mb-0"><strong>Total:</strong> <span id="totalCompra">$0.00</span></h5>
                              </div>
                            </div>
                            <hr class="my-2">
                            <small class="text-muted"><i class="fa fa-info-circle"></i> El inventario se actualizará automáticamente cuando el estado sea "Completada"</small>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Notas</label>
                            <textarea id="notas" name="notas" class="form-control" rows="2" placeholder="Notas adicionales sobre la compra"></textarea>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="card-footer">
                    <div class="d-flex justify-content-end">
                      <a href="index.php" class="btn btn-secondary me-2">Cancelar</a>
                      <button type="button" class="btn btn-primary" id="btnGuardarCompra">
                        <i class="fa fa-save"></i> Guardar Compra
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
      </div>
    </div>

    <!-- Modal Buscar Producto -->
    <div class="modal fade" id="modalBuscarProducto" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Buscar y Seleccionar Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Filtros de búsqueda -->
            <div class="row mb-3">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Buscar por nombre</label>
                  <input type="text" id="filtroNombre" class="form-control" placeholder="Nombre del producto...">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Filtrar por material</label>
                  <select id="filtroMaterial" class="form-control">
                    <option value="">Todos los materiales</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Filtrar por categoría</label>
                  <select id="filtroCategoria" class="form-control">
                    <option value="">Todas las categorías</option>
                  </select>
                </div>
              </div>
            </div>
            
            <!-- Tabla de productos -->
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
              <table class="table table-hover table-striped" id="tablaProductos">
                <thead class="thead-dark" style="position: sticky; top: 0; background: white; z-index: 10;">
                  <tr>
                    <th style="width: 50px;">Seleccionar</th>
                    <th>Nombre</th>
                    <th>Material</th>
                    <th>Categoría</th>
                    <th>Unidad</th>
                    <th>Precio Compra</th>
                  </tr>
                </thead>
                <tbody id="tbodyProductos">
                  <tr>
                    <td colspan="6" class="text-center">
                      <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="sinResultados" class="alert alert-info mt-3" style="display: none;">
              <i class="fa fa-info-circle"></i> No se encontraron productos con los filtros seleccionados.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/setting-demo.js"></script>
    <script>
      $(document).ready(function() {
        // Array para almacenar productos seleccionados
        var productosSeleccionados = [];
        var contadorProductos = 0;
        
        // Establecer fecha actual por defecto
        $('#fecha_compra').val(new Date().toISOString().split('T')[0]);
        
        // Cargar siguiente número de factura al iniciar
        cargarSiguienteNumeroFactura();
        
        // Función para cargar el siguiente número de factura desde la base de datos
        function cargarSiguienteNumeroFactura() {
          $('#numero_factura').val('Cargando...');
          
          $.ajax({
            url: 'api.php?action=siguiente_numero_factura',
            method: 'GET',
            dataType: 'json',
            cache: false,
            success: function(response) {
              if (response.success && response.numero_factura) {
                $('#numero_factura').val(response.numero_factura);
                console.log('Número de factura cargado desde BD:', response.numero_factura);
              } else {
                $('#numero_factura').val('00001');
                console.log('No se pudo obtener número de BD, usando 00001');
              }
            },
            error: function(xhr, status, error) {
              $('#numero_factura').val('00001');
              console.error('Error al cargar número de factura:', error);
            }
          });
        }
        
        // Cargar sucursales, proveedores y productos
        function cargarDatos() {
          // Cargar sucursales
          $.ajax({
            url: '../sucursales/api.php?action=activas',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var select = $('#sucursal_id');
                select.empty().append('<option value="">Seleccione una sucursal</option>');
                response.data.forEach(function(sucursal) {
                  select.append('<option value="' + sucursal.id + '">' + sucursal.nombre + '</option>');
                });
                
                // Preseleccionar sucursal del usuario si existe
                var sucursalUsuarioId = '<?php echo $sucursal_usuario_id; ?>';
                console.log("Depuración Sucursal - ID encontrado por PHP:", sucursalUsuarioId);
                
                if (sucursalUsuarioId) {
                  // Verificar si el ID existe en las opciones cargadas
                  var existe = false;
                  response.data.forEach(function(s) {
                    if (s.id == sucursalUsuarioId) existe = true;
                  });
                  console.log("Depuración Sucursal - ¿Existe en la lista API?:", existe);

                  if (existe) {
                    select.val(sucursalUsuarioId);
                    // Disparar evento change por si hay listeners
                    select.trigger('change');
                  } else {
                    console.warn("La sucursal del usuario (ID: " + sucursalUsuarioId + ") no está en la lista de sucursales activas devuelta por la API.");
                  }
                } else {
                   console.log("No se encontró ninguna sucursal asignada a este usuario (responsable_id no coincide).");
                }
              }
            }
          });
          
          // Cargar proveedores
          $.ajax({
            url: '../proveedores/api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var select = $('#proveedor_id');
                select.empty().append('<option value="">Seleccione un proveedor</option>');
                response.data.forEach(function(proveedor) {
                  if (proveedor.estado === 'activo') {
                    select.append('<option value="' + proveedor.id + '">' + proveedor.nombre + '</option>');
                  }
                });
              }
            }
          });
          
          // Cargar materiales y categorías para los filtros del modal
          cargarFiltrosProductos();
        }
        
        // Variables globales para productos
        var todosLosProductos = [];
        
        // Cargar filtros del modal de productos
        function cargarFiltrosProductos() {
          $.ajax({
            url: 'api.php?action=productos',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                todosLosProductos = response.data;
                
                // Extraer materiales y categorías únicos
                var materiales = {};
                var categorias = {};
                
                response.data.forEach(function(producto) {
                  if (producto.material_nombre && !materiales[producto.material_nombre]) {
                    materiales[producto.material_nombre] = producto.material_nombre;
                  }
                  if (producto.categoria_nombre && !categorias[producto.categoria_nombre]) {
                    categorias[producto.categoria_nombre] = producto.categoria_nombre;
                  }
                });
                
                // Llenar select de materiales
                var selectMaterial = $('#filtroMaterial');
                selectMaterial.empty().append('<option value="">Todos los materiales</option>');
                Object.keys(materiales).sort().forEach(function(material) {
                  selectMaterial.append('<option value="' + material + '">' + material + '</option>');
                });
                
                // Llenar select de categorías
                var selectCategoria = $('#filtroCategoria');
                selectCategoria.empty().append('<option value="">Todas las categorías</option>');
                Object.keys(categorias).sort().forEach(function(categoria) {
                  selectCategoria.append('<option value="' + categoria + '">' + categoria + '</option>');
                });
              }
            }
          });
        }
        
        // Cargar productos en el modal cuando se abre
        $('#modalBuscarProducto').on('show.bs.modal', function() {
          // Limpiar filtros
          $('#filtroNombre').val('');
          $('#filtroMaterial').val('');
          $('#filtroCategoria').val('');
          cargarProductosEnModal();
        });
        
        // Función para cargar y mostrar productos en el modal
        function cargarProductosEnModal() {
          var tbody = $('#tbodyProductos');
          tbody.html('<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></td></tr>');
          
          $.ajax({
            url: 'api.php?action=productos',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                todosLosProductos = response.data;
                filtrarYMostrarProductos();
              } else {
                tbody.html('<tr><td colspan="6" class="text-center text-danger">Error al cargar productos</td></tr>');
              }
            },
            error: function() {
              tbody.html('<tr><td colspan="6" class="text-center text-danger">Error al cargar productos</td></tr>');
            }
          });
        }
        
        // Función para filtrar y mostrar productos
        function filtrarYMostrarProductos() {
          var filtroNombre = $('#filtroNombre').val().toLowerCase();
          var filtroMaterial = $('#filtroMaterial').val();
          var filtroCategoria = $('#filtroCategoria').val();
          var tbody = $('#tbodyProductos');
          var sinResultados = $('#sinResultados');
          
          var productosFiltrados = todosLosProductos.filter(function(producto) {
            var coincideNombre = !filtroNombre || producto.nombre.toLowerCase().includes(filtroNombre);
            var coincideMaterial = !filtroMaterial || producto.material_nombre === filtroMaterial;
            var coincideCategoria = !filtroCategoria || producto.categoria_nombre === filtroCategoria;
            
            return coincideNombre && coincideMaterial && coincideCategoria;
          });
          
          tbody.empty();
          
          if (productosFiltrados.length === 0) {
            sinResultados.show();
            return;
          }
          
          sinResultados.hide();
          
          productosFiltrados.forEach(function(producto) {
            var precio = producto.precio_unitario ? parseFloat(producto.precio_unitario).toFixed(2) : '0.00';
            var categoria = producto.categoria_nombre || '-';
            var nombreEscapado = $('<div>').text(producto.nombre).html();
            
            var fila = $('<tr>');
            var btnCell = $('<td>');
            var btn = $('<button>')
              .attr('type', 'button')
              .addClass('btn btn-sm btn-primary')
              .html('<i class="fa fa-check"></i>')
              .on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                seleccionarProducto(
                  producto.id,
                  producto.nombre,
                  producto.precio_unitario || 0,
                  producto.precio_id || null,
                  producto.material_nombre || '',
                  producto.categoria_nombre || '',
                  producto.unidad || ''
                );
                return false;
              });
            btnCell.append(btn);
            
            fila.append(btnCell);
            fila.append($('<td>').html('<strong>' + nombreEscapado + '</strong>'));
            fila.append($('<td>').text(producto.material_nombre));
            fila.append($('<td>').text(categoria));
            fila.append($('<td>').text(producto.unidad));
            fila.append($('<td>').text('$' + precio));
            
            tbody.append(fila);
          });
        }
        
        // Aplicar filtros cuando cambian
        $('#filtroNombre, #filtroMaterial, #filtroCategoria').on('input change', function() {
          filtrarYMostrarProductos();
        });
        
        // Función para seleccionar producto y agregarlo a la lista
        function seleccionarProducto(id, nombre, precio, precioId, material, categoria, unidad) {
          console.log('Seleccionando producto:', {id: id, nombre: nombre, precio: precio, precioId: precioId});
          
          // Validar que tenemos los datos necesarios
          if (!id || !nombre) {
            console.error('Error: Datos de producto incompletos');
            swal("Error", "No se pudo seleccionar el producto. Por favor, intente nuevamente.", "error");
            return;
          }
          
          // Verificar si el producto ya está agregado
          var existe = productosSeleccionados.find(function(p) { return p.id === id; });
          if (existe) {
            swal("Información", "Este producto ya está en la lista. Puede editar la cantidad en la tabla.", "info");
            return;
          }
          
          // Agregar producto a la lista
          var producto = {
            id: id,
            nombre: nombre,
            precio: parseFloat(precio) || 0,
            precioId: precioId || null,
            material: material || '',
            categoria: categoria || '',
            unidad: unidad || '',
            cantidad: 1, // Cantidad por defecto
            subtotal: parseFloat(precio) || 0
          };
          
          productosSeleccionados.push(producto);
          contadorProductos++;
          
          // Renderizar la tabla de productos
          renderizarProductosAgregados();
          
          // Mostrar mensaje de éxito
          swal({
            title: "¡Producto agregado!",
            text: nombre + " ha sido agregado a la lista",
            icon: "success",
            timer: 1500,
            buttons: false
          });
          
          // NO cerrar el modal para permitir agregar más productos
          console.log('Producto agregado correctamente:', nombre);
        }
        
        // Función para renderizar la tabla de productos agregados
        function renderizarProductosAgregados() {
          var tbody = $('#tbodyProductosAgregados');
          tbody.empty();
          
          if (productosSeleccionados.length === 0) {
            $('#productosAgregados').hide();
            $('#alertaSinProductos').hide();
            $('#btnLimpiarTodos').hide();
            $('#contadorProductos').text('0');
            calcularTotal();
            return;
          }
          
          // Mostrar el cuadro de productos
          $('#productosAgregados').fadeIn(300);
          $('#alertaSinProductos').hide();
          $('#btnLimpiarTodos').show();
          $('#contadorProductos').text(productosSeleccionados.length);
          
          productosSeleccionados.forEach(function(producto, index) {
            var fila = $('<tr>');
            fila.attr('data-index', index);
            
            // Número de orden
            fila.append($('<td>').html('<strong>' + (index + 1) + '</strong>'));
            
            // Producto
            fila.append($('<td>').html('<strong>' + producto.nombre + '</strong><br><small class="text-muted">' + (producto.categoria || '') + '</small>'));
            
            // Material
            fila.append($('<td>').text(producto.material || '-'));
            
            // Cantidad (editable)
            var cantidadInput = $('<input>')
              .attr('type', 'number')
              .attr('step', '0.01')
              .attr('min', '0.01')
              .addClass('form-control form-control-sm')
              .val(producto.cantidad)
              .on('change input', function() {
                var nuevaCantidad = parseFloat($(this).val()) || 0;
                if (nuevaCantidad <= 0) {
                  $(this).val(1);
                  nuevaCantidad = 1;
                }
                producto.cantidad = nuevaCantidad;
                producto.subtotal = producto.cantidad * producto.precio;
                actualizarFilaProducto(index);
                calcularTotal();
              });
            fila.append($('<td>').append(cantidadInput));
            
            // Precio Unitario (editable)
            var precioInput = $('<input>')
              .attr('type', 'number')
              .attr('step', '0.01')
              .attr('min', '0')
              .addClass('form-control form-control-sm')
              .val(producto.precio.toFixed(2))
              .on('change input', function() {
                var nuevoPrecio = parseFloat($(this).val()) || 0;
                producto.precio = nuevoPrecio;
                producto.subtotal = producto.cantidad * producto.precio;
                actualizarFilaProducto(index);
                calcularTotal();
              });
            fila.append($('<td>').append(precioInput));
            
            // Subtotal
            fila.append($('<td>').html('<strong>$' + producto.subtotal.toFixed(2) + '</strong>'));
            
            // Botón eliminar
            var btnEliminar = $('<button>')
              .attr('type', 'button')
              .addClass('btn btn-sm btn-danger')
              .html('<i class="fa fa-times"></i>')
              .on('click', function() {
                eliminarProducto(index);
              });
            fila.append($('<td>').append(btnEliminar));
            
            tbody.append(fila);
          });
          
          calcularTotal();
        }
        
        // Función para actualizar una fila de producto
        function actualizarFilaProducto(index) {
          var producto = productosSeleccionados[index];
          if (!producto) return;
          
          var fila = $('#tbodyProductosAgregados tr[data-index="' + index + '"]');
          fila.find('td:eq(5)').html('<strong>$' + producto.subtotal.toFixed(2) + '</strong>');
        }
        
        // Función para eliminar un producto (simplificada para depuración)
        function eliminarProducto(index) {
          console.log("Eliminar producto llamado para el índice:", index);
        }
        
        // Función para limpiar todos los productos
        function limpiarTodosProductos() {
          if (productosSeleccionados.length === 0) return;
          
          swal({
            title: "¿Limpiar todos los productos?",
            text: "Se eliminarán todos los productos de la lista",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          }).then((willDelete) => {
            if (willDelete) {
              productosSeleccionados = [];
              renderizarProductosAgregados();
              calcularTotal();
              swal("Lista limpiada", "Todos los productos han sido eliminados", "success");
            }
          });
        }
        
        // Hacer funciones globales
        window.eliminarProducto = eliminarProducto;
        window.limpiarTodosProductos = limpiarTodosProductos;
        
        // Calcular total automáticamente
        $('#iva, #descuento').on('input', function() {
          calcularTotal();
        });
        
        function calcularTotal() {
          // Calcular subtotal de todos los productos
          var subtotalProductos = 0;
          productosSeleccionados.forEach(function(producto) {
            subtotalProductos += producto.subtotal;
          });
          
          // Actualizar subtotal en la tabla
          $('#subtotalProductos').text('$' + subtotalProductos.toFixed(2));
          $('#subtotalProductosResumen').text('$' + subtotalProductos.toFixed(2));
          
          // Calcular total con IVA y descuento
          var iva = parseFloat($('#iva').val()) || 0;
          var descuento = parseFloat($('#descuento').val()) || 0;
          var total = subtotalProductos + iva - descuento;
          
          // Actualizar resumen
          $('#ivaResumen').text('$' + iva.toFixed(2));
          $('#descuentoResumen').text('$' + descuento.toFixed(2));
          $('#totalCompra').text('$' + total.toFixed(2));
        }
        
        // Guardar nueva compra
        $('#btnGuardarCompra').click(function() {
          var form = $('#formNuevaCompra')[0];
          
          // Validar que hay productos seleccionados
          if (productosSeleccionados.length === 0) {
            swal("Error", "Debe agregar al menos un producto haciendo clic en 'Agregar Producto'", "error");
            return;
          }
          
          // Validar campos del formulario
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          // Validar que todos los productos tengan cantidad válida
          var productosInvalidos = productosSeleccionados.filter(function(p) {
            return !p.cantidad || p.cantidad <= 0 || !p.precio || p.precio <= 0;
          });
          
          if (productosInvalidos.length > 0) {
            swal("Error", "Todos los productos deben tener cantidad y precio válidos", "error");
            return;
          }
          
          // Calcular totales
          var subtotal = 0;
          productosSeleccionados.forEach(function(producto) {
            subtotal += producto.subtotal;
          });
          
          var iva = parseFloat($('#iva').val()) || 0;
          var descuento = parseFloat($('#descuento').val()) || 0;
          var total = subtotal + iva - descuento;
          
          // Preparar detalles de productos
          var detalles = productosSeleccionados.map(function(producto) {
            return {
              producto_id: producto.id,
              precio_id: producto.precioId,
              cantidad: producto.cantidad,
              subtotal: producto.subtotal
            };
          });
          
          var formData = {
            proveedor_id: $('#proveedor_id').val(),
            sucursal_id: $('#sucursal_id').val(),
            fecha_compra: $('#fecha_compra').val(),
            numero_factura: $('#numero_factura').val(),
            tipo_comprobante: $('#tipo_comprobante').val(),
            subtotal: subtotal,
            iva: iva,
            descuento: descuento,
            total: total,
            estado: $('#estado').val(),
            notas: $('#notas').val(),
            detalles: JSON.stringify(detalles),
            action: 'crear'
          };
          
          $.ajax({
            url: 'api.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                swal({
                  title: "¡Éxito!",
                  text: response.message,
                  icon: "success",
                  buttons: {
                    confirm: {
                      text: "Ver Factura",
                      value: true
                    },
                    cancel: {
                      text: "Volver",
                      value: false
                  }
                  }
                }).then((verFactura) => {
                  if (verFactura) {
                    window.location.href = 'ver.php?id=' + response.id;
                  } else {
                    window.location.href = 'index.php';
                  }
                });
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar la compra';
              swal("Error", error, "error");
            }
          });
        });
        
        // Cargar datos al iniciar
        cargarDatos();
      });
    </script>
  </body>
</html>

