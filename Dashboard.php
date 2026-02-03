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
    <link rel="stylesheet" href="assets/css/demo.css" />
    
    <style>
      /* ===== ESTILOS PROFESIONALES DASHBOARD ===== */
      
      /* Animaciones suaves */
      * {
        transition: all 0.3s ease;
      }
      
      /* Panel de filtros mejorado */
      .filter-panel {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        margin-bottom: 30px;
      }
      
      .filter-panel .card-title {
        color: white;
        font-weight: 600;
        font-size: 1.3rem;
      }
      
      .filter-panel .form-control,
      .filter-panel .form-select {
        border-radius: 10px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.95);
        padding: 12px 15px;
        font-size: 0.95rem;
      }
      
      .filter-panel .form-control:focus,
      .filter-panel .form-select:focus {
        border-color: white;
        box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        background: white;
      }
      
      .filter-panel label {
        color: white;
        font-weight: 500;
        margin-bottom: 8px;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      
      /* Tarjetas de estadísticas mejoradas */
      .stat-card {
        border-radius: 20px;
        border: none;
        overflow: hidden;
        position: relative;
        height: 100%;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      
      .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
      }
      
      .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
      }
      
      .stat-card.primary {
        --gradient-start: #667eea;
        --gradient-end: #764ba2;
      }
      
      .stat-card.success {
        --gradient-start: #11998e;
        --gradient-end: #38ef7d;
      }
      
      .stat-card.warning {
        --gradient-start: #f2994a;
        --gradient-end: #f2c94c;
      }
      
      .stat-card.info {
        --gradient-start: #00d2ff;
        --gradient-end: #3a7bd5;
      }
      
      .stat-card .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        color: white;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      }
      
      .stat-card .stat-content {
        flex: 1;
        padding-left: 20px;
      }
      
      .stat-card .stat-label {
        font-size: 0.85rem;
        color: #8e8e93;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
      }
      
      .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1a1a1a;
        line-height: 1;
        margin: 0;
      }
      
      .stat-card .stat-change {
        font-size: 0.8rem;
        margin-top: 8px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
      }
      
      .stat-change.positive {
        background: #d4edda;
        color: #155724;
      }
      
      .stat-change.negative {
        background: #f8d7da;
        color: #721c24;
      }
      
      /* Tarjetas de gráficos mejoradas */
      .chart-card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 30px;
        background: white;
      }
      
      .chart-card .card-header {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-bottom: none;
        padding: 20px 25px;
      }
      
      .chart-card .card-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
      }
      
      .chart-card .card-title i {
        font-size: 1.3rem;
        color: #667eea;
      }
      
      .chart-card .card-body {
        padding: 25px;
      }
      
      /* Botones mejorados */
      .btn-modern {
        border-radius: 12px;
        padding: 12px 30px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
      }
      
      .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      }
      
      .btn-modern.btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      }
      
      .btn-modern.btn-secondary {
        background: linear-gradient(135deg, #868f96 0%, #596164 100%);
      }
      
      /* Loading skeleton */
      .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 8px;
      }
      
      @keyframes loading {
        0% {
          background-position: 200% 0;
        }
        100% {
          background-position: -200% 0;
        }
      }
      
      /* Badges mejorados */
      .badge-modern {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      
      /* Tooltips personalizados */
      .info-tooltip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #667eea;
        color: white;
        font-size: 0.7rem;
        cursor: help;
        margin-left: 5px;
      }
      
      /* Responsive mejoras */
      @media (max-width: 768px) {
        .stat-card .stat-value {
          font-size: 1.5rem;
        }
        
        .stat-card .stat-icon {
          width: 50px;
          height: 50px;
          font-size: 1.5rem;
        }
        
        .filter-panel {
          padding: 20px 15px;
        }
      }
      
      /* Animación de entrada */
      @keyframes fadeInUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      
      .animate-in {
        animation: fadeInUp 0.6s ease forwards;
      }
      
      /* Efecto de brillo en hover */
      .shine-effect {
        position: relative;
        overflow: hidden;
      }
      
      .shine-effect::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
          45deg,
          transparent 30%,
          rgba(255, 255, 255, 0.3) 50%,
          transparent 70%
        );
        transform: translateX(-100%);
        transition: transform 0.6s;
      }
      
      .shine-effect:hover::after {
        transform: translateX(100%);
      }
    </style>
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
            <!-- Panel de Filtros Mejorado -->
            <div class="row mb-4 animate-in">
              <div class="col-md-12">
                <div class="filter-panel">
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="card-title mb-0">
                      <i class="fas fa-sliders-h me-2"></i>Panel de Control y Filtros
                    </h4>
                    <div class="d-flex gap-2">
                      <button class="btn btn-light btn-modern" id="btnAplicarFiltros">
                        <i class="fas fa-search me-2"></i>Aplicar
                      </button>
                      <button class="btn btn-secondary btn-modern" id="btnLimpiarFiltros">
                        <i class="fas fa-redo me-2"></i>Restablecer
                      </button>
                    </div>
                  </div>
                  <div class="row g-3">
                    <div class="col-md-3">
                      <div class="form-group mb-0">
                        <label for="filtroFechaInicio">
                          <i class="fas fa-calendar-alt me-1"></i>Fecha Inicio
                        </label>
                        <input type="date" class="form-control" id="filtroFechaInicio" />
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group mb-0">
                        <label for="filtroFechaFin">
                          <i class="fas fa-calendar-check me-1"></i>Fecha Fin
                        </label>
                        <input type="date" class="form-control" id="filtroFechaFin" />
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group mb-0">
                        <label for="filtroMaterial">
                          <i class="fas fa-recycle me-1"></i>Material
                        </label>
                        <select class="form-control" id="filtroMaterial">
                          <option value="">Todos los materiales</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group mb-0">
                        <label for="filtroSucursal">
                          <i class="fas fa-store me-1"></i>Sucursal
                        </label>
                        <select class="form-control" id="filtroSucursal">
                          <option value="">Todas las sucursales</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tarjetas de Estadísticas Mejoradas -->
            <div class="row g-4 mb-4">
              <div class="col-sm-6 col-md-3 animate-in" style="animation-delay: 0.1s">
                <div class="card stat-card primary shine-effect">
                  <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                      <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                      </div>
                      <div class="stat-content">
                        <div class="stat-label">Total Compras</div>
                        <h4 class="stat-value" id="statTotalCompras">$0.00</h4>
                        <span class="stat-change positive" id="changeCompras" style="display: none;">
                          <i class="fas fa-arrow-up"></i> <span>0%</span>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3 animate-in" style="animation-delay: 0.2s">
                <div class="card stat-card success shine-effect">
                  <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                      <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                      </div>
                      <div class="stat-content">
                        <div class="stat-label">Total Ventas</div>
                        <h4 class="stat-value" id="statTotalVentas">$0.00</h4>
                        <span class="stat-change positive" id="changeVentas" style="display: none;">
                          <i class="fas fa-arrow-up"></i> <span>0%</span>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3 animate-in" style="animation-delay: 0.3s">
                <div class="card stat-card warning shine-effect">
                  <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                      <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                      </div>
                      <div class="stat-content">
                        <div class="stat-label">Ganancia Bruta</div>
                        <h4 class="stat-value" id="statGanancia">$0.00</h4>
                        <span class="stat-change positive" id="changeGanancia" style="display: none;">
                          <i class="fas fa-arrow-up"></i> <span>0%</span>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3 animate-in" style="animation-delay: 0.4s">
                <div class="card stat-card info shine-effect">
                  <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                      <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                      </div>
                      <div class="stat-content">
                        <div class="stat-label">Margen (%)</div>
                        <h4 class="stat-value" id="statMargen">0%</h4>
                        <span class="badge-modern bg-info text-white" id="badgeMargen" style="display: none;">
                          Excelente
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Gráfico: Flujo Diario de Compras y Ventas -->
            <div class="row animate-in" style="animation-delay: 0.5s">
              <div class="col-md-12">
                <div class="chart-card">
                  <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                      <h5 class="card-title">
                        <i class="fas fa-chart-area"></i>
                        Flujo Diario de Compras y Ventas
                        <span class="info-tooltip" title="Comparación diaria de ingresos y egresos">?</span>
                      </h5>
                      <a href="reportes/index.php" class="btn btn-success btn-sm btn-modern">
                        <i class="fa fa-file-pdf me-2"></i>
                        Exportar PDF
                      </a>
                    </div>
                  </div>
                  <div class="card-body">
                    <div id="flujoDiarioChart" style="width: 100%; height: 450px;"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Análisis Comparativo -->
            <div class="row g-4 animate-in" style="animation-delay: 0.6s">
              <div class="col-md-6">
                <div class="chart-card">
                  <div class="card-header">
                    <h5 class="card-title">
                      <i class="fas fa-chart-pie"></i>
                      Compras por Material
                      <span class="info-tooltip" title="Distribución de compras por tipo de material">?</span>
                    </h5>
                  </div>
                  <div class="card-body">
                    <div id="comprasMaterialChart" style="width: 100%; height: 400px;"></div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="chart-card">
                  <div class="card-header">
                    <h5 class="card-title">
                      <i class="fas fa-chart-pie"></i>
                      Ventas por Material
                      <span class="info-tooltip" title="Distribución de ventas por tipo de material">?</span>
                    </h5>
                  </div>
                  <div class="card-body">
                    <div id="ventasMaterialChart" style="width: 100%; height: 400px;"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Análisis por Sucursal -->
            <div class="row animate-in" style="animation-delay: 0.7s">
              <div class="col-md-12">
                <div class="chart-card">
                  <div class="card-header">
                    <h5 class="card-title">
                      <i class="fas fa-chart-bar"></i>
                      Análisis de Negocio por Sucursal
                      <span class="info-tooltip" title="Comparación de rendimiento entre sucursales">?</span>
                    </h5>
                  </div>
                  <div class="card-body">
                    <div id="analisisSucursalChart" style="width: 100%; height: 450px;"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Gráficos adicionales (3 columnas) -->
            <div class="row g-4 animate-in" style="animation-delay: 0.8s">
              <div class="col-md-4">
                <div class="chart-card">
                  <div class="card-header">
                    <h5 class="card-title">
                      <i class="fas fa-trophy"></i>
                      Top 5 Productos
                      <span class="info-tooltip" title="Productos más vendidos del período">?</span>
                    </h5>
                  </div>
                  <div class="card-body">
                    <div id="topProductosChart" style="width: 100%; height: 350px;"></div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="chart-card">
                  <div class="card-header">
                    <h5 class="card-title">
                      <i class="fas fa-boxes"></i>
                      Inventario
                      <span class="info-tooltip" title="Distribución del inventario por categoría">?</span>
                    </h5>
                  </div>
                  <div class="card-body">
                    <div id="inventarioChart" style="width: 100%; height: 350px;"></div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="chart-card">
                  <div class="card-header">
                    <h5 class="card-title">
                      <i class="fas fa-exchange-alt"></i>
                      Transacciones
                      <span class="info-tooltip" title="Estado de las transacciones del sistema">?</span>
                    </h5>
                  </div>
                  <div class="card-body">
                    <div id="estadoTransaccionesChart" style="width: 100%; height: 350px;"></div>
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

    <!-- ECharts - Librería profesional de gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    
    <!-- Dashboard ECharts - Funciones personalizadas -->
    <script src="assets/js/dashboard-echarts.js"></script>

    <!-- Datatables -->
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps - COMENTADO: Causa conflictos -->
    <!-- <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script> -->
    <!-- <script src="assets/js/plugin/jsvectormap/world.js"></script> -->

    <!-- Sweet Alert -->
    <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="assets/js/kaiadmin.min.js"></script>

    <!-- Kaiadmin DEMO methods - COMENTADO: No necesario en producción -->
    <!-- <script src="assets/js/setting-demo.js"></script> -->
    <!-- <script src="assets/js/demo.js"></script> -->
    <?php
      $basePath = '';
      include __DIR__ . '/includes/footer-scripts.php';
    ?>
    <script>
      // Variables globales para los gráficos ECharts
      var chartFlujoDiario = null;
      var chartComprasMaterial = null;
      var chartVentasMaterial = null;
      var chartAnalisisSucursal = null;
      var chartTopProductos = null;
      var chartInventario = null;
      var chartEstadoTransacciones = null;
      
      // Paleta de colores profesional y moderna
      var coloresProfesionales = [
        '#667eea', '#764ba2', '#f093fb', '#4facfe',
        '#43e97b', '#fa709a', '#fee140', '#30cfd0',
        '#a8edea', '#fed6e3', '#c471f5', '#fa7cbb',
        '#f38181', '#aa96da', '#fcbad3', '#ffffd2'
      ];
      
      // Tema personalizado para ECharts
      var temaElegante = {
        color: coloresProfesionales,
        backgroundColor: 'transparent',
        textStyle: {
          fontFamily: "'Public Sans', sans-serif",
          fontSize: 13,
          color: '#666'
        },
        title: {
          textStyle: {
            color: '#2c3e50',
            fontWeight: 600
          }
        },
        legend: {
          textStyle: {
            color: '#666'
          }
        },
        grid: {
          borderWidth: 0,
          borderColor: 'transparent'
        }
      };
      
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
      
      // Función para mostrar loading en tarjetas
      function mostrarLoading() {
        $('.stat-value').html('<div class="skeleton" style="height: 30px; width: 100px;"></div>');
        $('.chart-container canvas').css('opacity', '0.3');
      }
      
      function ocultarLoading() {
        $('.chart-container canvas').css('opacity', '1');
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
        
        // Mostrar feedback visual
        var btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Cargando...').prop('disabled', true);
        mostrarLoading();
        
        // Cargar datos
        cargarTodosLosDatos();
        
        // Restaurar botón
        setTimeout(() => {
          btn.html('<i class="fas fa-search me-2"></i>Aplicar').prop('disabled', false);
          ocultarLoading();
        }, 1000);
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
      
      // Función para formatear números con separadores de miles
      function formatearMoneda(valor) {
        return '$' + valor.toLocaleString('es-ES', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
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
              $('#statTotalCompras').text(formatearMoneda(totalCompras));
              $('#statTotalCompras').data('valor', totalCompras); // Guardar valor numérico
            } else {
              $('#statTotalCompras').text('$0.00');
            }
          },
          error: function() {
            $('#statTotalCompras').text('$0.00');
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
              $('#statTotalVentas').text(formatearMoneda(totalVentas));
              $('#statTotalVentas').data('valor', totalVentas); // Guardar valor numérico
              
              // Calcular ganancia y margen
              var totalCompras = $('#statTotalCompras').data('valor') || 0;
              var ganancia = totalVentas - totalCompras;
              var margen = totalVentas > 0 ? ((ganancia / totalVentas) * 100) : 0;
              
              $('#statGanancia').text(formatearMoneda(ganancia));
              $('#statMargen').text(margen.toFixed(1) + '%');
              
              // Actualizar badge de margen
              var badgeMargen = $('#badgeMargen');
              if (margen >= 30) {
                badgeMargen.removeClass('bg-warning bg-danger').addClass('bg-success').text('Excelente').show();
              } else if (margen >= 15) {
                badgeMargen.removeClass('bg-success bg-danger').addClass('bg-warning').text('Bueno').show();
              } else if (margen > 0) {
                badgeMargen.removeClass('bg-success bg-warning').addClass('bg-danger').text('Bajo').show();
              } else {
                badgeMargen.hide();
              }
            } else {
              $('#statTotalVentas').text('$0.00');
              $('#statGanancia').text('$0.00');
              $('#statMargen').text('0%');
            }
          },
          error: function() {
            $('#statTotalVentas').text('$0.00');
            $('#statGanancia').text('$0.00');
            $('#statMargen').text('0%');
          }
        });
      }
      
      // Gráfico 1: Flujo Diario de Compras y Ventas con ECharts
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
          var labels = fechas.map(f => new Date(f).toLocaleDateString('es-ES', { day: '2-digit', month: 'short' }));
          
          // Inicializar o actualizar gráfico ECharts
          if (!chartFlujoDiario) {
            chartFlujoDiario = echarts.init(document.getElementById('flujoDiarioChart'));
          }
          
          var option = {
            tooltip: {
              trigger: 'axis',
              backgroundColor: 'rgba(0, 0, 0, 0.85)',
              borderWidth: 0,
              textStyle: {
                color: '#fff',
                fontSize: 13
              },
              axisPointer: {
                type: 'cross',
                label: {
                  backgroundColor: '#667eea'
                }
              },
              formatter: function(params) {
                var result = params[0].name + '<br/>';
                params.forEach(function(item) {
                  result += item.marker + ' ' + item.seriesName + ': $' + 
                           item.value.toLocaleString('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '<br/>';
                });
                return result;
              }
            },
            legend: {
              data: ['Compras', 'Ventas'],
              top: 10,
              textStyle: {
                fontSize: 13,
                fontWeight: 600
              }
            },
            grid: {
              left: '3%',
              right: '4%',
              bottom: '3%',
              top: '15%',
              containLabel: true
            },
            xAxis: {
              type: 'category',
              boundaryGap: false,
              data: labels,
              axisLine: {
                lineStyle: {
                  color: '#e0e0e0'
                }
              },
              axisLabel: {
                color: '#666',
                fontSize: 11
              }
            },
            yAxis: {
              type: 'value',
              axisLine: {
                show: false
              },
              axisTick: {
                show: false
              },
              axisLabel: {
                color: '#666',
                fontSize: 12,
                formatter: function(value) {
                  return '$' + value.toLocaleString('es-ES');
                }
              },
              splitLine: {
                lineStyle: {
                  color: 'rgba(0, 0, 0, 0.05)'
                }
              }
            },
            series: [
              {
                name: 'Compras',
                type: 'line',
                smooth: true,
                symbol: 'circle',
                symbolSize: 8,
                lineStyle: {
                  width: 3,
                  color: '#f3545d'
                },
                itemStyle: {
                  color: '#f3545d',
                  borderWidth: 2,
                  borderColor: '#fff'
                },
                areaStyle: {
                  color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [
                      { offset: 0, color: 'rgba(243, 84, 93, 0.3)' },
                      { offset: 1, color: 'rgba(243, 84, 93, 0.01)' }
                    ]
                  }
                },
                emphasis: {
                  focus: 'series',
                  itemStyle: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(243, 84, 93, 0.5)'
                  }
                },
                data: datosCompras
              },
              {
                name: 'Ventas',
                type: 'line',
                smooth: true,
                symbol: 'circle',
                symbolSize: 8,
                lineStyle: {
                  width: 3,
                  color: '#1dce6c'
                },
                itemStyle: {
                  color: '#1dce6c',
                  borderWidth: 2,
                  borderColor: '#fff'
                },
                areaStyle: {
                  color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [
                      { offset: 0, color: 'rgba(29, 206, 108, 0.3)' },
                      { offset: 1, color: 'rgba(29, 206, 108, 0.01)' }
                    ]
                  }
                },
                emphasis: {
                  focus: 'series',
                  itemStyle: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(29, 206, 108, 0.5)'
                  }
                },
                data: datosVentas
              }
            ]
          };
          
          chartFlujoDiario.setOption(option);
        });
      }
      
      // NOTA: Las funciones de gráficos ahora están en assets/js/dashboard-echarts.js
      // Las siguientes funciones son legacy y no se usan (se mantienen por referencia)
      
      /*
      // Gráfico 2: Compras por Material (LEGACY - NO SE USA)
      function cargarComprasPorMaterial_OLD() {
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
      */
      
      // Inicializar al cargar la página
      $(document).ready(function() {
        console.log('Dashboard con ECharts cargando...');
        
        inicializarFechas();
        cargarMaterialesFiltro();
        cargarSucursalesFiltro();
        
        // Cargar datos iniciales
        setTimeout(function() {
          console.log('Cargando datos del dashboard con ECharts...');
          cargarTodosLosDatos();
        }, 300);
        
        // Hacer los gráficos responsive
        window.addEventListener('resize', function() {
          if (chartFlujoDiario) chartFlujoDiario.resize();
          if (chartComprasMaterial) chartComprasMaterial.resize();
          if (chartVentasMaterial) chartVentasMaterial.resize();
          if (chartAnalisisSucursal) chartAnalisisSucursal.resize();
          if (chartTopProductos) chartTopProductos.resize();
          if (chartInventario) chartInventario.resize();
          if (chartEstadoTransacciones) chartEstadoTransacciones.resize();
        });
      });
    </script>
  </body>
</html>
