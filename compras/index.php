<?php
/**
 * Registro de Compras
 * Sistema de Gestión de Reciclaje
 */

// Verificar autenticación
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Registro de Compras - Sistema de Reciclaje</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="../assets/img/kaiadmin/favicon.ico"
      type="image/x-icon"
    />

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
        <div class="sidebar-logo">
          <div class="logo-header" data-background-color="dark">
            <a href="../Dashboard.php" class="logo">
              <img
                src="../assets/img/kaiadmin/logo_light.svg"
                alt="navbar brand"
                class="navbar-brand"
                height="20"
              />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
            </div>
          </div>
        </div>
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
          <div class="main-header-logo">
            <div class="logo-header" data-background-color="dark">
              <a href="../Dashboard.php" class="logo">
                <img
                  src="../assets/img/kaiadmin/logo_light.svg"
                  alt="navbar brand"
                  class="navbar-brand"
                  height="20"
                />
              </a>
            </div>
          </div>
          <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
            <div class="container-fluid">
              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                      <img src="../assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <li><a class="dropdown-item" href="../config/logout.php">Cerrar Sesión</a></li>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Registro de Compras</h3>
                <h6 class="op-7 mb-2">Registra compras de materiales reciclables - Actualiza inventario y Kardex (PEPS)</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalNuevaCompra">
                  <i class="fa fa-plus"></i> Nueva Compra
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Historial de Compras</div>
                      <div class="card-tools">
                        <input type="date" class="form-control form-control-sm" id="filtroFecha" style="width: 200px; display: inline-block;">
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="comprasTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Sucursal</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                            <th>Proveedor</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Los datos se cargarán dinámicamente desde la base de datos -->
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <footer class="footer">
          <div class="container-fluid d-flex justify-content-between">
            <div class="copyright">
              2024, Sistema de Gestión de Reciclaje
            </div>
          </div>
        </footer>
      </div>
    </div>

    <!-- Modal Nueva Compra -->
    <div class="modal fade" id="modalNuevaCompra" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nueva Compra de Material Reciclable</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
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
                      <option value="boleta">Boleta</option>
                      <option value="recibo">Recibo</option>
                      <option value="nota_credito">Nota de Crédito</option>
                      <option value="otro">Otro</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Producto <span class="text-danger">*</span></label>
                    <div class="input-group mb-2">
                      <input type="text" id="producto_seleccionado" class="form-control" placeholder="Ningún producto seleccionado" readonly style="background-color: #f8f9fa;">
                      <input type="hidden" id="producto_id" name="producto_id" required>
                      <input type="hidden" id="producto_precio" name="producto_precio">
                      <input type="hidden" id="producto_precio_id" name="producto_precio_id">
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuscarProducto">
                        <i class="fa fa-search"></i> Buscar Producto
                      </button>
                    </div>
                    <!-- Panel de información del producto seleccionado -->
                    <div id="productoInfo" class="alert alert-info" style="display: none; margin-top: 10px;">
                      <div class="d-flex align-items-center">
                        <i class="fa fa-check-circle text-success me-2" style="font-size: 1.5em;"></i>
                        <div class="flex-grow-1">
                          <strong id="productoInfoNombre">-</strong>
                          <br>
                          <small id="productoInfoDetalles" class="text-muted">-</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="limpiarProducto()" title="Quitar producto">
                          <i class="fa fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <small class="form-text text-muted"><i class="fa fa-info-circle"></i> Haga clic en "Buscar Producto" para seleccionar un producto de la lista</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Cantidad <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" id="cantidad" name="cantidad" class="form-control" placeholder="0.00" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Precio Unitario (Compra) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" id="precio_unitario" name="precio_unitario" class="form-control" placeholder="0.00" required>
                    <small class="form-text text-muted">Se cargará automáticamente desde el precio de compra del producto</small>
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
                    <i class="fas fa-info-circle"></i> 
                    <strong>Total:</strong> <span id="totalCompra">$0.00</span>
                    <br>
                    <small>El inventario se actualizará automáticamente cuando el estado sea "Completada"</small>
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
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarCompra">Registrar Compra</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ver Compra -->
    <div class="modal fade" id="modalVerCompra" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detalle de Compra #1</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <p><strong>Fecha:</strong> 2024-11-05</p>
                <p><strong>Sucursal:</strong> Sucursal Central</p>
                <p><strong>Categoría:</strong> PET</p>
                <p><strong>Cantidad:</strong> 150.50 kg</p>
              </div>
              <div class="col-md-6">
                <p><strong>Precio Unitario:</strong> $2.50</p>
                <p><strong>Total:</strong> $376.25</p>
                <p><strong>Proveedor:</strong> Reciclajes S.A.</p>
                <p><strong>Estado:</strong> <span class="badge badge-success">Registrada</span></p>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-12">
                <div class="alert alert-success">
                  <i class="fas fa-check-circle"></i> 
                  <strong>Inventario actualizado:</strong> El material se agregó al inventario con método PEPS
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
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
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/setting-demo.js"></script>
    <script>
      $(document).ready(function() {
        var table = $('#comprasTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          },
          "order": [[0, "desc"]]
        });
        
        // Establecer fecha actual por defecto
        $('#fecha_compra').val(new Date().toISOString().split('T')[0]);
        
        // Cargar siguiente número de factura cuando se abre el modal
        $('#modalNuevaCompra').on('show.bs.modal', function() {
          // Limpiar el campo primero
          $('#numero_factura').val('');
          // Limpiar producto seleccionado
          $('#producto_id').val('');
          $('#producto_seleccionado').val('').css('background-color', '#f8f9fa').css('border-color', '');
          $('#producto_precio').val('');
          $('#producto_precio_id').val('');
          $('#productoInfo').hide();
          // Cargar el siguiente número desde la base de datos
          cargarSiguienteNumeroFactura();
        });
        
        // Función para cargar el siguiente número de factura desde la base de datos
        function cargarSiguienteNumeroFactura() {
          // Mostrar indicador de carga
          $('#numero_factura').val('Cargando...');
          
          $.ajax({
            url: 'api.php?action=siguiente_numero_factura',
            method: 'GET',
            dataType: 'json',
            cache: false, // No usar caché para obtener siempre el último número
            success: function(response) {
              if (response.success && response.numero_factura) {
                $('#numero_factura').val(response.numero_factura);
                console.log('Número de factura cargado desde BD:', response.numero_factura);
              } else {
                // Si no hay respuesta válida, usar 00001
                $('#numero_factura').val('00001');
                console.log('No se pudo obtener número de BD, usando 00001');
              }
            },
            error: function(xhr, status, error) {
              // Si hay error, establecer un número por defecto
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
        var materialesUnicos = [];
        var categoriasUnicas = [];
        
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
        
        // Función para seleccionar producto
        function seleccionarProducto(id, nombre, precio, precioId, material, categoria, unidad) {
          console.log('Seleccionando producto:', {id: id, nombre: nombre, precio: precio, precioId: precioId});
          
          // Validar que tenemos los datos necesarios
          if (!id || !nombre) {
            console.error('Error: Datos de producto incompletos');
            swal("Error", "No se pudo seleccionar el producto. Por favor, intente nuevamente.", "error");
            return;
          }
          
          // Actualizar los campos del formulario
          $('#producto_id').val(id);
          $('#producto_seleccionado').val(nombre);
          $('#producto_precio').val(precio || 0);
          $('#producto_precio_id').val(precioId || '');
          $('#precio_unitario').val(precio || 0);
          
          // Cambiar el estilo del campo para indicar que está seleccionado
          $('#producto_seleccionado').css('background-color', '#d4edda').css('border-color', '#28a745');
          
          // Mostrar panel de información del producto
          var detalles = [];
          if (material) detalles.push('Material: ' + material);
          if (categoria) detalles.push('Categoría: ' + categoria);
          if (unidad) detalles.push('Unidad: ' + unidad);
          if (precio) detalles.push('Precio: $' + parseFloat(precio).toFixed(2));
          
          $('#productoInfoNombre').text(nombre);
          $('#productoInfoDetalles').text(detalles.join(' | '));
          $('#productoInfo').fadeIn(300);
          
          // Cerrar el modal después de un pequeño delay para que se vea la actualización
          setTimeout(function() {
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalBuscarProducto'));
            if (modal) {
              modal.hide();
            } else {
              $('#modalBuscarProducto').modal('hide');
            }
            
            // Calcular total
            calcularTotal();
            console.log('Producto seleccionado correctamente:', nombre);
            console.log('Valores actualizados:', {
              producto_id: $('#producto_id').val(),
              producto_seleccionado: $('#producto_seleccionado').val(),
              precio_unitario: $('#precio_unitario').val()
            });
          }, 300);
        }
        
        // Función para limpiar producto seleccionado
        function limpiarProducto() {
          $('#producto_id').val('');
          $('#producto_seleccionado').val('').css('background-color', '#f8f9fa').css('border-color', '');
          $('#producto_precio').val('');
          $('#producto_precio_id').val('');
          $('#precio_unitario').val('');
          $('#productoInfo').fadeOut(300);
          calcularTotal();
        }
        
        // Hacer la función global para que funcione desde el onclick
        window.limpiarProducto = limpiarProducto;
        
        window.cargarCompras = cargarCompras;
        
        // Cargar compras
        function cargarCompras() {
          $.ajax({
            url: 'api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                table.clear();
                response.data.forEach(function(compra) {
                  // Obtener el primer detalle para mostrar en la tabla
                  var detalle = compra.detalles && compra.detalles.length > 0 ? compra.detalles[0] : null;
                  
                  if (detalle) {
                    var badgeEstado = '';
                    if (compra.estado === 'completada') {
                      badgeEstado = '<span class="badge badge-success">Completada</span>';
                    } else if (compra.estado === 'pendiente') {
                      badgeEstado = '<span class="badge badge-warning">Pendiente</span>';
                    } else {
                      badgeEstado = '<span class="badge badge-danger">Cancelada</span>';
                    }
                    
                    var productoNombre = detalle.producto_nombre || detalle.nombre_producto || '-';
                    var unidad = detalle.unidad_simbolo || detalle.unidad || '-';
                    var precio = detalle.precio_unitario ? parseFloat(detalle.precio_unitario).toFixed(2) : '0.00';
                    
                    table.row.add([
                      compra.id,
                      compra.fecha_compra,
                      compra.sucursal_nombre,
                      '<strong>' + productoNombre + '</strong>',
                      detalle.cantidad,
                      unidad,
                      '$' + precio,
                      '$' + parseFloat(compra.total).toFixed(2),
                      compra.proveedor_nombre,
                      badgeEstado,
                      '<button class="btn btn-link btn-primary btn-sm" onclick="verCompra(' + compra.id + ')"><i class="fa fa-eye"></i></button> ' +
                      '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarCompra(' + compra.id + ')"><i class="fa fa-times"></i></button>'
                    ]);
                  } else {
                    // Si no hay detalles, mostrar la compra sin detalles
                    var badgeEstado = '';
                    if (compra.estado === 'completada') {
                      badgeEstado = '<span class="badge badge-success">Completada</span>';
                    } else if (compra.estado === 'pendiente') {
                      badgeEstado = '<span class="badge badge-warning">Pendiente</span>';
                    } else {
                      badgeEstado = '<span class="badge badge-danger">Cancelada</span>';
                    }
                    
                    table.row.add([
                      compra.id,
                      compra.fecha_compra,
                      compra.sucursal_nombre,
                      '-',
                      '-',
                      '-',
                      '-',
                      '$' + parseFloat(compra.total).toFixed(2),
                      compra.proveedor_nombre,
                      badgeEstado,
                      '<button class="btn btn-link btn-primary btn-sm" onclick="verCompra(' + compra.id + ')"><i class="fa fa-eye"></i></button> ' +
                      '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarCompra(' + compra.id + ')"><i class="fa fa-times"></i></button>'
                    ]);
                  }
                });
                table.draw();
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar las compras", "error");
            }
          });
        }
        
        // Calcular total automáticamente
        $('#cantidad, #precio_unitario, #iva, #descuento').on('input', function() {
          calcularTotal();
        });
        
        function calcularTotal() {
          var cantidad = parseFloat($('#cantidad').val()) || 0;
          var precio = parseFloat($('#precio_unitario').val()) || 0;
          var iva = parseFloat($('#iva').val()) || 0;
          var descuento = parseFloat($('#descuento').val()) || 0;
          var subtotal = cantidad * precio;
          var total = subtotal + iva - descuento;
          $('#totalCompra').text('$' + total.toFixed(2));
        }
        
        // Actualizar subtotal cuando cambia cantidad o precio
        $('#cantidad, #precio_unitario').on('input', function() {
          var cantidad = parseFloat($('#cantidad').val()) || 0;
          var precio = parseFloat($('#precio_unitario').val()) || 0;
          var subtotal = cantidad * precio;
          $('#subtotalCompra').text('$' + subtotal.toFixed(2));
        });
        
        // Guardar nueva compra
        $('#btnGuardarCompra').click(function() {
          var form = $('#formNuevaCompra')[0];
          
          // Validar producto seleccionado primero
          var producto_id = $('#producto_id').val();
          if (!producto_id) {
            swal("Error", "Debe seleccionar un producto haciendo clic en 'Buscar Producto'", "error");
            $('#producto_seleccionado').css('border-color', '#dc3545').focus();
            return;
          }
          
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var cantidad = parseFloat($('#cantidad').val()) || 0;
          var precio_unitario = parseFloat($('#precio_unitario').val()) || 0;
          var iva = parseFloat($('#iva').val()) || 0;
          var descuento = parseFloat($('#descuento').val()) || 0;
          var subtotal = cantidad * precio_unitario;
          var total = subtotal + iva - descuento;
          
          var precio_id = $('#producto_precio_id').val() || null;
          
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
            detalles: JSON.stringify([{
              producto_id: producto_id,
              precio_id: precio_id,
              cantidad: cantidad,
              subtotal: subtotal
            }]),
            action: 'crear'
          };
          
          $.ajax({
            url: 'api.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                swal("¡Éxito!", response.message, "success");
                $('#modalNuevaCompra').modal('hide');
                $('#formNuevaCompra')[0].reset();
                $('#fecha_compra').val(new Date().toISOString().split('T')[0]);
                $('#numero_factura').val(''); // Limpiar para que se recargue al abrir de nuevo
                $('#producto_id').val('');
                $('#producto_seleccionado').val('').css('background-color', '#f8f9fa').css('border-color', '');
                $('#producto_precio').val('');
                $('#producto_precio_id').val('');
                $('#productoInfo').hide();
                calcularTotal();
                cargarCompras();
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
        cargarCompras();
      });
      
      function verCompra(id) {
        swal("Próximamente", "La funcionalidad de visualización estará disponible pronto", "info");
      }
      
      function eliminarCompra(id) {
        swal({
          title: "¿Está seguro?",
          text: "La compra será cancelada",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            $.ajax({
              url: 'api.php',
              method: 'POST',
              data: { id: id, action: 'eliminar' },
              dataType: 'json',
              success: function(response) {
                if (response.success) {
                  swal("¡Éxito!", response.message, "success");
                  cargarCompras();
                } else {
                  swal("Error", response.message, "error");
                }
              }
            });
          }
        });
      }
    </script>
  </body>
</html>

