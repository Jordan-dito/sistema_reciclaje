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
                    <label>Fecha de Venta</label>
                    <input type="date" id="fecha_venta" name="fecha_venta" class="form-control" readonly style="background-color: #e9ecef; cursor: not-allowed;">
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

                <!-- Sección de productos múltiples -->
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Productos <span class="text-danger">*</span></label>
                    <button type="button" class="btn btn-primary mb-3" id="btnBuscarInventario">
                      <i class="fa fa-plus"></i> Agregar Producto
                    </button>
                    <small class="form-text text-muted d-block mb-2"><i class="fa fa-info-circle"></i> Seleccione la sucursal primero. Puede agregar múltiples productos del inventario.</small>

                    <div id="productosVentaAgregados" class="card border-primary" style="display: none;">
                      <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-shopping-cart"></i> Productos seleccionados (<span id="contadorProductosVenta">0</span>)</span>
                        <button type="button" class="btn btn-sm btn-danger" id="btnLimpiarTodosVenta" style="display: none;">
                          <i class="fa fa-trash"></i> Limpiar Todo
                        </button>
                      </div>
                      <div class="card-body" style="padding: 15px;">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                          <table class="table table-sm table-hover table-bordered" id="tablaProductosVenta" style="margin-bottom: 0;">
                            <thead class="thead-light" style="position: sticky; top: 0; background: white; z-index: 10;">
                              <tr>
                                <th style="width: 30px;">#</th>
                                <th>Producto</th>
                                <th>Material</th>
                                <th>Unidad</th>
                                <th style="width: 120px;">Cantidad</th>
                                <th style="width: 130px;">Precio Unitario</th>
                                <th style="width: 120px;">Subtotal</th>
                                <th style="width: 60px;">Acción</th>
                              </tr>
                            </thead>
                            <tbody id="tbodyProductosVenta">
                            </tbody>
                            <tfoot class="table-info" style="position: sticky; bottom: 0; background: #d1ecf1; z-index: 10;">
                              <tr>
                                <th colspan="6" class="text-end"><strong>Subtotal Productos:</strong></th>
                                <th id="subtotalProductosVenta" style="font-size: 1.1em;">$0.00</th>
                                <th></th>
                              </tr>
                            </tfoot>
                          </table>
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
                    <label>Descuento (%)</label>
                    <input type="number" step="0.01" id="descuento" name="descuento" class="form-control" placeholder="0" value="0" min="0" max="100">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Método de Pago</label>
                    <select id="metodo_pago" name="metodo_pago" class="form-control" style="background-color: #e9ecef; pointer-events: none;" tabindex="-1">
                      <option value="efectivo" selected>Efectivo</option>
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

    <!-- Modal Buscar Inventario -->
    <div class="modal fade" id="modalBuscarInventario" tabindex="-1" aria-hidden="true">
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
                    <th style="width: 40px;"><input type="checkbox" id="checkTodosInventario"></th>
                    <th>Código</th>
                    <th>Material</th>
                    <th>Categoría</th>
                    <th>Unidad</th>
                    <th>Stock</th>
                    <th>Precio</th>
                  </tr>
                </thead>
                <tbody>
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
            <span id="contadorSeleccionadosInventario" class="text-muted me-auto">0 seleccionados</span>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="btnAgregarSeleccionados">
              <i class="fa fa-plus"></i> Agregar Seleccionados
            </button>
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
        var productosVentaSeleccionados = [];

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
          productosVentaSeleccionados = [];
          renderizarProductosVenta();
          calcularTotal();
        });

        function cargarInventarioModal(sucursal_id) {
          var tbody = $('#tablaInventario tbody');
          var mensajeSin = $('#mensajeSinInventario');
          var mensajeCargando = $('#mensajeCargandoInventario');
          
          tbody.empty();
          mensajeSin.hide();
          mensajeCargando.show();
          $('#checkTodosInventario').prop('checked', false);
          
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
            var yaAgregado = productosVentaSeleccionados.some(function(p) { return p.inventario_id == item.inventario_id; });
            var checkboxClass = yaAgregado ? 'disabled' : '';
            var rowClass = yaAgregado ? 'table-success' : '';
            tbody.append(
              '<tr class="' + rowClass + '">' +
              '<td class="text-center"><input type="checkbox" class="check-inventario" data-id="' + item.inventario_id + '" ' + (yaAgregado ? 'disabled checked' : '') + '></td>' +
              '<td><strong>' + item.producto_nombre + '</strong></td>' +
              '<td>' + (item.material_nombre || '-') + '</td>' +
              '<td>' + (item.categoria_nombre || '-') + '</td>' +
              '<td>' + (item.unidad || '-') + '</td>' +
              '<td>' + item.cantidad + '</td>' +
              '<td>$' + precio + '</td>' +
              '</tr>'
            );
          });
          actualizarContadorSeleccionados();
        }

        function actualizarContadorSeleccionados() {
          var count = $('#tablaInventario .check-inventario:checked:not(:disabled)').length;
          $('#contadorSeleccionadosInventario').text(count + ' seleccionados');
        }

        $('#tablaInventario').on('change', '.check-inventario', function() {
          actualizarContadorSeleccionados();
          var allChecks = $('#tablaInventario .check-inventario:not(:disabled)');
          var allChecked = allChecks.length > 0 && allChecks.filter(':checked').length === allChecks.length;
          $('#checkTodosInventario').prop('checked', allChecked);
        });

        $('#checkTodosInventario').change(function() {
          var checked = $(this).is(':checked');
          $('#tablaInventario .check-inventario:not(:disabled)').prop('checked', checked);
          actualizarContadorSeleccionados();
        });

        $('#btnAgregarSeleccionados').click(function() {
          var checksSeleccionados = $('#tablaInventario .check-inventario:checked:not(:disabled)');
          if (checksSeleccionados.length === 0) {
            swal("Atención", "Seleccione al menos un producto", "warning");
            return;
          }
          checksSeleccionados.each(function() {
            var invId = $(this).data('id');
            var item = inventarioData.find(function(i) { return i.inventario_id == invId; });
            if (item) {
              productosVentaSeleccionados.push({
                inventario_id: item.inventario_id,
                producto_id: item.producto_id,
                precio_id: item.precio_id,
                nombre: item.producto_nombre,
                material: item.material_nombre || '',
                categoria: item.categoria_nombre || '',
                unidad: item.unidad || '',
                stock: parseFloat(item.cantidad),
                precio: parseFloat(item.precio_unitario) || 0,
                cantidad: 0,
                subtotal: 0
              });
            }
          });
          renderizarProductosVenta();
          calcularTotal();
          $('#modalBuscarInventario').modal('hide');
        });

        $('#filtroInventario').on('keyup', function() {
          var valor = $(this).val().toLowerCase();
          var datosFiltrados = inventarioData.filter(function(item) {
            return (item.producto_nombre && item.producto_nombre.toLowerCase().includes(valor)) ||
                   (item.material_nombre && item.material_nombre.toLowerCase().includes(valor)) ||
                   (item.categoria_nombre && item.categoria_nombre.toLowerCase().includes(valor));
          });
          renderizarTablaInventario(datosFiltrados);
        });

        function renderizarProductosVenta() {
          var tbody = $('#tbodyProductosVenta');
          tbody.empty();

          if (productosVentaSeleccionados.length === 0) {
            $('#productosVentaAgregados').hide();
            $('#btnLimpiarTodosVenta').hide();
            $('#contadorProductosVenta').text('0');
            calcularTotal();
            return;
          }

          $('#productosVentaAgregados').fadeIn(300);
          $('#btnLimpiarTodosVenta').show();
          $('#contadorProductosVenta').text(productosVentaSeleccionados.length);

          productosVentaSeleccionados.forEach(function(producto, index) {
            var fila = $('<tr>').attr('data-index', index);

            fila.append($('<td>').html('<strong>' + (index + 1) + '</strong>'));
            fila.append($('<td>').html('<strong>' + producto.nombre + '</strong><br><small class="text-muted">' + producto.categoria + '</small>'));
            fila.append($('<td>').text(producto.material || '-'));
            fila.append($('<td>').text(producto.unidad || '-'));

            var cantidadInput = $('<input>')
              .attr('type', 'number')
              .attr('step', '0.01')
              .attr('min', '0')
              .attr('max', producto.stock)
              .addClass('form-control form-control-sm')
              .val(producto.cantidad)
              .on('change input', function() {
                var nuevaCantidad = parseFloat($(this).val()) || 0;
                if (nuevaCantidad > producto.stock) {
                  nuevaCantidad = producto.stock;
                  $(this).val(nuevaCantidad);
                  swal("Atención", "Stock máximo: " + producto.stock + " " + producto.unidad, "warning");
                }
                producto.cantidad = nuevaCantidad;
                producto.subtotal = producto.cantidad * producto.precio;
                actualizarFilaVenta(index);
                calcularTotal();
              });
            fila.append($('<td>').append(cantidadInput).append('<small class="text-muted">Stock: ' + producto.stock + '</small>'));

            var precioInput = $('<input>')
              .attr('type', 'number')
              .attr('step', '0.01')
              .attr('min', '0')
              .attr('readonly', true)
              .css({ 'background-color': '#f8f9fa', 'cursor': 'not-allowed' })
              .addClass('form-control form-control-sm')
              .val(producto.precio.toFixed(2));
            fila.append($('<td>').append(precioInput));

            fila.append($('<td>').html('<strong>$' + producto.subtotal.toFixed(2) + '</strong>'));

            var btnEliminar = $('<button>')
              .addClass('btn btn-sm btn-danger')
              .html('<i class="fa fa-times"></i>')
              .on('click', function() { eliminarProductoVenta(index); });
            fila.append($('<td>').append(btnEliminar));

            tbody.append(fila);
          });
        }

        function actualizarFilaVenta(index) {
          var producto = productosVentaSeleccionados[index];
          if (!producto) return;
          var fila = $('#tbodyProductosVenta tr[data-index="' + index + '"]');
          fila.find('td:eq(6)').html('<strong>$' + producto.subtotal.toFixed(2) + '</strong>');
        }

        function eliminarProductoVenta(index) {
          var producto = productosVentaSeleccionados[index];
          swal({
            title: "¿Eliminar producto?",
            text: "¿Desea quitar \"" + producto.nombre + "\" de la lista?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          }).then(function(willDelete) {
            if (willDelete) {
              productosVentaSeleccionados.splice(index, 1);
              renderizarProductosVenta();
              calcularTotal();
            }
          });
        }

        $('#btnLimpiarTodosVenta').click(function() {
          swal({
            title: "¿Limpiar todos?",
            text: "Se quitarán todos los productos de la lista",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          }).then(function(willDelete) {
            if (willDelete) {
              productosVentaSeleccionados = [];
              renderizarProductosVenta();
              calcularTotal();
            }
          });
        });

        $('#modalNuevaVenta').on('show.bs.modal', function() {
          cargarSiguienteNumeroFactura();
          $('#fecha_venta').val(new Date().toISOString().split('T')[0]);
        });

        $('#modalNuevaVenta').on('hidden.bs.modal', function() {
          productosVentaSeleccionados = [];
          renderizarProductosVenta();
          calcularTotal();
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
        
        $('#iva, #descuento').on('input', function() {
          calcularTotal();
        });
        
        function calcularTotal() {
          var subtotalProductos = 0;
          productosVentaSeleccionados.forEach(function(p) {
            subtotalProductos += p.subtotal;
          });
          $('#subtotalProductosVenta').text('$' + subtotalProductos.toFixed(2));

          var iva = parseFloat($('#iva').val()) || 0;
          var descuentoPct = parseFloat($('#descuento').val()) || 0;
          if (descuentoPct > 100) descuentoPct = 100;
          var descuentoMonto = (subtotalProductos * descuentoPct) / 100;
          var total = subtotalProductos + iva - descuentoMonto;
          $('#totalVenta').text('$' + total.toFixed(2));
        }
        
        var guardandoVenta = false;
        $('#btnGuardarVenta').click(function() {
          if (guardandoVenta) return;

          if (!$('#sucursal_id').val()) {
            swal("Error", "Debe seleccionar una sucursal", "error");
            return;
          }
          if (!$('#cliente_id').val()) {
            swal("Error", "Debe seleccionar un cliente", "error");
            return;
          }
          if (!$('#fecha_venta').val()) {
            swal("Error", "Debe ingresar la fecha de venta", "error");
            return;
          }

          if (productosVentaSeleccionados.length === 0) {
            swal("Error", "Debe agregar al menos un producto", "error");
            return;
          }

          var hayError = false;
          productosVentaSeleccionados.forEach(function(p) {
            if (p.cantidad <= 0) {
              hayError = true;
            }
            if (p.cantidad > p.stock) {
              hayError = true;
            }
          });
          if (hayError) {
            swal("Error", "Verifique las cantidades de los productos. Deben ser mayores a 0 y no exceder el stock disponible.", "error");
            return;
          }

          var subtotalProductos = 0;
          var detalles = [];
          productosVentaSeleccionados.forEach(function(p) {
            subtotalProductos += p.subtotal;
            detalles.push({
              inventario_id: p.inventario_id,
              producto_id: p.producto_id,
              precio_id: p.precio_id,
              cantidad: p.cantidad,
              precio_unitario: p.precio,
              subtotal: p.subtotal
            });
          });

          var iva = parseFloat($('#iva').val()) || 0;
          var descuentoPct = parseFloat($('#descuento').val()) || 0;
          if (descuentoPct > 100) descuentoPct = 100;
          var descuento = (subtotalProductos * descuentoPct) / 100;
          var total = subtotalProductos + iva - descuento;
          
          var formData = {
            cliente_id: $('#cliente_id').val(),
            cliente_nombre: $('#cliente_id option:selected').text(),
            sucursal_id: $('#sucursal_id').val(),
            fecha_venta: $('#fecha_venta').val(),
            numero_factura: $('#numero_factura').val(),
            tipo_comprobante: $('#tipo_comprobante').val(),
            subtotal: subtotalProductos,
            iva: iva,
            descuento: descuento,
            total: total,
            metodo_pago: $('#metodo_pago').val(),
            estado: $('#estado').val(),
            notas: $('#notas').val(),
            detalles: JSON.stringify(detalles),
            action: 'crear'
          };
          
          guardandoVenta = true;
          var btn = $('#btnGuardarVenta');
          btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

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
                productosVentaSeleccionados = [];
                renderizarProductosVenta();
                calcularTotal();
                cargarVentas();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar la venta';
              swal("Error", error, "error");
            },
            complete: function() {
              guardandoVenta = false;
              btn.prop('disabled', false).html('Registrar Venta');
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

