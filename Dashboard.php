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
      body {
        background: #f8f9fa !important;
        color: #23272f;
        font-family: 'Public Sans', 'Inter', Arial, sans-serif;
      }
      .bg-dark-card {
        background: #fff !important;
        color: #23272f !important;
        border: 1px solid #e3e6ea;
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
      }
      .card-header.bg-primary,
      .card-header.bg-success,
      .card-header.bg-warning,
      .card-header.bg-info {
        background: #f8f9fa !important;
        color: #23272f !important;
        border-bottom: 1px solid #e3e6ea;
      }
      .card-header i {
        color: #495057 !important;
      }
      .navbar, .main-header {
        background: #fff !important;
        color: #23272f !important;
      }
      .sidebar {
        background: #f8f9fa !important;
      }
      .btn, .btn-modern {
        background: #e3e6ea !important;
        color: #23272f !important;
        border: none;
      }
      .btn:hover, .btn-modern:hover {
        background: #dee2e6 !important;
        color: #23272f !important;
      }
      .form-control, .form-select {
        background: #fff !important;
        color: #23272f !important;
        border: 1px solid #e3e6ea;
      }
      .form-control:focus, .form-select:focus {
        background: #f8f9fa !important;
        color: #23272f !important;
        border-color: #adb5bd;
      }
      .info-tooltip {
        background: #e3e6ea !important;
        color: #23272f !important;
      }
      .badge {
        background: #adb5bd !important;
        color: #fff !important;
      }
      .table {
        background: #fff !important;
        color: #23272f !important;
      }
      .table th, .table td {
        border-color: #e3e6ea !important;
      }
      .dashboard-panel {
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 14px !important;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      }
      .dashboard-panel .card-body {
        padding: 1.25rem;
      }
      .kpi-mini-card {
        background: #fff;
        border: 1px solid #eef2f6;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
      }
      .kpi-mini-card:hover {
        transform: translateY(-2px);
        border-color: rgba(102, 126, 234, 0.3);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.06);
      }
      .kpi-mini-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(102, 126, 234, 0.1);
        color: #5c7cfa;
        font-size: 0.95rem;
      }
      .kpi-mini-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #6c757d;
        font-weight: 600;
      }
      .kpi-mini-value {
        margin: 0;
        font-size: 1.45rem;
        font-weight: 700;
        color: #212529;
        line-height: 1.1;
      }
      .section-title {
        color: #495057;
        font-size: 0.92rem;
        font-weight: 700;
        letter-spacing: 0.45px;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
        padding-left: 0.25rem;
        margin-top: 1.5rem;
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
                        <select class="form-control" id="filtroMaterial" disabled>
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

            <div class="section-title animate-in" style="animation-delay: 0.45s;">Indicadores del sistema</div>
            <!-- Indicadores fijos (no dependen de filtros) -->
            <div class="row g-3 mb-4 animate-in" style="animation-delay: 0.5s;">
              <div class="col-sm-6 col-lg-3">
                <div class="card kpi-mini-card h-100">
                  <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="kpi-mini-label">Usuarios registrados</div>
                      <span class="kpi-mini-icon"><i class="fas fa-users"></i></span>
                    </div>
                    <h4 class="kpi-mini-value" id="statUsuariosRegistrados">0</h4>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="card kpi-mini-card h-100">
                  <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="kpi-mini-label">Usuarios activos</div>
                      <span class="kpi-mini-icon"><i class="fas fa-user-check"></i></span>
                    </div>
                    <h4 class="kpi-mini-value text-success" id="statUsuariosActivos">0</h4>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="card kpi-mini-card h-100">
                  <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="kpi-mini-label">Sucursales activas</div>
                      <span class="kpi-mini-icon"><i class="fas fa-store"></i></span>
                    </div>
                    <h4 class="kpi-mini-value text-info" id="statSucursalesActivas">0</h4>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="card kpi-mini-card h-100">
                  <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <div class="kpi-mini-label">Usuarios con sucursal</div>
                      <span class="kpi-mini-icon"><i class="fas fa-user-tag"></i></span>
                    </div>
                    <h4 class="kpi-mini-value text-warning" id="statUsuariosConSucursal">0</h4>
                  </div>
                </div>
              </div>
            </div>

            <div class="section-title animate-in" style="animation-delay: 0.55s;">Analítica</div>
            <!-- Gráfico: Ingresos y Costos por Año -->
            <div class="row mb-4">
              <div class="col-md-12">
                <div class="card border-0 shadow-lg bg-dark-card rounded dashboard-panel">
                  <div class="card-header bg-warning text-dark d-flex align-items-center">
                    <i class="fas fa-chart-area me-2"></i>
                    <h5 class="mb-0">Ingresos y Costos</h5>
                  </div>
                  <div class="card-body">
                    <!-- Filtros para el gráfico de ingresos y costos (Independientes) -->
                    <div class="row g-2 align-items-end mb-3">
                      <div class="col-md-4">
                        <label for="filtroSucursalIC" class="form-label mb-0">Sucursal</label>
                        <select id="filtroSucursalIC" class="form-select">
                          <option value="">Todas</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label for="filtroAnioIC" class="form-label mb-0">Año</label>
                        <select id="filtroAnioIC" class="form-select">
                          <option value="">Todos</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label for="filtroMesIC" class="form-label mb-0">Mes</label>
                        <select id="filtroMesIC" class="form-select">
                          <option value="">Todos</option>
                          <option value="1">Enero</option>
                          <option value="2">Febrero</option>
                          <option value="3">Marzo</option>
                          <option value="4">Abril</option>
                          <option value="5">Mayo</option>
                          <option value="6">Junio</option>
                          <option value="7">Julio</option>
                          <option value="8">Agosto</option>
                          <option value="9">Septiembre</option>
                          <option value="10">Octubre</option>
                          <option value="11">Noviembre</option>
                          <option value="12">Diciembre</option>
                        </select>
                      </div>
                    </div>
                    <div id="ingresosCostosAnioChart" style="height: 350px;"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Gráfico: Flujo Diario de Compras y Ventas -->
            <div class="row mb-4">
              <div class="col-md-12">
                <div class="card border-0 shadow-lg bg-dark-card rounded dashboard-panel">
                  <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-chart-line me-2"></i>
                    <h5 class="mb-0">Flujo Diario de Compras y Ventas</h5>
                  </div>
                  <div class="card-body">
                    <div id="flujoDiarioChart" style="height: 350px;"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Análisis por Sucursal -->
            <div class="row mb-4">
              <div class="col-md-12">
                <div class="card border-0 shadow bg-dark-card rounded dashboard-panel">
                  <div class="card-header bg-info text-white d-flex align-items-center">
                    <i class="fas fa-store-alt me-2"></i>
                    <h5 class="mb-0">Análisis por Sucursal</h5>
                  </div>
                  <div class="card-body">
                    <div id="analisisSucursalChart" style="height: 350px;"></div>
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

    <!-- Sweet Alert -->
    <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="assets/js/kaiadmin.min.js"></script>

    <?php
      $basePath = '';
      include __DIR__ . '/includes/footer-scripts.php';
    ?>
    <script>
      // Variables globales para los gráficos ECharts
      var chartFlujoDiario = null;
      var chartComprasMaterial = null; // Mantenido pero no usado en UI
      var chartVentasMaterial = null;
      var chartAnalisisSucursal = null;
      var chartTopProductos = null;
      var chartInventario = null;
      var chartEstadoTransacciones = null;
      var chartIngresosCostos = null; // Para el gráfico de ingresos/costos
      
      // Paleta de colores profesional y moderna
      var coloresProfesionales = [
        '#667eea', '#764ba2', '#f093fb', '#4facfe',
        '#43e97b', '#fa709a', '#fee140', '#30cfd0',
        '#a8edea', '#fed6e3', '#c471f5', '#fa7cbb',
        '#f38181', '#aa96da', '#fcbad3', '#ffffd2'
      ];
      
      // Filtros actuales (Globales)
      var filtrosActuales = {
        fechaInicio: '',
        fechaFin: '',
        material: '',
        sucursal: ''
      };
      
      // Filtros locales por gráfico (sub-filtros opcionales)
      var filtrosLocales = {
        'card-ventasMaterial': { fechaInicio: '', fechaFin: '', sucursal: '', metrica: 'monto' },
        'card-inventario': { fechaInicio: '', fechaFin: '', sucursal: '' }
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
      
      // Cargar materiales para el filtro global
      function cargarMaterialesFiltro() {
        $.ajax({
          url: 'materiales/api.php?action=listar&estado=activos',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var select = $('#filtroMaterial');
              select.empty().append('<option value="">Todos los materiales</option>');
              response.data.forEach(function(material) {
                select.append($('<option>', {
                  value: material.nombre,
                  text: material.nombre
                }));
              });
              select.val('');
            }
          }
        });
      }
      
      // Cargar sucursales para el filtro global
      function cargarSucursalesFiltro() {
        $.ajax({
          url: 'sucursales/api.php?action=activas',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var select = $('#filtroSucursal');
              select.empty().append('<option value="">Todas las sucursales</option>');
              response.data.forEach(function(sucursal) {
                select.append($('<option>', {
                  value: sucursal.id,
                  text: sucursal.nombre
                }));
              });
              setTimeout(actualizarEtiquetasSucursal, 100);
            }
          }
        });
      }
      
      // Funciones para gráfico Ingresos y Costos (INDEPENDIENTE)
      function cargarSucursalesFiltroIC() {
        $.ajax({
          url: 'sucursales/api.php?action=activas',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var select = $('#filtroSucursalIC');
              select.empty().append('<option value="">Todas</option>');
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

      function cargarAniosFiltroIC() {
        $.ajax({
            url: 'dashboard/api_ingresos_costos.php',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var anios = [];
              response.data.forEach(function(d) {
                if (d.periodo && (!response.labelType || response.labelType === 'anio')) anios.push(d.periodo);
              });
              // Si no hay datos, al menos poner el año actual
              if (anios.length === 0) anios.push(new Date().getFullYear());
              
              anios = [...new Set(anios)];
              var select = $('#filtroAnioIC');
              select.empty().append('<option value="">Todos</option>');
              anios.forEach(function(anio) {
                select.append($('<option>', { value: anio, text: anio }));
              });
            }
          }
        });
      }

      function cargarIngresosCostosAnio() {
        var sucursal = $('#filtroSucursalIC').val();
        var anio = $('#filtroAnioIC').val();
        var mes = $('#filtroMesIC').val();
        
        // Mostrar estado de carga
        if (chartIngresosCostos) {
            chartIngresosCostos.showLoading();
        }
        
        $.ajax({
            url: 'dashboard/api_ingresos_costos.php',
          method: 'GET',
          data: { sucursal_id: sucursal, anio: anio, mes: mes },
          dataType: 'json',
          success: function(response) {
            var dom = document.getElementById('ingresosCostosAnioChart');
            if (!dom) return;
            
            if (!chartIngresosCostos) {
              chartIngresosCostos = echarts.init(dom);
            }
            chartIngresosCostos.hideLoading();
            
            if (response.success && response.data) {
              var labelType = response.labelType;
              var labels = response.data.map(d => {
                if (d.periodo) return d.periodo;
                return '';
              });
              
              if (labelType === 'mes') {
                labels = labels.map(function(m) {
                  const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                  return meses[m-1] || m;
                });
              }
              if (labelType === 'dia') {
                labels = labels.map(d => 'Día ' + d);
              }
              
              var ventas = response.data.map(d => d.ventas);
              var compras = response.data.map(d => d.compras);

              var option = {
                tooltip: { trigger: 'axis' },
                legend: {
                  data: ['Ventas', 'Ingresos'],
                  bottom: 0
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '10%',
                    containLabel: true
                },
                xAxis: {
                  type: 'category',
                  data: labels
                },
                yAxis: {
                  type: 'value',
                  axisLabel: {
                    formatter: function(value) {
                      return '$' + value;
                    }
                  }
                },
                series: [
                  {
                    name: 'Ventas',
                    type: 'line',
                    data: ventas,
                    smooth: true,
                    lineStyle: { color: '#1dce6c', width: 3 },
                    itemStyle: { color: '#1dce6c' },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                            offset: 0,
                            color: 'rgba(29, 206, 108, 0.3)'
                        }, {
                            offset: 1,
                            color: 'rgba(29, 206, 108, 0.01)'
                        }])
                    }
                  },
                  {
                    name: 'Ingresos', // En realidad costos/compras en BD, pero etiquetado como se pida
                    type: 'line',
                    data: compras,
                    smooth: true,
                    lineStyle: { color: '#f3545d', width: 3 },
                    itemStyle: { color: '#f3545d' },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                            offset: 0,
                            color: 'rgba(243, 84, 93, 0.3)'
                        }, {
                            offset: 1,
                            color: 'rgba(243, 84, 93, 0.01)'
                        }])
                    }
                  }
                ]
              };
              chartIngresosCostos.setOption(option, true);
            }
          },
          error: function() {
              if (chartIngresosCostos) chartIngresosCostos.hideLoading();
          }
        });
      }

      // Listeners para filtros independientes de Ingresos/Costos
      $(document).on('change', '#filtroSucursalIC, #filtroAnioIC, #filtroMesIC', function() {
        var anio = $('#filtroAnioIC').val();
        var mes = $('#filtroMesIC').val();
        if (!anio && mes) {
          $('#filtroMesIC').val('');
          swal('Advertencia', 'Debe seleccionar un año antes de elegir un mes.', 'warning');
          return;
        }
        cargarIngresosCostosAnio();
      });
      
      // Función para mostrar loading en tarjetas
      function mostrarLoading() {
        $('.stat-value').html('<div class="skeleton" style="height: 30px; width: 100px;"></div>');
        $('.chart-container canvas').css('opacity', '0.3');
      }
      
      function ocultarLoading() {
        $('.chart-container canvas').css('opacity', '1');
      }
      
      // Función para actualizar etiquetas de sucursal (Filtro Global)
      function actualizarEtiquetasSucursal() {
        var nombreSucursal = $('#filtroSucursal option:selected').text();
        var valorSucursal = $('#filtroSucursal').val();
        
        if (!valorSucursal || valorSucursal === "") {
          nombreSucursal = "Todas las Sucursales";
        }
        $('.info-sucursal').text(nombreSucursal);
      }

      // Aplicar filtros GLOBALES
      $('#btnAplicarFiltros').click(function() {
        filtrosActuales = {
          fechaInicio: $('#filtroFechaInicio').val(),
          fechaFin: $('#filtroFechaFin').val(),
          material: $('#filtroMaterial').val(),
          sucursal: $('#filtroSucursal').val()
        };
        
        actualizarEtiquetasSucursal();
        
        if (!filtrosActuales.fechaInicio || !filtrosActuales.fechaFin) {
          swal('Error', 'Debe seleccionar fecha de inicio y fin', 'error');
          return;
        }
        
        if (new Date(filtrosActuales.fechaInicio) > new Date(filtrosActuales.fechaFin)) {
          swal('Error', 'La fecha de inicio no puede ser mayor a la fecha fin', 'error');
          return;
        }
        
        var btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Cargando...').prop('disabled', true);
        mostrarLoading();
        
        cargarTodosLosDatos();
        
        setTimeout(() => {
          btn.html('<i class="fas fa-search me-2"></i>Aplicar').prop('disabled', false);
          ocultarLoading();
        }, 1000);
      });
      
      // Limpiar filtros GLOBALES
      $('#btnLimpiarFiltros').click(function() {
        inicializarFechas();
        $('#filtroMaterial').val('');
        $('#filtroSucursal').val('');
        
        filtrosActuales.material = '';
        filtrosActuales.sucursal = '';
        
        // Resetear locales
        filtrosLocales['card-ventasMaterial'] = { fechaInicio: '', fechaFin: '', sucursal: '', metrica: 'monto' };
        filtrosLocales['card-inventario'] = { fechaInicio: '', fechaFin: '', sucursal: '' };
        
        $('#card-ventasMaterial .filter-fecha-inicio, #card-ventasMaterial .filter-fecha-fin').val('');
        $('#card-inventario .filter-fecha-inicio, #card-inventario .filter-fecha-fin, #card-inventario .filter-sucursal').val('');
        $('.filter-badge').hide();
        
        actualizarEtiquetasSucursal();
        cargarTodosLosDatos();
      });
      
      // Indicadores generales del sistema (sin filtros)
      function cargarIndicadoresSistema() {
        Promise.all([
          $.ajax({ url: 'usuarios/api.php?action=listar', method: 'GET', dataType: 'json' }),
          $.ajax({ url: 'sucursales/api.php?action=listar', method: 'GET', dataType: 'json' })
        ]).then(function([usuariosResp, sucursalesResp]) {
          var usuarios = (usuariosResp && usuariosResp.success && Array.isArray(usuariosResp.data)) ? usuariosResp.data : [];
          var sucursales = (sucursalesResp && sucursalesResp.success && Array.isArray(sucursalesResp.data)) ? sucursalesResp.data : [];

          var usuariosRegistrados = usuarios.length;
          var usuariosActivos = usuarios.filter(function(u) {
            return String(u.estado || '').toLowerCase() === 'activo';
          }).length;
          var usuariosConSucursal = usuarios.filter(function(u) {
            return u.sucursal_id !== null && u.sucursal_id !== undefined && String(u.sucursal_id) !== '';
          }).length;
          var sucursalesActivas = sucursales.filter(function(s) {
            return String(s.estado || '').toLowerCase() === 'activa';
          }).length;

          $('#statUsuariosRegistrados').text(usuariosRegistrados.toLocaleString('es-ES'));
          $('#statUsuariosActivos').text(usuariosActivos.toLocaleString('es-ES'));
          $('#statUsuariosConSucursal').text(usuariosConSucursal.toLocaleString('es-ES'));
          $('#statSucursalesActivas').text(sucursalesActivas.toLocaleString('es-ES'));
        }).catch(function() {
          $('#statUsuariosRegistrados, #statUsuariosActivos, #statUsuariosConSucursal, #statSucursalesActivas').text('0');
        });
      }

      // Función principal para cargar datos dependientes de filtros globales
      function cargarTodosLosDatos() {
        cargarIndicadoresSistema(); // Fijo
        cargarEstadisticas();       // Depende de filtros globales
        cargarFlujoDiario();        // Depende de filtros globales
        cargarAnalisisPorSucursal();// Depende de filtros globales
        cargarInventarioPorCategoria(); // Depende de filtros globales
      }
      
      // Inicializar todo
      $(document).ready(function() {
          // 1. Cargar opciones de filtros independientes
          cargarSucursalesFiltroIC();
          cargarAniosFiltroIC();
          // 2. Cargar gráfico independiente
          setTimeout(cargarIngresosCostosAnio, 500); 
          
          // 3. Inicializar filtros globales
          inicializarFechas();
          cargarMaterialesFiltro();
          cargarSucursalesFiltro();
          
          // 4. Cargar datos globales iniciales
          setTimeout(function() {
            var sucursalInicial = $('#filtroSucursal').val();
            if (sucursalInicial) {
               filtrosActuales.sucursal = sucursalInicial;
            }
            actualizarEtiquetasSucursal();
            cargarTodosLosDatos();
          }, 600);
          
          // Responsive
          window.addEventListener('resize', function() {
            if (chartFlujoDiario) chartFlujoDiario.resize();
            if (chartAnalisisSucursal) chartAnalisisSucursal.resize();
            if (chartInventario) chartInventario.resize();
            if (chartIngresosCostos) chartIngresosCostos.resize();
          });
      });
    </script>
  </body>
</html>
