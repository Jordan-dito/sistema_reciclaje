<?php
/**
 * Módulo de Reportes
 * Sistema de Gestión de Reciclaje
 */

// Verificar autenticación
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../index.php');
    exit;
}

$currentRoute = 'reportes';
$basePath = '';
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Reportes - Sistema de Reciclaje</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="../assets/img/logo.jpg"
      type="image/jpeg"
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
              $currentRoute = 'reportes';
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
                  <h3 class="fw-bold mb-3">Reportes del Sistema</h3>
                  <h6 class="op-7 mb-2">Genera y exporta reportes en formato PDF</h6>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="card card-round">
                    <div class="card-header">
                      <div class="card-head-row">
                        <div class="card-title">Seleccionar Reporte</div>
                      </div>
                    </div>
                    <div class="card-body">
                      <form id="formReporte">
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Tipo de Reporte *</label>
                              <select id="tipo_reporte" name="tipo_reporte" class="form-control" required>
                                <option value="">Seleccione un reporte</option>
                                <option value="inventarios">Reporte de Inventarios</option>
                                <option value="compras">Reporte de Compras</option>
                                <option value="ventas">Reporte de Ventas</option>
                                <option value="productos">Reporte de Productos</option>
                                <option value="materiales">Reporte de Materiales por Categoría</option>
                                <option value="sucursales">Reporte de Sucursales</option>
                                <option value="usuarios">Reporte de Usuarios por Rol</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>Fecha Desde *</label>
                              <input type="date" id="fecha_desde" name="fecha_desde" class="form-control" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>Fecha Hasta *</label>
                              <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control" required>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label>&nbsp;</label>
                              <div>
                                <button type="button" class="btn btn-primary btn-block" id="btnGenerarReporte">
                                  <i class="fa fa-file-pdf"></i> PDF
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row" id="filtro_rol" style="display: none;">
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Filtrar por Rol</label>
                              <select id="rol_id" name="rol_id" class="form-control">
                                <option value="">Todos los roles</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <button type="button" class="btn btn-secondary" id="btnVistaPrevia">
                              <i class="fa fa-eye"></i> Vista Previa
                            </button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mt-4" id="dashboardSection" style="display: none;">
                <div class="col-md-12">
                  <div class="card card-round">
                    <div class="card-header">
                      <div class="card-head-row">
                        <div class="card-title">Dashboard de Sucursales</div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="chart-container" style="min-height: 375px; position: relative;">
                        <canvas id="sucursalesChart"></canvas>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mt-4" id="vistaPrevia" style="display: none;">
                <div class="col-md-12">
                  <div class="card card-round">
                    <div class="card-header">
                      <div class="card-head-row">
                        <div class="card-title">Vista Previa del Reporte</div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div id="contenidoVistaPrevia"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <?php include __DIR__ . '/../includes/footer.php'; ?>
        </div>
      </div>
    </div>

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/setting-demo.js"></script>
    <?php
      $basePath = '..';
      include __DIR__ . '/../includes/footer-scripts.php';
    ?>
    <script>
      $(document).ready(function() {
        // Establecer fechas por defecto (último mes)
        var hoy = new Date();
        var haceUnMes = new Date();
        haceUnMes.setMonth(haceUnMes.getMonth() - 1);
        
        $('#fecha_hasta').val(hoy.toISOString().split('T')[0]);
        $('#fecha_desde').val(haceUnMes.toISOString().split('T')[0]);
        
        // Manejar cambios en el tipo de reporte
        $('#tipo_reporte').change(function() {
          var tipo = $(this).val();
          
          // Mostrar/ocultar filtro de rol
          if (tipo === 'usuarios') {
            $('#filtro_rol').show();
            cargarRoles();
          } else {
            $('#filtro_rol').hide();
          }
          
          // Reportes que no requieren fechas
          var reportesSinFechas = ['productos', 'materiales'];
          if (reportesSinFechas.includes(tipo)) {
            $('#fecha_desde').prop('required', false).closest('.form-group').hide();
            $('#fecha_hasta').prop('required', false).closest('.form-group').hide();
          } else {
            $('#fecha_desde').prop('required', true).closest('.form-group').show();
            $('#fecha_hasta').prop('required', true).closest('.form-group').show();
          }
        });
        
        // Cargar roles disponibles
        function cargarRoles() {
          $.ajax({
            url: '../roles/api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            xhrFields: {
              withCredentials: true
            },
            crossDomain: false,
            success: function(response) {
              if (response.success) {
                var select = $('#rol_id');
                select.empty().append('<option value="">Todos los roles</option>');
                response.data.forEach(function(rol) {
                  if (rol.estado === 'activo') {
                    select.append('<option value="' + rol.id + '">' + rol.nombre + '</option>');
                  }
                });
              }
            }
          });
        }
        
        var tieneDatos = false;
        var datosReporte = [];
        var chartSucursales = null;
        
        // Inicialmente deshabilitar botones
        $('#btnGenerarReporte').prop('disabled', true);
        $('#btnVistaPrevia').prop('disabled', false);
        
        // Vista previa
        $('#btnVistaPrevia').click(function() {
          var form = $('#formReporte')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var tipo = $('#tipo_reporte').val();
          var fechaDesde = $('#fecha_desde').val() || '';
          var fechaHasta = $('#fecha_hasta').val() || '';
          var rolId = $('#rol_id').val() || '';
          
          // Validar fechas solo si son requeridas
          var reportesSinFechas = ['productos', 'materiales'];
          if (!reportesSinFechas.includes(tipo)) {
            if (!fechaDesde || !fechaHasta) {
              swal("Error", "Las fechas son obligatorias para este tipo de reporte", "error");
              return;
            }
            if (new Date(fechaDesde) > new Date(fechaHasta)) {
              swal("Error", "La fecha desde debe ser menor o igual a la fecha hasta", "error");
              return;
            }
          }
          
          // Cargar vista previa
          var dataParams = {
            action: 'vista_previa',
            tipo: tipo,
            rol_id: rolId
          };
          
          if (fechaDesde) dataParams.fecha_desde = fechaDesde;
          if (fechaHasta) dataParams.fecha_hasta = fechaHasta;
          
          $.ajax({
            url: 'api.php',
            method: 'GET',
            data: dataParams,
            dataType: 'json',
            xhrFields: {
              withCredentials: true
            },
            crossDomain: false,
            success: function(response) {
              if (response.success) {
                tieneDatos = response.tieneDatos;
                datosReporte = response.datos || [];
                
                $('#contenidoVistaPrevia').html(response.html);
                $('#vistaPrevia').show();
                
                // Habilitar/deshabilitar botones según si hay datos
                if (tieneDatos) {
                  $('#btnGenerarReporte').prop('disabled', false);
                  
                  // Si es reporte de sucursales, mostrar dashboard
                  if (tipo === 'sucursales' && datosReporte.length > 0) {
                    mostrarDashboardSucursales(datosReporte);
                  } else {
                    $('#dashboardSection').hide();
                  }
                } else {
                  $('#btnGenerarReporte').prop('disabled', true);
                  $('#dashboardSection').hide();
                  swal("Sin datos", "No hay datos para mostrar en el período seleccionado", "warning");
                }
              } else {
                swal("Error", response.message || "No se pudo generar la vista previa", "error");
                $('#btnGenerarReporte').prop('disabled', true);
                $('#dashboardSection').hide();
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al generar la vista previa';
              swal("Error", error, "error");
              $('#btnGenerarReporte').prop('disabled', true);
              $('#dashboardSection').hide();
            }
          });
        });
        
        // Generar reporte PDF (solo si hay datos)
        $('#btnGenerarReporte').click(function() {
          if (!tieneDatos) {
            swal("Sin datos", "Primero debe generar la vista previa y verificar que hay datos", "warning");
            return;
          }
          
          var form = $('#formReporte')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var tipo = $('#tipo_reporte').val();
          var fechaDesde = $('#fecha_desde').val() || '';
          var fechaHasta = $('#fecha_hasta').val() || '';
          var rolId = $('#rol_id').val() || '';
          
          // Validar fechas solo si son requeridas
          var reportesSinFechas = ['productos', 'materiales'];
          if (!reportesSinFechas.includes(tipo)) {
            if (!fechaDesde || !fechaHasta) {
              swal("Error", "Las fechas son obligatorias para este tipo de reporte", "error");
              return;
            }
            if (new Date(fechaDesde) > new Date(fechaHasta)) {
              swal("Error", "La fecha desde debe ser menor o igual a la fecha hasta", "error");
              return;
            }
          }
          
          // Construir URL para generar PDF
          var url = 'pdf.php?tipo=' + tipo;
          if (fechaDesde) url += '&fecha_desde=' + fechaDesde;
          if (fechaHasta) url += '&fecha_hasta=' + fechaHasta;
          if (rolId) url += '&rol_id=' + rolId;
          
          // Abrir en nueva ventana para descargar PDF
          window.open(url, '_blank');
        });
        
        // Función para mostrar dashboard de sucursales
        function mostrarDashboardSucursales(datos) {
          $('#dashboardSection').show();
          
          // Agrupar por dirección
          var datosPorDireccion = {};
          datos.forEach(function(sucursal) {
            var direccion = sucursal.direccion || 'Sin dirección';
            if (!datosPorDireccion[direccion]) {
              datosPorDireccion[direccion] = 0;
            }
            datosPorDireccion[direccion]++;
          });
          
          var labels = Object.keys(datosPorDireccion);
          var valores = Object.values(datosPorDireccion);
          
          // Destruir gráfico anterior si existe
          if (chartSucursales) {
            chartSucursales.destroy();
          }
          
          var ctx = document.getElementById('sucursalesChart').getContext('2d');
          chartSucursales = new Chart(ctx, {
            type: 'line',
            data: {
              labels: labels,
              datasets: [{
                label: 'Cantidad de Sucursales',
                data: valores,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              interaction: {
                intersect: false,
                mode: 'index'
              },
              plugins: {
                legend: {
                  display: true,
                  position: 'top',
                  labels: {
                    usePointStyle: true,
                    padding: 15,
                    font: {
                      size: 12,
                      weight: 'bold'
                    }
                  }
                },
                tooltip: {
                  backgroundColor: 'rgba(0, 0, 0, 0.8)',
                  padding: 12,
                  titleFont: {
                    size: 14,
                    weight: 'bold'
                  },
                  bodyFont: {
                    size: 13
                  },
                  displayColors: true,
                  callbacks: {
                    label: function(context) {
                      return 'Sucursales: ' + context.parsed.y;
                    }
                  }
                }
              },
              scales: {
                x: {
                  display: true,
                  grid: {
                    display: false
                  },
                  ticks: {
                    font: {
                      size: 11
                    },
                    maxRotation: 45,
                    minRotation: 45
                  }
                },
                y: {
                  beginAtZero: true,
                  ticks: {
                    stepSize: 1,
                    font: {
                      size: 11
                    },
                    callback: function(value) {
                      return value;
                    }
                  },
                  grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                  }
                }
              }
            }
          });
        }
      });
    </script>
  </body>
</html>

