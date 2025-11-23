<?php
/**
 * Ver Detalle de Compra / Factura
 * Sistema de Gestión de Reciclaje
 */

// Verificar autenticación
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../index.php');
    exit;
}

// Obtener ID de la compra
$compra_id = $_GET['id'] ?? 0;
if (!$compra_id) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Factura de Compra #<?php echo $compra_id; ?> - Sistema de Reciclaje</title>
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
    <style>
      @media print {
        .no-print { display: none !important; }
        body { background: white; }
        .card { border: none; box-shadow: none; }
      }
      .factura-header {
        border-bottom: 3px solid #007bff;
        padding-bottom: 20px;
        margin-bottom: 30px;
      }
      .factura-body {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
      }
    </style>
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
          ?>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4 no-print">
              <div>
                <h3 class="fw-bold mb-3">Factura de Compra</h3>
                <h6 class="op-7 mb-2">Detalle completo de la compra</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" onclick="window.print()">
                  <i class="fa fa-print"></i> Imprimir
                </button>
                <a href="index.php" class="btn btn-secondary btn-round">
                  <i class="fa fa-arrow-left"></i> Volver
                </a>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-body factura-body">
                    <!-- Encabezado de Factura -->
                    <div class="factura-header">
                      <div class="row">
                        <div class="col-md-6">
                          <h2 class="text-primary mb-0">FACTURA DE COMPRA</h2>
                          <p class="text-muted mb-0">Sistema de Gestión de Reciclaje</p>
                        </div>
                        <div class="col-md-6 text-end">
                          <p class="mb-1"><strong>N° Factura:</strong> <span id="numeroFactura">-</span></p>
                          <p class="mb-1"><strong>Fecha:</strong> <span id="fechaCompra">-</span></p>
                          <p class="mb-0"><strong>Estado:</strong> <span id="estadoCompra">-</span></p>
                        </div>
                      </div>
                    </div>

                    <!-- Información de Proveedor y Sucursal -->
                    <div class="row mb-4">
                      <div class="col-md-6">
                        <h5 class="text-muted mb-3">Proveedor</h5>
                        <p class="mb-1"><strong id="proveedorNombre">-</strong></p>
                      </div>
                      <div class="col-md-6">
                        <h5 class="text-muted mb-3">Sucursal</h5>
                        <p class="mb-1"><strong id="sucursalNombre">-</strong></p>
                      </div>
                    </div>

                    <!-- Tabla de Productos -->
                    <div class="table-responsive mb-4">
                      <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                          <tr>
                            <th style="width: 40px;">#</th>
                            <th>Producto</th>
                            <th>Material</th>
                            <th>Categoría</th>
                            <th class="text-end">Cantidad</th>
                            <th>Unidad</th>
                            <th class="text-end">Precio Unitario</th>
                            <th class="text-end">Subtotal</th>
                          </tr>
                        </thead>
                        <tbody id="productosBody">
                          <tr>
                            <td colspan="8" class="text-center">
                              <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                        <tfoot class="table-info">
                          <tr>
                            <th colspan="7" class="text-end">Subtotal Productos:</th>
                            <th class="text-end" id="subtotalProductos">$0.00</th>
                          </tr>
                          <tr>
                            <th colspan="7" class="text-end">IVA:</th>
                            <th class="text-end" id="ivaTotal">$0.00</th>
                          </tr>
                          <tr>
                            <th colspan="7" class="text-end">Descuento:</th>
                            <th class="text-end" id="descuentoTotal">$0.00</th>
                          </tr>
                          <tr class="table-primary">
                            <th colspan="7" class="text-end"><h4 class="mb-0">TOTAL:</h4></th>
                            <th class="text-end"><h4 class="mb-0" id="totalFinal">$0.00</h4></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>

                    <!-- Notas -->
                    <div id="notasContainer" style="display: none;">
                      <h5 class="text-muted mb-2">Notas</h5>
                      <div class="alert alert-light">
                        <p id="notasTexto" class="mb-0">-</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <footer class="footer no-print">
          <?php include __DIR__ . '/../includes/footer.php'; ?>
        </footer>
      </div>
    </div>

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/setting-demo.js"></script>
    <script>
      $(document).ready(function() {
        var compraId = <?php echo $compra_id; ?>;
        
        // Cargar datos de la compra
        $.ajax({
          url: 'api.php?action=obtener&id=' + compraId,
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var compra = response.data;
              var detalles = compra.detalles || [];
              
              // Llenar información general
              $('#numeroFactura').text(compra.numero_factura || 'N/A');
              $('#fechaCompra').text(compra.fecha_compra || '-');
              $('#proveedorNombre').text(compra.proveedor_nombre || '-');
              $('#sucursalNombre').text(compra.sucursal_nombre || '-');
              
              // Estado
              var badgeEstado = '';
              if (compra.estado === 'completada') {
                badgeEstado = '<span class="badge badge-success">Completada</span>';
              } else if (compra.estado === 'pendiente') {
                badgeEstado = '<span class="badge badge-warning">Pendiente</span>';
              } else {
                badgeEstado = '<span class="badge badge-danger">Cancelada</span>';
              }
              $('#estadoCompra').html(badgeEstado);
              
              // Llenar tabla de productos
              var tbody = $('#productosBody');
              tbody.empty();
              
              if (detalles.length === 0) {
                tbody.html('<tr><td colspan="8" class="text-center text-muted">No hay productos registrados</td></tr>');
              } else {
                var totalProductos = 0;
                detalles.forEach(function(detalle, index) {
                  var subtotal = parseFloat(detalle.subtotal || (detalle.cantidad * (detalle.precio_unitario || 0)) || 0);
                  totalProductos += subtotal;
                  
                  var fila = $('<tr>');
                  fila.append($('<td>').text(index + 1));
                  fila.append($('<td>').html('<strong>' + (detalle.producto_nombre || '-') + '</strong>'));
                  fila.append($('<td>').text(detalle.material_nombre || '-'));
                  fila.append($('<td>').text(detalle.categoria_nombre || '-'));
                  fila.append($('<td>').addClass('text-end').text(parseFloat(detalle.cantidad || 0).toFixed(2)));
                  fila.append($('<td>').text(detalle.unidad_simbolo || detalle.unidad_nombre || '-'));
                  fila.append($('<td>').addClass('text-end').text('$' + parseFloat(detalle.precio_unitario || 0).toFixed(2)));
                  fila.append($('<td>').addClass('text-end').html('<strong>$' + subtotal.toFixed(2) + '</strong>'));
                  
                  tbody.append(fila);
                });
                
                $('#subtotalProductos').text('$' + totalProductos.toFixed(2));
              }
              
              // Totales
              $('#ivaTotal').text('$' + parseFloat(compra.iva || 0).toFixed(2));
              $('#descuentoTotal').text('$' + parseFloat(compra.descuento || 0).toFixed(2));
              $('#totalFinal').text('$' + parseFloat(compra.total || 0).toFixed(2));
              
              // Notas
              if (compra.notas && compra.notas.trim() !== '') {
                $('#notasTexto').text(compra.notas);
                $('#notasContainer').show();
              }
            } else {
              alert('No se pudo cargar la información de la compra');
            }
          },
          error: function() {
            alert('Error al cargar la información de la compra');
          }
        });
      });
    </script>
  </body>
</html>

