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
                    <!-- Filtros por estado (Tabs) -->
                    <ul class="nav nav-pills nav-secondary nav-pills-no-bd mb-3" id="pills-tab" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active" id="tab-activos" data-bs-toggle="pill" href="#pills-activos" role="tab" onclick="cambiarFiltroEstado('activos')">Activos</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="tab-completadas" data-bs-toggle="pill" href="#pills-completadas" role="tab" onclick="cambiarFiltroEstado('completada')">Completadas</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="tab-pendientes" data-bs-toggle="pill" href="#pills-pendientes" role="tab" onclick="cambiarFiltroEstado('pendiente')">Pendientes</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="tab-canceladas" data-bs-toggle="pill" href="#pills-canceladas" role="tab" onclick="cambiarFiltroEstado('cancelada')">Inactivos</a>
                      </li>
                    </ul>

                    <div class="table-responsive">
                      <table id="ventasTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th style="width: 20px;"></th>
                            <th>Fecha</th>
                            <th>Sucursal</th>
                            <th>Productos</th>
                            <th>Cant. Total</th>
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
    <div class="modal fade" id="modalNuevaVenta" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
                    <div class="input-group">
                      <input type="text" id="producto_seleccionado" class="form-control" placeholder="Seleccione un producto" readonly required style="background-color: #fff;">
                      <input type="hidden" id="inventario_id" name="inventario_id">
                      <button class="btn btn-outline-secondary" type="button" id="btnBuscarInventario">
                        <i class="fa fa-search"></i>
                      </button>
                    </div>
                    <small class="form-text text-muted">Seleccione el producto del inventario que desea vender</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Número de Factura</label>
                    <input type="text" id="numero_factura" name="numero_factura" class="form-control" placeholder="Se generará automáticamente" readonly>
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
                    <input type="number" step="0.01" id="precio_unitario" name="precio_unitario" class="form-control" placeholder="0.00" required readonly style="background-color: #e9ecef;">
                    <small class="form-text text-muted">Se cargará automáticamente desde el precio de venta del producto</small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>IVA (%)</label>
                    <select id="iva" name="iva" class="form-control">
                      <option value="0">0%</option>
                      <option value="15">15%</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Tipo Descuento</label>
                    <select id="tipo_descuento" name="tipo_descuento" class="form-control">
                      <option value="dinero">En $</option>
                      <option value="porcentaje">En %</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label id="labelDescuento">Descuento ($)</label>
                    <input type="number" step="0.01" id="descuento" name="descuento" class="form-control" placeholder="0.00" value="0">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Método de Pago</label>
                    <select id="metodo_pago" name="metodo_pago" class="form-control" style="background-color: #e9ecef; pointer-events: none;" tabindex="-1">
                      <option value="efectivo" selected>Efectivo</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
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

    <!-- Modal Buscar Inventario -->
    <div class="modal fade" id="modalBuscarInventario" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Buscar Producto en Inventario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-md-12">
                <input type="text" id="filtroInventario" class="form-control" placeholder="Buscar por nombre, material o categoría...">
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-hover table-striped" id="tablaInventario">
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th>Material</th>
                    <th>Categoría</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Los datos se cargarán dinámicamente -->
                </tbody>
              </table>
            </div>
            <div id="mensajeSinInventario" class="alert alert-warning text-center" style="display: none;">
              No hay productos disponibles en esta sucursal o no coinciden con la búsqueda.
            </div>
            <div id="mensajeCargandoInventario" class="text-center py-3" style="display: none;">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
              <p class="mt-2">Cargando inventario...</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ver Venta -->
    <div class="modal fade" id="modalVerVenta" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
    <?php
      $basePath = '..';
      include __DIR__ . '/../includes/footer-scripts.php';
    ?>
    <script>
      $(document).ready(function() {
        var table = $('#ventasTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          },
          "order": [[1, "desc"]],
          "columnDefs": [
            { "orderable": false, "targets": [0, 8] }
          ]
        });

        // Función para formatear el detalle desplegable
        function formatoDetalle(d) {
          var html = '<div class="p-3 bg-light rounded border">' +
                     '<h6 class="fw-bold mb-2"><i class="fa fa-list"></i> Detalle de Productos:</h6>' +
                     '<table class="table table-sm table-bordered bg-white mb-0">' +
                     '<thead class="thead-light"><tr>' +
                     '<th>Producto</th><th>Cantidad</th><th>Unidad</th><th>Precio Unitario</th><th>Subtotal</th>' +
                     '</tr></thead><tbody>';
          
          d.detalles.forEach(function(det) {
            html += '<tr>' +
                    '<td>' + (det.producto_nombre || '-') + '</td>' +
                    '<td>' + parseFloat(det.cantidad || 0).toFixed(2) + '</td>' +
                    '<td>' + (det.unidad_simbolo || '-') + '</td>' +
                    '<td>$' + parseFloat(det.precio_unitario || 0).toFixed(2) + '</td>' +
                    '<td>$' + parseFloat(det.subtotal || 0).toFixed(2) + '</td>' +
                    '</tr>';
          });
          
          html += '</tbody></table></div>';
          return html;
        }

        // Listener para el botón de expansión
        $('#ventasTable tbody').on('click', 'td.details-control', function() {
          var tr = $(this).closest('tr');
          var row = table.row(tr);
          var icon = $(this).find('i');

          if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            icon.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-primary');
          } else {
            row.child(formatoDetalle(tr.data('venta'))).show();
            tr.addClass('shown');
            icon.removeClass('fa-plus-circle text-primary').addClass('fa-minus-circle text-danger');
          }
        });

        var estadoActual = 'activos';

        window.cambiarFiltroEstado = function(nuevoEstado) {
          estadoActual = nuevoEstado;
          cargarVentas();
        };

        window.cargarVentas = cargarVentas;
        
        // Establecer fecha actual por defecto
        $('#fecha_venta').val(new Date().toISOString().split('T')[0]);
        
        // Cargar datos iniciales
        function cargarDatos() {
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
        
        var inventarioData = [];

        $('#btnBuscarInventario').click(function() {
          var sucursal_id = $('#sucursal_id').val();
          if (!sucursal_id) {
            swal("Atención", "Primero debe seleccionar una sucursal", "warning");
            return;
          }
          $('#modalBuscarInventario').modal('show');
          cargarInventarioModal(sucursal_id);
        });

        $('#sucursal_id').change(function() {
          limpiarSeleccionProducto();
        });

        function limpiarSeleccionProducto() {
          $('#inventario_id').val('');
          $('#producto_seleccionado').val('');
          $('#precio_unitario').val('');
          $('#stockDisponible').text('Stock disponible: -');
          calcularTotal();
        }

        function cargarInventarioModal(sucursal_id) {
          var tbody = $('#tablaInventario tbody');
          var mensajeSin = $('#mensajeSinInventario');
          var mensajeCargando = $('#mensajeCargandoInventario');
          
          tbody.empty();
          mensajeSin.hide();
          mensajeCargando.show();
          
          $.ajax({
            url: 'api.php?action=inventarios&sucursal_id=' + sucursal_id,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              mensajeCargando.hide();
              inventarioData = [];
              if (response.success && response.data && response.data.length > 0) {
                inventarioData = response.data;
                renderizarTablaInventario(inventarioData);
              } else {
                mensajeSin.text("No hay productos disponibles en esta sucursal").show();
              }
            },
            error: function() {
              mensajeCargando.hide();
              mensajeSin.text("Error al cargar inventario").show();
            }
          });
        }

        function renderizarTablaInventario(datos) {
          var tbody = $('#tablaInventario tbody');
          var mensajeSin = $('#mensajeSinInventario');
          tbody.empty();
          if (datos.length === 0) {
            mensajeSin.show();
            return;
          }
          mensajeSin.hide();
          datos.forEach(function(item) {
            var precio = parseFloat(item.precio_unitario || 0).toFixed(2);
            tbody.append('<tr><td><strong>' + item.producto_nombre + '</strong></td><td>' + (item.material_nombre || '-') + '</td><td>' + (item.categoria_nombre || '-') + '</td><td>' + item.cantidad + ' ' + item.unidad + '</td><td>$' + precio + '</td><td><button type="button" class="btn btn-sm btn-primary" onclick="seleccionarInventario(' + item.inventario_id + ')"><i class="fa fa-check"></i> Seleccionar</button></td></tr>');
          });
        }

        $('#filtroInventario').on('keyup', function() {
          var valor = $(this).val().toLowerCase();
          var datosFiltrados = inventarioData.filter(function(item) {
            return (item.producto_nombre && item.producto_nombre.toLowerCase().includes(valor)) ||
                   (item.material_nombre && item.material_nombre.toLowerCase().includes(valor)) ||
                   (item.categoria_nombre && item.categoria_nombre.toLowerCase().includes(valor));
          });
          renderizarTablaInventario(datosFiltrados);
        });

        window.seleccionarInventario = function(id) {
          var item = inventarioData.find(function(i) { return i.inventario_id == id; });
          if (item) {
            $('#inventario_id').val(item.inventario_id);
            $('#producto_seleccionado').val(item.producto_nombre);
            $('#inventario_id').data('producto-id', item.producto_id);
            $('#inventario_id').data('precio-id', item.precio_id);
            $('#inventario_id').data('cantidad', item.cantidad);
            $('#precio_unitario').val(item.precio_unitario || 0);
            $('#stockDisponible').text('Stock disponible: ' + item.cantidad + ' ' + item.unidad);
            calcularTotal();
            $('#modalBuscarInventario').modal('hide');
          }
        };

        $('#modalNuevaVenta').on('show.bs.modal', function() {
          cargarSiguienteNumeroFactura();
          // Resetear fecha a hoy al abrir
          $('#fecha_venta').val(new Date().toISOString().split('T')[0]);
        });

        $('#modalNuevaVenta').on('hidden.bs.modal', function() {
           limpiarSeleccionProducto();
        });

        // Función para cargar el siguiente número de factura
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
              } else {
                $('#numero_factura').val('00001');
              }
            },
            error: function() {
              $('#numero_factura').val('00001');
            }
          });
        }
        
        // Cargar ventas
        function cargarVentas() {
          $.ajax({
            url: 'api.php?action=listar&estado=' + estadoActual,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              console.log('cargarVentas response:', response); // debug
              if (response.success) {
                table.clear();
                response.data.forEach(function(venta) {
                  var detalles = venta.detalles || [];
                  var numProductos = detalles.length;
                  
                  var badgeEstado = '';
                  if (venta.estado === 'completada') {
                    badgeEstado = '<span class="badge badge-success">Completada</span>';
                  } else if (venta.estado === 'pendiente') {
                    badgeEstado = '<span class="badge badge-warning">Pendiente</span>';
                  } else {
                    badgeEstado = '<span class="badge badge-danger">Inactiva</span>';
                  }

                  var cantTotal = detalles.reduce(function(sum, d) { return sum + parseFloat(d.cantidad || 0); }, 0);
                  
                  var rowNode = table.row.add([
                    '<i class="fa fa-plus-circle text-primary" style="font-size: 1.2rem; cursor: pointer;"></i>',
                    venta.fecha_venta,
                    venta.sucursal_nombre,
                    '<strong>' + numProductos + ' producto(s)</strong>',
                    cantTotal.toFixed(2),
                    '<strong>$' + parseFloat(venta.total).toFixed(2) + '</strong>',
                    venta.cliente_nombre,
                    badgeEstado,
                    '<a href="ver.php?id=' + venta.id + '" target="_blank" class="btn btn-link btn-success btn-sm" title="Ver Factura"><i class="fa fa-eye"></i></a> ' +
                    '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarVenta(' + venta.id + ')" title="Cancelar Venta"><i class="fa fa-times"></i></button>'
                  ]).node();

                  $(rowNode).data('venta', venta);
                  $(rowNode).find('td:first').addClass('details-control text-center');
                });
                table.draw();
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar las ventas", "error");
            }
          });
        }
        
        $('#cantidad, #precio_unitario').on('input', function() {
          calcularTotal();
        });
        
        $('#iva, #tipo_descuento, #descuento').on('change input', function() {
          calcularTotal();
        });
        
        $('#tipo_descuento').on('change', function() {
          var tipo = $(this).val();
          if (tipo === 'porcentaje') {
            $('#labelDescuento').text('Descuento (%)');
          } else {
            $('#labelDescuento').text('Descuento ($)');
          }
          calcularTotal();
        });
        
        function calcularTotal() {
          var cantidad = parseFloat($('#cantidad').val()) || 0;
          var precio = parseFloat($('#precio_unitario').val()) || 0;
          var ivaPorcentaje = parseFloat($('#iva').val()) || 0;
          var valorDescuento = parseFloat($('#descuento').val()) || 0;
          var tipoDescuento = $('#tipo_descuento').val();
          
          var subtotal = cantidad * precio;
          var ivaMonto = (subtotal * ivaPorcentaje) / 100;
          var descuentoMonto = (tipoDescuento === 'porcentaje') ? (subtotal * valorDescuento) / 100 : valorDescuento;
          var total = subtotal + ivaMonto - descuentoMonto;
          
          $('#totalVenta').text('$' + total.toFixed(2));
        }
        
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
          
          var inventarioInput = $('#inventario_id');
          var producto_id = inventarioInput.data('producto-id');
          var precio_id = inventarioInput.data('precio-id') || null;
          
          var cantidad = parseFloat($('#cantidad').val()) || 0;
          var precio_unitario = parseFloat($('#precio_unitario').val()) || 0;
          var ivaPorcentaje = parseFloat($('#iva').val()) || 0;
          var valorDescuento = parseFloat($('#descuento').val()) || 0;
          var tipoDescuento = $('#tipo_descuento').val();
          
          var subtotal = cantidad * precio_unitario;
          var iva = (subtotal * ivaPorcentaje) / 100;
          var descuento = (tipoDescuento === 'porcentaje') ? (subtotal * valorDescuento) / 100 : valorDescuento;
          var total = subtotal + iva - descuento;
          
          var stockDisponible = parseFloat(inventarioInput.data('cantidad')) || 0;
          if (cantidad > stockDisponible) {
            swal("Error", "La cantidad solicitada (" + cantidad + ") excede el stock disponible (" + stockDisponible + ")", "error");
            return;
          }
          
          var formData = {
            cliente_id: $('#cliente_id').val(),
            cliente_nombre: $('#cliente_id option:selected').text(),
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
              precio_unitario: precio_unitario,
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
        
        cargarDatos();
        cargarVentas();
      });
      
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

