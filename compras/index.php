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

    <!-- Modal Ver Compra -->
    <div class="modal fade" id="modalVerCompra" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detalle de Compra #<span id="compraIdModal">-</span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Información general -->
            <div class="row mb-4">
              <div class="col-md-6">
                <h6 class="text-muted mb-3">Información General</h6>
                <p><strong>Fecha:</strong> <span id="compraFecha">-</span></p>
                <p><strong>Sucursal:</strong> <span id="compraSucursal">-</span></p>
                <p><strong>Proveedor:</strong> <span id="compraProveedor">-</span></p>
                <p><strong>Número de Factura:</strong> <span id="compraNumeroFactura">-</span></p>
              </div>
              <div class="col-md-6">
                <h6 class="text-muted mb-3">Totales</h6>
                <p><strong>Subtotal:</strong> <span id="compraSubtotal">$0.00</span></p>
                <p><strong>IVA:</strong> <span id="compraIva">$0.00</span></p>
                <p><strong>Descuento:</strong> <span id="compraDescuento">$0.00</span></p>
                <p><strong>Total:</strong> <span id="compraTotal" class="h5 text-primary">$0.00</span></p>
                <p><strong>Estado:</strong> <span id="compraEstado">-</span></p>
              </div>
            </div>
            
            <hr>
            
            <!-- Desglose de productos -->
            <h6 class="text-muted mb-3">Productos de la Compra</h6>
            <div class="table-responsive">
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Material</th>
                    <th>Categoría</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody id="compraDetallesBody">
                  <tr>
                    <td colspan="8" class="text-center">
                      <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                      </div>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr class="table-info">
                    <th colspan="7" class="text-end">Total:</th>
                    <th id="compraTotalProductos">$0.00</th>
                  </tr>
                </tfoot>
              </table>
            </div>
            
            <!-- Notas -->
            <div class="row mt-3" id="compraNotasContainer" style="display: none;">
              <div class="col-md-12">
                <h6 class="text-muted mb-2">Notas</h6>
                <div class="alert alert-light">
                  <p id="compraNotas" class="mb-0">-</p>
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
                  var detalles = compra.detalles || [];
                  var numProductos = detalles.length;
                  
                  var badgeEstado = '';
                  if (compra.estado === 'completada') {
                    badgeEstado = '<span class="badge badge-success">Completada</span>';
                  } else if (compra.estado === 'pendiente') {
                    badgeEstado = '<span class="badge badge-warning">Pendiente</span>';
                  } else {
                    badgeEstado = '<span class="badge badge-danger">Cancelada</span>';
                  }
                  
                  // Mostrar información de productos
                  var productosInfo = '';
                  if (numProductos > 0) {
                    if (numProductos === 1) {
                      var detalle = detalles[0];
                      productosInfo = '<strong>' + (detalle.producto_nombre || '-') + '</strong>';
                    } else {
                      productosInfo = '<strong>' + numProductos + ' productos</strong><br><small class="text-muted">Ver detalles</small>';
                    }
                  } else {
                    productosInfo = '<span class="text-muted">Sin productos</span>';
                  }
                  
                  table.row.add([
                    compra.id,
                    compra.fecha_compra,
                    compra.sucursal_nombre,
                    productosInfo,
                    numProductos > 0 ? detalles.reduce(function(sum, d) { return sum + parseFloat(d.cantidad || 0); }, 0).toFixed(2) : '-',
                    numProductos > 0 ? (detalles[0].unidad_simbolo || detalles[0].unidad || '-') : '-',
                    '$' + parseFloat(compra.subtotal || compra.total || 0).toFixed(2),
                    '$' + parseFloat(compra.total).toFixed(2),
                    compra.proveedor_nombre,
                    badgeEstado,
                      '<a href="ver.php?id=' + compra.id + '" class="btn btn-link btn-primary btn-sm" title="Ver factura"><i class="fa fa-eye"></i></a> ' +
                      '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarCompra(' + compra.id + ')" title="Eliminar"><i class="fa fa-times"></i></button>'
                  ]);
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

