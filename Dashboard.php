<?php
/**
 * Dashboard Principal
 * Sistema de Gestión de Reciclaje
 */

// Verificar autenticación
require_once __DIR__ . '/config/auth.php';

// Verificar si el usuario está autenticado
$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: index.php');
    exit;
}

// Obtener datos del usuario actual
$usuario = $auth->getCurrentUser();
$usuarioNombre = $usuario['nombre'] ?? 'Usuario';
$usuarioEmail = $usuario['email'] ?? '';
$usuarioRol = $usuario['rol'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Dashboard - <?php echo htmlspecialchars($usuarioNombre); ?></title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="assets/img/logo.jpg"
      type="image/jpeg"
    />
    <link
      rel="shortcut icon"
      href="assets/img/logo.jpg"
      type="image/jpeg"
    />

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
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
          urls: ["assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="assets/css/demo.css" />
  </head>
  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar" data-background-color="dark">
        <?php
          $basePath = '';
          include __DIR__ . '/includes/sidebar-logo.php';
        ?>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <?php
              $basePath = '';
              $currentRoute = 'dashboard';
              include __DIR__ . '/includes/sidebar.php';
            ?>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->

      <div class="main-panel">
        <div class="main-header">
          <?php
            $basePath = '';
            include __DIR__ . '/includes/main-header-logo.php';
          ?>
          <!-- Navbar Header -->
          <nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
          >
            <div class="container-fluid">
              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <?php
                  $basePath = '';
                  include __DIR__ . '/includes/user-header.php';
                ?>
              </ul>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>

        <div class="container">
          <div class="page-inner">
            <!-- Panel de Filtros -->
            <div class="row mb-4">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center">
                      <h4 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>Filtros de Análisis
                      </h4>
                      <button class="btn btn-primary btn-sm ms-auto" id="btnAplicarFiltros">
                        <i class="fas fa-search me-1"></i>Aplicar Filtros
                      </button>
                      <button class="btn btn-secondary btn-sm ms-2" id="btnLimpiarFiltros">
                        <i class="fas fa-times me-1"></i>Limpiar
                      </button>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="filtroFechaInicio">Fecha Inicio</label>
                          <input type="date" class="form-control" id="filtroFechaInicio" />
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="filtroFechaFin">Fecha Fin</label>
                          <input type="date" class="form-control" id="filtroFechaFin" />
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="filtroMaterial">Material</label>
                          <select class="form-control" id="filtroMaterial">
                            <option value="">Todos los materiales</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label for="filtroSucursal">Sucursal</label>
                          <select class="form-control" id="filtroSucursal">
                            <option value="">Todas las sucursales</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tarjetas de Estadísticas -->
            <div class="row">
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                          <i class="fas fa-shopping-cart"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Total Compras</p>
                          <h4 class="card-title" id="statTotalCompras">$0.00</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                          <i class="fas fa-dollar-sign"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Total Ventas</p>
                          <h4 class="card-title" id="statTotalVentas">$0.00</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                          <i class="fas fa-chart-line"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Ganancia Bruta</p>
                          <h4 class="card-title" id="statGanancia">$0.00</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                          <i class="fas fa-percentage"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Margen (%)</p>
                          <h4 class="card-title" id="statMargen">0%</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Gráfico: Flujo Diario de Compras y Ventas -->
            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">
                        <i class="fas fa-chart-area me-2"></i>Flujo Diario de Compras y Ventas
                      </div>
                      <div class="card-tools">
                        <a href="reportes/index.php" class="btn btn-label-success btn-round btn-sm">
                          <span class="btn-label">
                            <i class="fa fa-file-pdf"></i>
                          </span>
                          Exportar Reporte
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container" style="min-height: 375px; position: relative;">
                      <canvas id="flujoDiarioChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Análisis Comparativo -->
            <div class="row">
              <div class="col-md-6">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>Compras por Material
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container" style="min-height: 350px; position: relative;">
                      <canvas id="comprasMaterialChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>Ventas por Material
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container" style="min-height: 350px; position: relative;">
                      <canvas id="ventasMaterialChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Análisis por Sucursal -->
            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>Análisis de Negocio por Sucursal
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container" style="min-height: 400px; position: relative;">
                      <canvas id="analisisSucursalChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Gráficos adicionales (3 columnas) -->
            <div class="row">
              <div class="col-md-4">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Top 5 Productos Vendidos</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container" style="min-height: 300px; position: relative;">
                      <canvas id="topProductosChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Inventario por Categoría</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container" style="min-height: 300px; position: relative;">
                      <canvas id="inventarioChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Estado de Transacciones</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container" style="min-height: 300px; position: relative;">
                      <canvas id="estadoTransaccionesChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php include __DIR__ . '/includes/footer.php'; ?>
      </div>
      
      <!-- Modales Globales -->
      <?php 
        include __DIR__ . '/includes/modal-foto-perfil.php';
        include __DIR__ . '/includes/modal-cambiar-password.php';
      ?>
    </div>
    <!--   Core JS Files   -->
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="assets/js/plugin/jsvectormap/world.js"></script>

    <!-- Sweet Alert -->
    <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="assets/js/kaiadmin.min.js"></script>

    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="assets/js/setting-demo.js"></script>
    <script src="assets/js/demo.js"></script>
    <?php
      $basePath = '';
      include __DIR__ . '/includes/footer-scripts.php';
    ?>
    <script>
      // Variables globales para los gráficos
      var chartFlujoDiario = null;
      var chartComprasMaterial = null;
      var chartVentasMaterial = null;
      var chartAnalisisSucursal = null;
      var chartTopProductos = null;
      var chartInventario = null;
      var chartEstadoTransacciones = null;
      
      // Colores para los gráficos
      var colores = [
        '#177dff', '#f3545d', '#fdaf4b', '#1dce6c', '#9013fe',
        '#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff',
        '#c44569', '#f7b731', '#5f27cd', '#00d2d3', '#ff9ff3'
      ];
      
      // Filtros actuales
      var filtrosActuales = {
        fechaInicio: '',
        fechaFin: '',
        material: '',
        sucursal: ''
      };
      
      // Inicializar fechas por defecto (último mes)
      function inicializarFechas() {
        var hoy = new Date();
        var hace30Dias = new Date();
        hace30Dias.setDate(hoy.getDate() - 30);
        
        $('#filtroFechaInicio').val(hace30Dias.toISOString().split('T')[0]);
        $('#filtroFechaFin').val(hoy.toISOString().split('T')[0]);
        
        filtrosActuales.fechaInicio = hace30Dias.toISOString().split('T')[0];
        filtrosActuales.fechaFin = hoy.toISOString().split('T')[0];
      }
      
      // Cargar materiales para el filtro
      function cargarMaterialesFiltro() {
        $.ajax({
          url: 'materiales/api.php?action=listar&estado=activos',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var select = $('#filtroMaterial');
              response.data.forEach(function(material) {
                select.append($('<option>', {
                  value: material.nombre,
                  text: material.nombre
                }));
              });
            }
          }
        });
      }
      
      // Cargar sucursales para el filtro
      function cargarSucursalesFiltro() {
        $.ajax({
          url: 'sucursales/api.php?action=activas',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var select = $('#filtroSucursal');
              response.data.forEach(function(sucursal) {
                select.append($('<option>', {
                  value: sucursal.id,
                  text: sucursal.nombre
                }));
              });
            }
          }
        });
      }
      
      // Aplicar filtros
      $('#btnAplicarFiltros').click(function() {
        filtrosActuales = {
          fechaInicio: $('#filtroFechaInicio').val(),
          fechaFin: $('#filtroFechaFin').val(),
          material: $('#filtroMaterial').val(),
          sucursal: $('#filtroSucursal').val()
        };
        
        // Validar fechas
        if (!filtrosActuales.fechaInicio || !filtrosActuales.fechaFin) {
          swal('Error', 'Debe seleccionar fecha de inicio y fin', 'error');
          return;
        }
        
        if (new Date(filtrosActuales.fechaInicio) > new Date(filtrosActuales.fechaFin)) {
          swal('Error', 'La fecha de inicio no puede ser mayor a la fecha fin', 'error');
          return;
        }
        
        cargarTodosLosDatos();
      });
      
      // Limpiar filtros
      $('#btnLimpiarFiltros').click(function() {
        inicializarFechas();
        $('#filtroMaterial').val('');
        $('#filtroSucursal').val('');
        filtrosActuales.material = '';
        filtrosActuales.sucursal = '';
        cargarTodosLosDatos();
      });
      
      // Construir parámetros de URL
      function buildQueryParams() {
        var params = [];
        if (filtrosActuales.fechaInicio) params.push('fecha_desde=' + filtrosActuales.fechaInicio);
        if (filtrosActuales.fechaFin) params.push('fecha_hasta=' + filtrosActuales.fechaFin);
        if (filtrosActuales.material) params.push('material=' + encodeURIComponent(filtrosActuales.material));
        if (filtrosActuales.sucursal) params.push('sucursal_id=' + filtrosActuales.sucursal);
        return params.length > 0 ? '&' + params.join('&') : '';
      }
      
      // Función principal para cargar todos los datos
      function cargarTodosLosDatos() {
        cargarEstadisticas();
        cargarFlujoDiario();
        cargarComprasPorMaterial();
        cargarVentasPorMaterial();
        cargarAnalisisPorSucursal();
        cargarTopProductos();
        cargarInventarioPorCategoria();
        cargarEstadoTransacciones();
      }
      
      // Cargar estadísticas (tarjetas superiores)
      function cargarEstadisticas() {
        var params = buildQueryParams();
        
        // Cargar compras
        $.ajax({
          url: 'compras/api.php?action=listar' + params,
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var totalCompras = 0;
              response.data.forEach(function(compra) {
                if (compra.estado !== 'cancelada') {
                  totalCompras += parseFloat(compra.total || 0);
                }
              });
              $('#statTotalCompras').text('$' + totalCompras.toFixed(2));
            }
          }
        });
        
        // Cargar ventas
        $.ajax({
          url: 'ventas/api.php?action=listar' + params,
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var totalVentas = 0;
              response.data.forEach(function(venta) {
                if (venta.estado !== 'cancelada') {
                  totalVentas += parseFloat(venta.total || 0);
                }
              });
              $('#statTotalVentas').text('$' + totalVentas.toFixed(2));
              
              // Calcular ganancia y margen
              var totalCompras = parseFloat($('#statTotalCompras').text().replace('$', '')) || 0;
              var ganancia = totalVentas - totalCompras;
              var margen = totalVentas > 0 ? ((ganancia / totalVentas) * 100) : 0;
              
              $('#statGanancia').text('$' + ganancia.toFixed(2));
              $('#statMargen').text(margen.toFixed(1) + '%');
            }
          }
        });
      }
      
      // Gráfico 1: Flujo Diario de Compras y Ventas
      function cargarFlujoDiario() {
        var params = buildQueryParams();
        
        Promise.all([
          $.ajax({ url: 'compras/api.php?action=listar' + params, method: 'GET', dataType: 'json' }),
          $.ajax({ url: 'ventas/api.php?action=listar' + params, method: 'GET', dataType: 'json' })
        ]).then(function([comprasResp, ventasResp]) {
          var fechas = [];
          var comprasPorDia = {};
          var ventasPorDia = {};
          
          // Procesar compras
          if (comprasResp.success && comprasResp.data) {
            comprasResp.data.forEach(function(compra) {
              if (compra.estado !== 'cancelada') {
                var fecha = compra.fecha_compra;
                comprasPorDia[fecha] = (comprasPorDia[fecha] || 0) + parseFloat(compra.total || 0);
                if (!fechas.includes(fecha)) fechas.push(fecha);
              }
            });
          }
          
          // Procesar ventas
          if (ventasResp.success && ventasResp.data) {
            ventasResp.data.forEach(function(venta) {
              if (venta.estado !== 'cancelada') {
                var fecha = venta.fecha_venta;
                ventasPorDia[fecha] = (ventasPorDia[fecha] || 0) + parseFloat(venta.total || 0);
                if (!fechas.includes(fecha)) fechas.push(fecha);
              }
            });
          }
          
          // Ordenar fechas
          fechas.sort();
          
          var datosCompras = fechas.map(f => comprasPorDia[f] || 0);
          var datosVentas = fechas.map(f => ventasPorDia[f] || 0);
          
          // Destruir gráfico anterior
          if (chartFlujoDiario) {
            chartFlujoDiario.destroy();
          }
          
          // Crear gráfico
          var ctx = document.getElementById('flujoDiarioChart').getContext('2d');
          chartFlujoDiario = new Chart(ctx, {
            type: 'line',
            data: {
              labels: fechas.map(f => new Date(f).toLocaleDateString('es-ES')),
              datasets: [
                {
                  label: 'Compras',
                  data: datosCompras,
                  borderColor: '#f3545d',
                  backgroundColor: 'rgba(243, 84, 93, 0.1)',
                  fill: true,
                  tension: 0.4
                },
                {
                  label: 'Ventas',
                  data: datosVentas,
                  borderColor: '#1dce6c',
                  backgroundColor: 'rgba(29, 206, 108, 0.1)',
                  fill: true,
                  tension: 0.4
                }
              ]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              interaction: {
                mode: 'index',
                intersect: false,
              },
              plugins: {
                legend: {
                  display: true,
                  position: 'top',
                },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      return context.dataset.label + ': $' + context.parsed.y.toFixed(2);
                    }
                  }
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: {
                    callback: function(value) {
                      return '$' + value.toFixed(0);
                    }
                  }
                }
              }
            }
          });
        });
      }
      
      // Gráfico 2: Compras por Material
      function cargarComprasPorMaterial() {
        var params = buildQueryParams();
        
        $.ajax({
          url: 'compras/api.php?action=listar' + params,
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var datosPorMaterial = {};
              
              response.data.forEach(function(compra) {
                if (compra.estado !== 'cancelada' && compra.detalles) {
                  compra.detalles.forEach(function(detalle) {
                    var material = detalle.material_nombre || 'Sin especificar';
                    datosPorMaterial[material] = (datosPorMaterial[material] || 0) + parseFloat(detalle.subtotal || 0);
                  });
                }
              });
              
              var labels = Object.keys(datosPorMaterial);
              var valores = Object.values(datosPorMaterial);
              
              if (chartComprasMaterial) {
                chartComprasMaterial.destroy();
              }
              
              var ctx = document.getElementById('comprasMaterialChart').getContext('2d');
              chartComprasMaterial = new Chart(ctx, {
                type: 'doughnut',
                data: {
                  labels: labels,
                  datasets: [{
                    data: valores,
                    backgroundColor: colores.slice(0, labels.length),
                    borderWidth: 2
                  }]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                    legend: {
                      position: 'bottom',
                      labels: {
                        padding: 10,
                        usePointStyle: true
                      }
                    },
                    tooltip: {
                      callbacks: {
                        label: function(context) {
                          var total = context.dataset.data.reduce((a, b) => a + b, 0);
                          var value = context.parsed;
                          var percentage = ((value / total) * 100).toFixed(1);
                          return context.label + ': $' + value.toFixed(2) + ' (' + percentage + '%)';
                        }
                      }
                    }
                  }
                }
              });
            }
          }
        });
      }
      
      // Gráfico 3: Ventas por Material
      function cargarVentasPorMaterial() {
        var params = buildQueryParams();
        
        $.ajax({
          url: 'ventas/api.php?action=listar' + params,
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var datosPorMaterial = {};
              
              response.data.forEach(function(venta) {
                if (venta.estado !== 'cancelada' && venta.detalles) {
                  venta.detalles.forEach(function(detalle) {
                    var material = detalle.material_nombre || 'Sin especificar';
                    datosPorMaterial[material] = (datosPorMaterial[material] || 0) + parseFloat(detalle.subtotal || 0);
                  });
                }
              });
              
              var labels = Object.keys(datosPorMaterial);
              var valores = Object.values(datosPorMaterial);
              
              if (chartVentasMaterial) {
                chartVentasMaterial.destroy();
              }
              
              var ctx = document.getElementById('ventasMaterialChart').getContext('2d');
              chartVentasMaterial = new Chart(ctx, {
                type: 'doughnut',
                data: {
                  labels: labels,
                  datasets: [{
                    data: valores,
                    backgroundColor: colores.slice(0, labels.length),
                    borderWidth: 2
                  }]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                    legend: {
                      position: 'bottom',
                      labels: {
                        padding: 10,
                        usePointStyle: true
                      }
                    },
                    tooltip: {
                      callbacks: {
                        label: function(context) {
                          var total = context.dataset.data.reduce((a, b) => a + b, 0);
                          var value = context.parsed;
                          var percentage = ((value / total) * 100).toFixed(1);
                          return context.label + ': $' + value.toFixed(2) + ' (' + percentage + '%)';
                        }
                      }
                    }
                  }
                }
              });
            }
          }
        });
      }
      
      // Gráfico 4: Análisis por Sucursal
      function cargarAnalisisPorSucursal() {
        var params = buildQueryParams();
        
        Promise.all([
          $.ajax({ url: 'compras/api.php?action=listar' + params, method: 'GET', dataType: 'json' }),
          $.ajax({ url: 'ventas/api.php?action=listar' + params, method: 'GET', dataType: 'json' })
        ]).then(function([comprasResp, ventasResp]) {
          var sucursales = [];
          var comprasPorSucursal = {};
          var ventasPorSucursal = {};
          
          // Procesar compras
          if (comprasResp.success && comprasResp.data) {
            comprasResp.data.forEach(function(compra) {
              if (compra.estado !== 'cancelada') {
                var sucursal = compra.sucursal_nombre || 'Sin sucursal';
                comprasPorSucursal[sucursal] = (comprasPorSucursal[sucursal] || 0) + parseFloat(compra.total || 0);
                if (!sucursales.includes(sucursal)) sucursales.push(sucursal);
              }
            });
          }
          
          // Procesar ventas
          if (ventasResp.success && ventasResp.data) {
            ventasResp.data.forEach(function(venta) {
              if (venta.estado !== 'cancelada') {
                var sucursal = venta.sucursal_nombre || 'Sin sucursal';
                ventasPorSucursal[sucursal] = (ventasPorSucursal[sucursal] || 0) + parseFloat(venta.total || 0);
                if (!sucursales.includes(sucursal)) sucursales.push(sucursal);
              }
            });
          }
          
          var datosCompras = sucursales.map(s => comprasPorSucursal[s] || 0);
          var datosVentas = sucursales.map(s => ventasPorSucursal[s] || 0);
          var datosGanancia = sucursales.map(s => (ventasPorSucursal[s] || 0) - (comprasPorSucursal[s] || 0));
          
          if (chartAnalisisSucursal) {
            chartAnalisisSucursal.destroy();
          }
          
          var ctx = document.getElementById('analisisSucursalChart').getContext('2d');
          chartAnalisisSucursal = new Chart(ctx, {
            type: 'bar',
            data: {
              labels: sucursales,
              datasets: [
                {
                  label: 'Compras',
                  data: datosCompras,
                  backgroundColor: '#f3545d'
                },
                {
                  label: 'Ventas',
                  data: datosVentas,
                  backgroundColor: '#1dce6c'
                },
                {
                  label: 'Ganancia',
                  data: datosGanancia,
                  backgroundColor: '#fdaf4b'
                }
              ]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  display: true,
                  position: 'top'
                },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      return context.dataset.label + ': $' + context.parsed.y.toFixed(2);
                    }
                  }
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: {
                    callback: function(value) {
                      return '$' + value.toFixed(0);
                    }
                  }
                }
              }
            }
          });
        });
      }
      
      // Gráfico 5: Top 5 Productos Vendidos
      function cargarTopProductos() {
        var params = buildQueryParams();
        
        $.ajax({
          url: 'ventas/api.php?action=listar' + params,
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var datosPorProducto = {};
              
              response.data.forEach(function(venta) {
                if (venta.estado !== 'cancelada' && venta.detalles) {
                  venta.detalles.forEach(function(detalle) {
                    var producto = detalle.producto_nombre || 'Sin nombre';
                    datosPorProducto[producto] = (datosPorProducto[producto] || 0) + parseFloat(detalle.cantidad || 0);
                  });
                }
              });
              
              // Ordenar y tomar top 5
              var sorted = Object.entries(datosPorProducto)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 5);
              
              var labels = sorted.map(item => item[0]);
              var valores = sorted.map(item => item[1]);
              
              if (chartTopProductos) {
                chartTopProductos.destroy();
              }
              
              var ctx = document.getElementById('topProductosChart').getContext('2d');
              chartTopProductos = new Chart(ctx, {
                type: 'bar',
                data: {
                  labels: labels,
                  datasets: [{
                    label: 'Cantidad Vendida',
                    data: valores,
                    backgroundColor: '#177dff'
                  }]
                },
                options: {
                  indexAxis: 'y',
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                    legend: {
                      display: false
                    }
                  },
                  scales: {
                    x: {
                      beginAtZero: true
                    }
                  }
                }
              });
            }
          }
        });
      }
      
      // Gráfico 6: Inventario por Categoría
      function cargarInventarioPorCategoria() {
        $.ajax({
          url: 'inventarios/api.php?action=listar',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var datosPorCategoria = {};
              
              response.data.forEach(function(inventario) {
                var categoria = inventario.categoria_nombre || 'Sin categoría';
                datosPorCategoria[categoria] = (datosPorCategoria[categoria] || 0) + parseFloat(inventario.cantidad || 0);
              });
              
              var labels = Object.keys(datosPorCategoria);
              var valores = Object.values(datosPorCategoria);
              
              if (chartInventario) {
                chartInventario.destroy();
              }
              
              var ctx = document.getElementById('inventarioChart').getContext('2d');
              chartInventario = new Chart(ctx, {
                type: 'pie',
                data: {
                  labels: labels,
                  datasets: [{
                    data: valores,
                    backgroundColor: colores.slice(0, labels.length)
                  }]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                    legend: {
                      position: 'bottom',
                      labels: {
                        padding: 10,
                        usePointStyle: true
                      }
                    }
                  }
                }
              });
            }
          }
        });
      }
      
      // Gráfico 7: Estado de Transacciones
      function cargarEstadoTransacciones() {
        var params = buildQueryParams();
        
        Promise.all([
          $.ajax({ url: 'compras/api.php?action=listar&estado=todos' + params, method: 'GET', dataType: 'json' }),
          $.ajax({ url: 'ventas/api.php?action=listar&estado=todos' + params, method: 'GET', dataType: 'json' })
        ]).then(function([comprasResp, ventasResp]) {
          var completadas = 0, pendientes = 0, canceladas = 0;
          
          if (comprasResp.success && comprasResp.data) {
            comprasResp.data.forEach(function(compra) {
              if (compra.estado === 'completada') completadas++;
              else if (compra.estado === 'pendiente') pendientes++;
              else if (compra.estado === 'cancelada') canceladas++;
            });
          }
          
          if (ventasResp.success && ventasResp.data) {
            ventasResp.data.forEach(function(venta) {
              if (venta.estado === 'completada') completadas++;
              else if (venta.estado === 'pendiente') pendientes++;
              else if (venta.estado === 'cancelada') canceladas++;
            });
          }
          
          if (chartEstadoTransacciones) {
            chartEstadoTransacciones.destroy();
          }
          
          var ctx = document.getElementById('estadoTransaccionesChart').getContext('2d');
          chartEstadoTransacciones = new Chart(ctx, {
            type: 'doughnut',
            data: {
              labels: ['Completadas', 'Pendientes', 'Canceladas'],
              datasets: [{
                data: [completadas, pendientes, canceladas],
                backgroundColor: ['#1dce6c', '#fdaf4b', '#f3545d']
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'bottom'
                }
              }
            }
          });
        });
      }
      
      // Inicializar al cargar la página
      $(document).ready(function() {
        inicializarFechas();
        cargarMaterialesFiltro();
        cargarSucursalesFiltro();
        cargarTodosLosDatos();
      });
    </script>
  </body>
</html>
