<?php
/**
 * Registro de Ventas
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
    <title>Registro de Ventas - Sistema de Reciclaje</title>
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
        <?php
          $basePath = '..';
          include __DIR__ . '/../includes/sidebar-logo.php';
        ?>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <?php
              $basePath = '..';
              $currentRoute = 'ventas';
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
                  src="../assets/img/logo.jpg"
                  alt="HNOSYÁNEZ S.A."
                  class="navbar-brand"
                  height="50"
                  style="object-fit: contain; border-radius: 8px;"
                />
              </a>
            </div>
          </div>
          <?php
            $basePath = '..';
            include __DIR__ . '/../includes/user-header.php';
            include __DIR__ . '/../includes/modal-foto-perfil.php';
          ?>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Registro de Ventas</h3>
                <h6 class="op-7 mb-2">Registra ventas de materiales reciclables - Actualiza inventario automáticamente</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalNuevaVenta">
                  <i class="fa fa-plus"></i> Nueva Venta
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Historial de Ventas</div>
                      <div class="card-tools">
                        <input type="date" class="form-control form-control-sm" id="filtroFecha" style="width: 200px; display: inline-block;">
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="ventasTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>Fecha</th>
                            <th>Sucursal</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                            <th>Cliente</th>
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

        <?php include __DIR__ . '/../includes/footer.php'; ?>
      </div>
    </div>

    <!-- Modal Nueva Venta -->
    <div class="modal fade" id="modalNuevaVenta" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nueva Venta de Material Reciclable</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formNuevaVenta">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Fecha de Venta <span class="text-danger">*</span></label>
                    <input type="date" id="fecha_venta" name="fecha_venta" class="form-control" required>
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
                    <label>Cliente <span class="text-danger">*</span></label>
                    <select id="cliente_id" name="cliente_id" class="form-control" required>
                      <option value="">Seleccione un cliente</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Inventario <span class="text-danger">*</span></label>
                    <select id="inventario_id" name="inventario_id" class="form-control" required>
                      <option value="">Primero seleccione una sucursal</option>
                    </select>
                    <small class="form-text text-muted">Seleccione el producto del inventario que desea vender</small>
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
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Cantidad <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" id="cantidad" name="cantidad" class="form-control" placeholder="0.00" required>
                    <small class="form-text text-muted" id="stockDisponible">Stock disponible: -</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Precio Unitario (Venta) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" id="precio_unitario" name="precio_unitario" class="form-control" placeholder="0.00" required>
                    <small class="form-text text-muted">Se cargará automáticamente desde el precio de venta del producto</small>
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
                    <label>Método de Pago</label>
                    <select id="metodo_pago" name="metodo_pago" class="form-control">
                      <option value="efectivo">Efectivo</option>
                      <option value="transferencia">Transferencia</option>
                      <option value="cheque">Cheque</option>
                      <option value="tarjeta">Tarjeta</option>
                      <option value="credito">Crédito</option>
                    </select>
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
                    <strong>Total:</strong> <span id="totalVenta">$0.00</span>
                    <br>
                    <small>El inventario se actualizará automáticamente cuando el estado sea "Completada"</small>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Notas</label>
                    <textarea id="notas" name="notas" class="form-control" rows="2" placeholder="Notas adicionales sobre la venta"></textarea>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarVenta">Registrar Venta</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ver Venta -->
    <div class="modal fade" id="modalVerVenta" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detalle de Venta #1</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <p><strong>Fecha:</strong> 2024-11-05</p>
                <p><strong>Sucursal:</strong> Sucursal Central</p>
                <p><strong>Categoría:</strong> PET</p>
                <p><strong>Cantidad:</strong> 50.00 kg</p>
              </div>
              <div class="col-md-6">
                <p><strong>Precio Unitario:</strong> $3.00</p>
                <p><strong>Total:</strong> $150.00</p>
                <p><strong>Cliente:</strong> Industrias ABC</p>
                <p><strong>Estado:</strong> <span class="badge badge-success">Completada</span></p>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-12">
                <div class="alert alert-success">
                  <i class="fas fa-check-circle"></i> 
                  <strong>Inventario actualizado:</strong> Se restaron 50.00 kg de PET del inventario
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
        var table = $('#ventasTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          },
          "order": [[0, "desc"]]
        });
        
        // Establecer fecha actual por defecto
        $('#fecha_venta').val(new Date().toISOString().split('T')[0]);
        
        // Cargar datos iniciales
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
          
          // Cargar clientes
          $.ajax({
            url: '../clientes/api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var select = $('#cliente_id');
                select.empty().append('<option value="">Seleccione un cliente</option>');
                response.data.forEach(function(cliente) {
                  if (cliente.estado === 'activo') {
                    select.append('<option value="' + cliente.id + '">' + cliente.nombre + '</option>');
                  }
                });
              }
            }
          });
        }
        
        window.cargarVentas = cargarVentas;
        
        // Cargar inventarios cuando se selecciona una sucursal
        $('#sucursal_id').change(function() {
          var sucursal_id = $(this).val();
          if (sucursal_id) {
            $.ajax({
              url: 'api.php?action=inventarios&sucursal_id=' + sucursal_id,
              method: 'GET',
              dataType: 'json',
              success: function(response) {
                if (response.success) {
                  var select = $('#inventario_id');
                  select.empty().append('<option value="">Seleccione un producto del inventario</option>');
                  response.data.forEach(function(inventario) {
                    var texto = inventario.producto_nombre + ' (' + inventario.cantidad + ' ' + inventario.unidad + ' disponible)';
                    select.append('<option value="' + inventario.inventario_id + '" ' +
                      'data-producto-id="' + inventario.producto_id + '" ' +
                      'data-precio="' + (inventario.precio_unitario || 0) + '" ' +
                      'data-precio-id="' + (inventario.precio_id || '') + '" ' +
                      'data-cantidad="' + inventario.cantidad + '" ' +
                      'data-unidad="' + inventario.unidad + '">' + texto + '</option>');
                  });
                }
              }
            });
          } else {
            $('#inventario_id').empty().append('<option value="">Primero seleccione una sucursal</option>');
          }
        });
        
        // Auto-completar campos cuando se selecciona inventario
        $('#inventario_id').change(function() {
          var option = $(this).find('option:selected');
          if (option.val()) {
            var precio = option.data('precio') || 0;
            var cantidad = option.data('cantidad') || 0;
            $('#precio_unitario').val(precio);
            $('#stockDisponible').text('Stock disponible: ' + cantidad + ' ' + option.data('unidad'));
            calcularTotal();
          } else {
            $('#precio_unitario').val('');
            $('#stockDisponible').text('Stock disponible: -');
          }
        });
        
        // Cargar ventas
        function cargarVentas() {
          $.ajax({
            url: 'api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                table.clear();
                response.data.forEach(function(venta) {
                  // Obtener el primer detalle para mostrar en la tabla
                  var detalle = venta.detalles && venta.detalles.length > 0 ? venta.detalles[0] : null;
                  
                  if (detalle) {
                    var badgeEstado = '';
                    if (venta.estado === 'completada') {
                      badgeEstado = '<span class="badge badge-success">Completada</span>';
                    } else if (venta.estado === 'pendiente') {
                      badgeEstado = '<span class="badge badge-warning">Pendiente</span>';
                    } else {
                      badgeEstado = '<span class="badge badge-danger">Cancelada</span>';
                    }
                    
                    var productoNombre = detalle.producto_nombre || detalle.nombre_producto || '-';
                    var unidad = detalle.unidad_simbolo || detalle.unidad || '-';
                    var precio = detalle.precio_unitario ? parseFloat(detalle.precio_unitario).toFixed(2) : '0.00';
                    
                    table.row.add([
                      venta.fecha_venta,
                      venta.sucursal_nombre,
                      '<strong>' + productoNombre + '</strong>',
                      detalle.cantidad,
                      unidad,
                      '$' + precio,
                      '$' + parseFloat(venta.total).toFixed(2),
                      venta.cliente_nombre,
                      badgeEstado,
                      '<button class="btn btn-link btn-primary btn-sm" onclick="verVenta(' + venta.id + ')"><i class="fa fa-eye"></i></button> ' +
                      '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarVenta(' + venta.id + ')"><i class="fa fa-times"></i></button>'
                    ]);
                  } else {
                    // Si no hay detalles, mostrar la venta sin detalles
                    var badgeEstado = '';
                    if (venta.estado === 'completada') {
                      badgeEstado = '<span class="badge badge-success">Completada</span>';
                    } else if (venta.estado === 'pendiente') {
                      badgeEstado = '<span class="badge badge-warning">Pendiente</span>';
                    } else {
                      badgeEstado = '<span class="badge badge-danger">Cancelada</span>';
                    }
                    
                    table.row.add([
                      venta.fecha_venta,
                      venta.sucursal_nombre,
                      '-',
                      '-',
                      '-',
                      '-',
                      '$' + parseFloat(venta.total).toFixed(2),
                      venta.cliente_nombre,
                      badgeEstado,
                      '<button class="btn btn-link btn-primary btn-sm" onclick="verVenta(' + venta.id + ')"><i class="fa fa-eye"></i></button> ' +
                      '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarVenta(' + venta.id + ')"><i class="fa fa-times"></i></button>'
                    ]);
                  }
                });
                table.draw();
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar las ventas", "error");
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
          $('#totalVenta').text('$' + total.toFixed(2));
        }
        
        // Guardar nueva venta
        $('#btnGuardarVenta').click(function() {
          var form = $('#formNuevaVenta')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var inventario_id = $('#inventario_id').val();
          if (!inventario_id) {
            swal("Error", "Debe seleccionar un producto del inventario", "error");
            return;
          }
          
          var inventarioOption = $('#inventario_id option:selected');
          var producto_id = inventarioOption.data('producto-id');
          var precio_id = inventarioOption.data('precio-id') || null;
          
          var cantidad = parseFloat($('#cantidad').val()) || 0;
          var precio_unitario = parseFloat($('#precio_unitario').val()) || 0;
          var iva = parseFloat($('#iva').val()) || 0;
          var descuento = parseFloat($('#descuento').val()) || 0;
          var subtotal = cantidad * precio_unitario;
          var total = subtotal + iva - descuento;
          
          // Verificar stock disponible
          var stockDisponible = parseFloat(inventarioOption.data('cantidad')) || 0;
          if (cantidad > stockDisponible) {
            swal("Error", "La cantidad solicitada (" + cantidad + ") excede el stock disponible (" + stockDisponible + ")", "error");
            return;
          }
          
          var formData = {
            cliente_id: $('#cliente_id').val(),
            sucursal_id: $('#sucursal_id').val(),
            fecha_venta: $('#fecha_venta').val(),
            numero_factura: $('#numero_factura').val(),
            tipo_comprobante: $('#tipo_comprobante').val(),
            subtotal: subtotal,
            iva: iva,
            descuento: descuento,
            total: total,
            metodo_pago: $('#metodo_pago').val(),
            estado: $('#estado').val(),
            notas: $('#notas').val(),
            detalles: JSON.stringify([{
              inventario_id: inventario_id,
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
                $('#modalNuevaVenta').modal('hide');
                $('#formNuevaVenta')[0].reset();
                $('#fecha_venta').val(new Date().toISOString().split('T')[0]);
                $('#inventario_id').empty().append('<option value="">Seleccione un producto del inventario</option>');
                calcularTotal();
                cargarVentas();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar la venta';
              swal("Error", error, "error");
            }
          });
        });
        
        // Cargar datos al iniciar
        cargarDatos();
        cargarVentas();
      });
      
      function verVenta(id) {
        swal("Próximamente", "La funcionalidad de visualización estará disponible pronto", "info");
      }
      
      function eliminarVenta(id) {
        swal({
          title: "¿Está seguro?",
          text: "La venta será cancelada",
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
                  cargarVentas();
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

