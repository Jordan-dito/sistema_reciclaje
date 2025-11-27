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
                  include __DIR__ . '/includes/modal-foto-perfil.php';
                ?>
              </ul>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="row">
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-primary bubble-shadow-small"
                        >
                          <i class="fas fa-users"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Visitors</p>
                          <h4 class="card-title">1,294</h4>
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
                        <div
                          class="icon-big text-center icon-info bubble-shadow-small"
                        >
                          <i class="fas fa-user-check"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Subscribers</p>
                          <h4 class="card-title">1303</h4>
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
                        <div
                          class="icon-big text-center icon-success bubble-shadow-small"
                        >
                          <i class="fas fa-luggage-cart"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Sales</p>
                          <h4 class="card-title">$ 1,345</h4>
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
                        <div
                          class="icon-big text-center icon-secondary bubble-shadow-small"
                        >
                          <i class="far fa-check-circle"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Order</p>
                          <h4 class="card-title">576</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Sucursales por Dirección</div>
                      <div class="card-tools">
                        <a
                          href="reportes/index.php"
                          class="btn btn-label-success btn-round btn-sm"
                        >
                          <span class="btn-label">
                            <i class="fa fa-chart-line"></i>
                          </span>
                          Reportes
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container" style="min-height: 300px; position: relative;">
                      <canvas id="sucursalesChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Usuarios por Rol</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container" style="min-height: 300px; position: relative;">
                      <canvas id="usuariosChart"></canvas>
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
            </div>
          </div>
        </div>

        <?php include __DIR__ . '/includes/footer.php'; ?>
      </div>

      <!-- Custom template | don't include it in your project! -->
      <div class="custom-template">
        <div class="title">Settings</div>
        <div class="custom-content">
          <div class="switcher">
            <div class="switch-block">
              <h4>Logo Header</h4>
              <div class="btnSwitch">
                <button
                  type="button"
                  class="selected changeLogoHeaderColor"
                  data-color="dark"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="blue"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="purple"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="light-blue"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="green"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="orange"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="red"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="white"
                ></button>
                <br />
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="dark2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="blue2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="purple2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="light-blue2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="green2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="orange2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="red2"
                ></button>
              </div>
            </div>
            <div class="switch-block">
              <h4>Navbar Header</h4>
              <div class="btnSwitch">
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="dark"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="blue"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="purple"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="light-blue"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="green"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="orange"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="red"
                ></button>
                <button
                  type="button"
                  class="selected changeTopBarColor"
                  data-color="white"
                ></button>
                <br />
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="dark2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="blue2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="purple2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="light-blue2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="green2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="orange2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="red2"
                ></button>
              </div>
            </div>
            <div class="switch-block">
              <h4>Sidebar</h4>
              <div class="btnSwitch">
                <button
                  type="button"
                  class="changeSideBarColor"
                  data-color="white"
                ></button>
                <button
                  type="button"
                  class="selected changeSideBarColor"
                  data-color="dark"
                ></button>
                <button
                  type="button"
                  class="changeSideBarColor"
                  data-color="dark2"
                ></button>
              </div>
            </div>
          </div>
        </div>
        <div class="custom-toggle">
          <i class="icon-settings"></i>
        </div>
      </div>
      <!-- End Custom template -->
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
      var chartSucursales = null;
      var chartUsuarios = null;
      var chartInventario = null;
      
      // Colores para los gráficos
      var colores = [
        '#177dff', '#f3545d', '#fdaf4b', '#1dce6c', '#9013fe',
        '#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff'
      ];
      
      // Gráfico 1: Sucursales por Dirección (Pastel)
      function cargarGraficoSucursales() {
        $.ajax({
          url: 'sucursales/api.php?action=listar',
          method: 'GET',
          dataType: 'json',
          xhrFields: {
            withCredentials: true
          },
          crossDomain: false,
          success: function(response) {
            if (response.success && response.data.length > 0) {
              // Agrupar por dirección
              var datosPorDireccion = {};
              response.data.forEach(function(sucursal) {
                var direccion = sucursal.direccion || 'Sin dirección';
                if (!datosPorDireccion[direccion]) {
                  datosPorDireccion[direccion] = 0;
                }
                datosPorDireccion[direccion]++;
              });
              
              var labels = Object.keys(datosPorDireccion);
              var valores = Object.values(datosPorDireccion);
              var backgroundColors = colores.slice(0, labels.length);
              
              // Destruir gráfico anterior si existe
              if (chartSucursales) {
                chartSucursales.destroy();
              }
              
              // Crear gráfico de pastel
              var ctx = document.getElementById('sucursalesChart').getContext('2d');
              chartSucursales = new Chart(ctx, {
                type: 'pie',
                data: {
                  labels: labels,
                  datasets: [{
                    data: valores,
                    backgroundColor: backgroundColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                  }]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                    legend: {
                      display: true,
                      position: 'bottom',
                      labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                          size: 11
                        }
                      }
                    },
                    tooltip: {
                      callbacks: {
                        label: function(context) {
                          var label = context.label || '';
                          var value = context.parsed || 0;
                          var total = context.dataset.data.reduce((a, b) => a + b, 0);
                          var percentage = ((value / total) * 100).toFixed(1);
                          return label + ': ' + value + ' (' + percentage + '%)';
                        }
                      }
                    }
                  }
                }
              });
            } else {
              document.getElementById('sucursalesChart').parentElement.innerHTML = 
                '<div class="alert alert-info text-center">No hay sucursales registradas</div>';
            }
          },
          error: function() {
            document.getElementById('sucursalesChart').parentElement.innerHTML = 
              '<div class="alert alert-warning text-center">Error al cargar los datos</div>';
          }
        });
      }
      
      // Gráfico 2: Usuarios por Rol (Barras)
      function cargarGraficoUsuarios() {
        $.ajax({
          url: 'usuarios/api.php?action=listar',
          method: 'GET',
          dataType: 'json',
          xhrFields: {
            withCredentials: true
          },
          crossDomain: false,
          success: function(response) {
            if (response.success && response.data.length > 0) {
              // Agrupar por rol
              var datosPorRol = {};
              response.data.forEach(function(usuario) {
                var rol = usuario.rol_nombre || 'Sin rol';
                if (!datosPorRol[rol]) {
                  datosPorRol[rol] = 0;
                }
                datosPorRol[rol]++;
              });
              
              var labels = Object.keys(datosPorRol);
              var valores = Object.values(datosPorRol);
              var backgroundColors = colores.slice(0, labels.length);
              
              // Destruir gráfico anterior si existe
              if (chartUsuarios) {
                chartUsuarios.destroy();
              }
              
              // Crear gráfico de barras
              var ctx = document.getElementById('usuariosChart').getContext('2d');
              chartUsuarios = new Chart(ctx, {
                type: 'bar',
                data: {
                  labels: labels,
                  datasets: [{
                    label: 'Cantidad de Usuarios',
                    data: valores,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors,
                    borderWidth: 1
                  }]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                    legend: {
                      display: false
                    },
                    tooltip: {
                      callbacks: {
                        label: function(context) {
                          return 'Usuarios: ' + context.parsed.y;
                        }
                      }
                    }
                  },
                  scales: {
                    y: {
                      beginAtZero: true,
                      ticks: {
                        stepSize: 1
                      }
                    }
                  }
                }
              });
            } else {
              document.getElementById('usuariosChart').parentElement.innerHTML = 
                '<div class="alert alert-info text-center">No hay usuarios registrados</div>';
            }
          },
          error: function() {
            document.getElementById('usuariosChart').parentElement.innerHTML = 
              '<div class="alert alert-warning text-center">Error al cargar los datos</div>';
          }
        });
      }
      
      // Gráfico 3: Inventario por Categoría (Barras)
      function cargarGraficoInventario() {
        $.ajax({
          url: 'inventarios/api.php?action=listar',
          method: 'GET',
          dataType: 'json',
          xhrFields: {
            withCredentials: true
          },
          crossDomain: false,
          success: function(response) {
            if (response.success && response.data.length > 0) {
              // Agrupar por categoría/material
              var datosPorCategoria = {};
              response.data.forEach(function(inventario) {
                // Usar categoria_padre_nombre si existe, sino material_nombre
                var categoria = inventario.categoria_padre_nombre || inventario.material_nombre || 'Sin categoría';
                if (!datosPorCategoria[categoria]) {
                  datosPorCategoria[categoria] = 0;
                }
                datosPorCategoria[categoria]++;
              });
              
              var labels = Object.keys(datosPorCategoria);
              var valores = Object.values(datosPorCategoria);
              var backgroundColors = colores.slice(0, labels.length);
              
              // Destruir gráfico anterior si existe
              if (chartInventario) {
                chartInventario.destroy();
              }
              
              // Crear gráfico de barras
              var ctx = document.getElementById('inventarioChart').getContext('2d');
              chartInventario = new Chart(ctx, {
                type: 'bar',
                data: {
                  labels: labels,
                  datasets: [{
                    label: 'Cantidad de Productos',
                    data: valores,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors,
                    borderWidth: 1
                  }]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                    legend: {
                      display: false
                    },
                    tooltip: {
                      callbacks: {
                        label: function(context) {
                          return 'Productos: ' + context.parsed.y;
                        }
                      }
                    }
                  },
                  scales: {
                    y: {
                      beginAtZero: true,
                      ticks: {
                        stepSize: 1
                      }
                    }
                  }
                }
              });
            } else {
              document.getElementById('inventarioChart').parentElement.innerHTML = 
                '<div class="alert alert-info text-center">No hay productos en inventario</div>';
            }
          },
          error: function() {
            document.getElementById('inventarioChart').parentElement.innerHTML = 
              '<div class="alert alert-warning text-center">Error al cargar los datos</div>';
          }
        });
      }
      
      // Cargar todos los gráficos al iniciar
      $(document).ready(function() {
        cargarGraficoSucursales();
        cargarGraficoUsuarios();
        cargarGraficoInventario();
      });
    </script>
  </body>
</html>
