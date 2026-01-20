<?php
/**
 * Ver Detalle de Venta / Factura
 * Sistema de Gestión de Reciclaje
 */

// Verificar autenticación
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../index.php');
    exit;
}

// Obtener ID de la venta
$venta_id = $_GET['id'] ?? 0;
if (!$venta_id) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Factura de Venta #<?php echo $venta_id; ?> - Sistema de Reciclaje</title>
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
        .sidebar { display: none !important; }
        .main-header { display: none !important; }
        .main-panel { width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .wrapper { padding: 0 !important; }
        .container-fluid { padding: 0 !important; max-width: 100% !important; }
        body { background: white; margin: 0 !important; padding: 0 !important; }
        .card { border: none; box-shadow: none; page-break-inside: avoid; margin: 0 !important; }
        .card-body { padding: 5px !important; }
        * {
          -webkit-print-color-adjust: exact !important;
          print-color-adjust: exact !important;
          color-adjust: exact !important;
        }
        .table-responsive { overflow: visible !important; }
        table { font-size: 9px !important; width: 100% !important; margin: 0 !important; }
        th, td { padding: 2px !important; line-height: 1.2 !important; }
        table th:nth-child(1) { width: 20px !important; }
        table th:nth-child(2) { width: 30% !important; }
        table th:nth-child(3) { width: 15% !important; }
        table th:nth-child(4) { width: 10% !important; }
        table th:nth-child(5) { width: 20% !important; }
        table th:nth-child(6) { width: 15% !important; }
        .factura-header { margin-bottom: 15px !important; padding-bottom: 15px !important; font-size: 12px !important; }
        .factura-header h1 { font-size: 18px !important; margin: 3px 0 !important; }
        .factura-header .row { align-items: flex-start !important; }
        .factura-header .col-md-7 { display: flex !important; align-items: center !important; }
        .factura-header .col-md-5 { display: block !important; text-align: right !important; margin-top: -80px !important; }
        .factura-header .col-md-5 p { margin-bottom: 3px !important; line-height: 1.4 !important; }
        .factura-body { padding: 5px !important; margin: 0 !important; }
        .row { margin: 0 !important; }
        .col-md-6 { padding: 3px !important; font-size: 10px !important; display: inline-block !important; width: 49% !important; vertical-align: top !important; }
        h5, h6 { margin: 3px 0 !important; font-size: 11px !important; }
        .mb-4, .mb-3, .mb-2 { margin-bottom: 5px !important; }
        tfoot th { font-size: 9px !important; padding: 2px !important; }
        tfoot { line-height: 1.1 !important; }
        html { height: 100%; }
        body { transform: scale(0.85); transform-origin: top left; width: 118%; height: 118%; }
        @page { size: landscape; margin: 2mm; }
      }
      .factura-header {
        border-bottom: 3px solid #28a745;
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
          ?>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4 no-print">
              <div>
                <h3 class="fw-bold mb-3">Factura de Venta</h3>
                <h6 class="op-7 mb-2">Detalle completo de la venta</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-success btn-round" onclick="window.print()">
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
                        <div class="col-md-7 d-flex align-items-center">
                          <img src="../assets/img/logo.jpg" alt="Logo Recicladora" style="max-width: 100px; height: auto; margin-right: 20px;">
                          <div>
                            <h2 class="text-success mb-0">COMPROBANTE DE FACTURA</h2>
                            <p class="text-muted mb-0">HNOSYÁNEZ S.A.</p>
                          </div>
                        </div>
                        <div class="col-md-5 text-end">
                          <p class="mb-1"><strong>N° Factura:</strong> <span id="numeroFactura">-</span></p>
                          <p class="mb-1"><strong>Fecha:</strong> <span id="fechaVenta">-</span></p>
                          <p class="mb-0"><strong>Estado:</strong> <span id="estadoVenta">-</span></p>
                        </div>
                      </div>
                    </div>

                    <!-- Información de Cliente y Sucursal -->
                    <div class="row mb-4">
                      <div class="col-md-6">
                        <h5 class="text-muted mb-3">Cliente</h5>
                        <p class="mb-1"><strong id="clienteNombre">-</strong></p>
                      </div>
                      <div class="col-md-6 text-end">
                        <h5 class="text-muted mb-3">Sucursal</h5>
                        <p class="mb-1"><strong id="sucursalNombre">-</strong></p>
                      </div>
                    </div>

                    <!-- Tabla de Productos -->
                    <div class="table-responsive mb-4">
                      <table class="table table-bordered table-striped">
                        <thead class="bg-success text-white">
                          <tr>
                            <th style="width: 40px;">#</th>
                            <th>Producto</th>
                            <th class="text-end">Cantidad</th>
                            <th>Unidad</th>
                            <th class="text-end">Precio Unitario</th>
                            <th class="text-end">Subtotal</th>
                          </tr>
                        </thead>
                        <tbody id="productosBody">
                          <tr>
                            <td colspan="6" class="text-center">
                              <div class="spinner-border text-success" role="status">
                                <span class="sr-only">Cargando...</span>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                        <tfoot class="table-success">
                          <tr>
                            <th colspan="5" class="text-end">Subtotal Productos:</th>
                            <th class="text-end" id="subtotalProductos">$0.00</th>
                          </tr>
                          <tr>
                            <th colspan="5" class="text-end">IVA:</th>
                            <th class="text-end" id="ivaTotal">$0.00</th>
                          </tr>
                          <tr>
                            <th colspan="5" class="text-end">Descuento:</th>
                            <th class="text-end" id="descuentoTotal">$0.00</th>
                          </tr>
                          <tr class="bg-success text-white">
                            <th colspan="5" class="text-end"><h4 class="mb-0">TOTAL:</h4></th>
                            <th class="text-end"><h4 class="mb-0" id="totalFinal">$0.00</h4></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>

                    <!-- Notas -->
                    <div id="notasContainer" style="display: none;">
                      <h5 class="text-muted mb-2">Notas</h5>
                      <div class="alert alert-light border">
                        <p id="notasTexto" class="mb-0">-</p>
                      </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <p class="text-muted small">
                                <strong>Método de Pago:</strong> <span id="metodoPago">-</span> | 
                                <strong>Tipo Comprobante:</strong> <span id="tipoComprobante">-</span>
                            </p>
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
        
        <!-- Modales Globales -->
        <?php 
          include __DIR__ . '/../includes/modal-foto-perfil.php';
          include __DIR__ . '/../includes/modal-cambiar-password.php';
        ?>
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
        var ventaId = <?php echo $venta_id; ?>;
        
        // Cargar datos de la venta
        $.ajax({
          url: 'api.php?action=obtener&id=' + ventaId,
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var venta = response.data;
              var detalles = venta.detalles || [];
              
              // Llenar información general
              var numFactura = venta.numero_factura && venta.numero_factura.trim() !== "" ? venta.numero_factura : "V-" + venta.id;
              $('#numeroFactura').text(numFactura);
              $('#fechaVenta').text(venta.fecha_venta || '-');
              $('#clienteNombre').text(venta.cliente_nombre || 'Cliente General');
              $('#sucursalNombre').text(venta.sucursal_nombre || '-');
              $('#metodoPago').text(venta.metodo_pago || '-');
              $('#tipoComprobante').text(venta.tipo_comprobante || '-');
              
              // Estado
              var badgeEstado = '';
              if (venta.estado === 'completada') {
                badgeEstado = '<span class="badge badge-success">Completada</span>';
              } else if (venta.estado === 'pendiente') {
                badgeEstado = '<span class="badge badge-warning">Pendiente</span>';
              } else {
                badgeEstado = '<span class="badge badge-danger">Cancelada</span>';
              }
              $('#estadoVenta').html(badgeEstado);
              
              // Llenar tabla de productos
              var tbody = $('#productosBody');
              tbody.empty();
              
              if (detalles.length === 0) {
                tbody.html('<tr><td colspan="6" class="text-center text-muted">No hay productos registrados</td></tr>');
              } else {
                var totalProductos = 0;
                detalles.forEach(function(detalle, index) {
                  var cantidad = parseFloat(detalle.cantidad || 0);
                  var precio = parseFloat(detalle.precio_unitario || 0);
                  // Si el precio_unitario es 0 o no viene, intentamos calcularlo del subtotal
                  if (precio === 0 && detalle.subtotal && cantidad > 0) {
                      precio = parseFloat(detalle.subtotal) / cantidad;
                  }
                  var subtotal = parseFloat(detalle.subtotal || (cantidad * precio) || 0);
                  totalProductos += subtotal;
                  
                  var fila = $('<tr>');
                  fila.append($('<td>').text(index + 1));
                  fila.append($('<td>').html('<strong>' + (detalle.producto_nombre || '-') + '</strong><br><small class="text-muted">' + (detalle.material_nombre || '') + ' - ' + (detalle.categoria_nombre || '') + '</small>'));
                  fila.append($('<td>').addClass('text-end').text(cantidad.toFixed(2)));
                  fila.append($('<td>').text(detalle.unidad_simbolo || detalle.unidad_nombre || '-'));
                  fila.append($('<td>').addClass('text-end').text('$' + precio.toFixed(2)));
                  fila.append($('<td>').addClass('text-end').html('<strong>$' + subtotal.toFixed(2) + '</strong>'));
                  
                  tbody.append(fila);
                });
                
                $('#subtotalProductos').text('$' + totalProductos.toFixed(2));
              }
              
              // Totales
              $('#ivaTotal').text('$' + parseFloat(venta.iva || 0).toFixed(2));
              $('#descuentoTotal').text('$' + parseFloat(venta.descuento || 0).toFixed(2));
              $('#totalFinal').text('$' + parseFloat(venta.total || 0).toFixed(2));
              
              // Notas
              if (venta.notas && venta.notas.trim() !== '') {
                $('#notasTexto').text(venta.notas);
                $('#notasContainer').show();
              }
            } else {
              alert('No se pudo cargar la información de la venta');
            }
          },
          error: function() {
            alert('Error al cargar la información de la venta');
          }
        });
      });
    </script>
  </body>
</html>
