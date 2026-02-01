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
          ?>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Registro de Compras</h3>
                <h6 class="op-7 mb-2">Registra compras de materiales reciclables - Actualiza inventario</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <a href="nueva.php" class="btn btn-primary btn-round">
                  <i class="fa fa-plus"></i> Nueva Compra
                </a>
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
                    <!-- Filtros por estado (Tabs) -->
                    <ul class="nav nav-pills nav-secondary nav-pills-no-bd mb-3" id="pills-tab" role="tablist">
                      <!-- <li class="nav-item">
                        <a class="nav-link active" id="tab-activos" data-bs-toggle="pill" href="#pills-activos" role="tab" onclick="cambiarFiltroEstado('activos')">Activos</a>
                      </li> -->
                      <li class="nav-item">
                        <a class="nav-link active" id="tab-completadas" data-bs-toggle="pill" href="#pills-completadas" role="tab" onclick="cambiarFiltroEstado('completada')">Completadas</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="tab-pendientes" data-bs-toggle="pill" href="#pills-pendientes" role="tab" onclick="cambiarFiltroEstado('pendiente')">Pendientes</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="tab-anuladas" data-bs-toggle="pill" href="#pills-anuladas" role="tab" onclick="cambiarFiltroEstado('cancelada')">Anuladas</a>
                      </li>
                    </ul>

                    <div class="table-responsive">
                      <table id="comprasTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th style="width: 20px;"></th>
                            <th>Fecha</th>
                            <th>Sucursal</th>
                            <th>Productos</th>
                            <th>Cant. Total</th>
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

        <?php include __DIR__ . '/../includes/footer.php'; ?>
      </div>
    </div>

    <!-- Modal Buscar Producto -->
    <div class="modal fade" id="modalBuscarProducto" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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

    <!-- Modales Globales -->
    <?php 
      include __DIR__ . '/../includes/modal-foto-perfil.php';
      include __DIR__ . '/../includes/modal-cambiar-password.php';
    ?>

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
          "order": [[1, "desc"]], // Ordenar por fecha (ahora es la col 1)
          "columnDefs": [
            { "orderable": false, "targets": [0, 8] } // No ordenar botón expandir ni acciones
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
        $('#comprasTable tbody').on('click', 'td.details-control', function() {
          var tr = $(this).closest('tr');
          var row = table.row(tr);
          var icon = $(this).find('i');

          if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            icon.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-primary');
          } else {
            row.child(formatoDetalle(tr.data('compra'))).show();
            tr.addClass('shown');
            icon.removeClass('fa-plus-circle text-primary').addClass('fa-minus-circle text-danger');
          }
        });
        
        var estadoActual = 'completada';
        var fechaFiltro = '';

        window.cambiarFiltroEstado = function(nuevoEstado) {
          estadoActual = nuevoEstado;
          cargarCompras();
        };

        // Listener para el input de fecha
        $('#filtroFecha').on('change', function() {
          fechaFiltro = $(this).val();
          cargarCompras();
        });

        window.cargarCompras = cargarCompras;
        
        // Cargar compras
        function cargarCompras() {
          var url = 'api.php?action=listar&estado=' + estadoActual;
          if (fechaFiltro) {
            url += '&fecha=' + encodeURIComponent(fechaFiltro);
          }
          $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                table.clear();
                response.data.forEach(function(compra) {
                  var detalles = compra.detalles || [];
                  var numProductos = detalles.length;
                  
                  var badgeEstado = '';
                  if (compra.estado === 'completada') {
                    badgeEstado = '<span class="badge badge-success">Completada</span>';
                  } else if (compra.estado === 'pendiente') {
                    badgeEstado = '<span class="badge badge-warning">Pendiente</span>';
                  } else if (compra.estado === 'cancelada') {
                    badgeEstado = '<span class="badge badge-danger">Anulada</span>';
                  } else {
                    badgeEstado = '<span class="badge badge-danger">' + (compra.estado || 'Anulado') + '</span>';
                  }

                  var cantTotal = detalles.reduce(function(sum, d) { return sum + parseFloat(d.cantidad || 0); }, 0);
                  
                  // Crear la fila
                  var rowNode = table.row.add([
                    '<i class="fa fa-plus-circle text-primary" style="font-size: 1.2rem; cursor: pointer;"></i>',
                    compra.fecha_compra,
                    compra.sucursal_nombre,
                    '<strong>' + numProductos + ' producto(s)</strong>',
                    cantTotal.toFixed(2),
                    '<strong>$' + parseFloat(compra.total).toFixed(2) + '</strong>',
                    compra.proveedor_nombre,
                    badgeEstado,
                    '<div class="d-flex gap-1 justify-content-center">' +
                    '<a href="ver.php?id=' + compra.id + '" class="btn btn-link btn-primary btn-sm" title="Ver factura"><i class="fa fa-eye"></i></a>' +
                    (compra.estado === 'cancelada' ? '' : '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarCompra(' + compra.id + ')" title="Eliminar"><i class="fa fa-times"></i></button>') +
                    (compra.estado === 'pendiente' ? '<button class="btn btn-link btn-success btn-sm" onclick="completarCompra(' + compra.id + ')" title="Completar"><i class="fa fa-check"></i></button>' : '') +
                    '</div>'
                  ]).node();

                  // Guardamos los datos de la compra en el nodo de la fila para usarlos al expandir
                  $(rowNode).data('compra', compra);
                  // Añadimos clase a la celda del icono para el click
                  $(rowNode).find('td:first').addClass('details-control text-center');
                });
                table.draw();
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar las compras", "error");
            }
          });
        }
        
        // Cargar compras al iniciar
        cargarCompras();
      });
      
      function verCompra(id) {
        // Redirigir a la página de visualización
        window.location.href = 'ver.php?id=' + id;
      }
      
      function completarCompra(id) {
        swal({
          title: "¿Completar compra?",
          text: "La compra será marcada como completada y se actualizará el inventario",
          icon: "info",
          buttons: true,
        })
        .then((willComplete) => {
          if (willComplete) {
            $.ajax({
              url: 'api.php',
              method: 'POST',
              data: { id: id, action: 'completar' },
              dataType: 'json',
              success: function(response) {
                if (response.success) {
                  swal("¡Éxito!", "Compra completada exitosamente", "success");
                  cargarCompras();
                } else {
                  swal("Error", response.message, "error");
                }
              },
              error: function() {
                swal("Error", "No se pudo completar la compra", "error");
              }
            });
          }
        });
      }
      
      function eliminarCompra(id) {
        swal({
          title: "¿Está seguro?",
          text: "La compra será anulada",
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
                  swal("¡Éxito!", "Compra anulada exitosamente", "success");
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

