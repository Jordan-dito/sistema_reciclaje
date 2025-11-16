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
                    <input type="text" id="numero_factura" name="numero_factura" class="form-control" placeholder="Opcional">
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
                    <select id="producto_id" name="producto_id" class="form-control" required>
                      <option value="">Seleccione un producto</option>
                      <!-- Las opciones se cargarán dinámicamente -->
                    </select>
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
          
          // Cargar productos
          $.ajax({
            url: 'api.php?action=productos',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var select = $('#producto_id');
                select.empty().append('<option value="">Seleccione un producto</option>');
                response.data.forEach(function(producto) {
                  var texto = producto.nombre + ' (' + producto.material_nombre + ' - ' + producto.unidad + ')';
                  var precio = producto.precio_unitario ? ' - $' + parseFloat(producto.precio_unitario).toFixed(2) : '';
                  select.append('<option value="' + producto.id + '" data-precio="' + (producto.precio_unitario || 0) + '" data-precio-id="' + (producto.precio_id || '') + '">' + texto + precio + '</option>');
                });
              }
            }
          });
        }
        
        // Auto-completar precio cuando se selecciona producto
        $('#producto_id').change(function() {
          var option = $(this).find('option:selected');
          if (option.val()) {
            var precio = option.data('precio') || 0;
            $('#precio_unitario').val(precio);
            calcularTotal();
          }
        });
        
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
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var producto_id = $('#producto_id').val();
          if (!producto_id) {
            swal("Error", "Debe seleccionar un producto", "error");
            return;
          }
          
          var cantidad = parseFloat($('#cantidad').val()) || 0;
          var precio_unitario = parseFloat($('#precio_unitario').val()) || 0;
          var iva = parseFloat($('#iva').val()) || 0;
          var descuento = parseFloat($('#descuento').val()) || 0;
          var subtotal = cantidad * precio_unitario;
          var total = subtotal + iva - descuento;
          
          var productoOption = $('#producto_id option:selected');
          var precio_id = productoOption.data('precio-id') || null;
          
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

