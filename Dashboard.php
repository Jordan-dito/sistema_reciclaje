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
        background: #23272f !important;
        color: #f1f3f7;
        font-family: 'Public Sans', 'Inter', Arial, sans-serif;
      }
      .bg-dark-card {
        background: #2c313a !important;
        color: #f1f3f7 !important;
        border: none;
        box-shadow: 0 8px 32px rgba(0,0,0,0.25);
      }
      .card-header.bg-primary,
      .card-header.bg-success,
      .card-header.bg-warning,
      .card-header.bg-info {
        background: #23272f !important;
        color: #f1f3f7 !important;
        border-bottom: 1px solid #343a40;
      }
      .card-header i {
        color: #a3a8b8 !important;
      }
      .navbar, .main-header {
        background: #23272f !important;
        color: #f1f3f7 !important;
      }
      .sidebar {
        background: #1a1d23 !important;
      }
      .btn, .btn-modern {
        background: #343a40 !important;
        color: #f1f3f7 !important;
        border: none;
      }
      .btn:hover, .btn-modern:hover {
        background: #495057 !important;
        color: #fff !important;
      }
      .form-control, .form-select {
        background: #23272f !important;
        color: #f1f3f7 !important;
        border: 1px solid #343a40;
      }
      .form-control:focus, .form-select:focus {
        background: #2c313a !important;
        color: #fff !important;
        border-color: #495057;
      }
      .info-tooltip {
        background: #343a40 !important;
        color: #f1f3f7 !important;
      }
      .badge {
        background: #495057 !important;
        color: #fff !important;
      }
      .table {
        background: #2c313a !important;
        color: #f1f3f7 !important;
      }
      .table th, .table td {
        border-color: #343a40 !important;
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
            <!-- Gráfico: Flujo Diario de Compras y Ventas -->
            <div class="row mb-4">
              <div class="col-md-12">
                <div class="card border-0 shadow-lg bg-dark-card rounded">
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

            <!-- Fila de Distribución: Compras e Inventario -->
            <!-- Sección de Distribución con Filtro Específico -->
            <div class="row mb-4">
              <div class="col-md-12">
                <div class="card border-0 shadow bg-dark-card rounded">
                  <div class="card-header bg-secondary text-white d-flex align-items-center">
                    <i class="fas fa-filter me-2"></i>
                    <h5 class="mb-0">Distribución de Datos (Filtro Específico)</h5>
                  </div>
                  <div class="card-body">
                    <div class="row mb-3">
                      <div class="col-md-4">
                        <label for="filtroDistribucionTipo" class="form-label">Tipo de Distribución</label>
                        <select class="form-control" id="filtroDistribucionTipo">
                          <option value="compras">Compras por Material</option>
                          <option value="inventario">Inventario por Categoría</option>
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label for="filtroDistribucionFecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="filtroDistribucionFecha" />
                      </div>
                      <div class="col-md-4">
                        <label for="filtroDistribucionSucursal" class="form-label">Sucursal</label>
                        <select class="form-control" id="filtroDistribucionSucursal">
                          <option value="">Todas las sucursales</option>
                        </select>
                      </div>
                    </div>
                    <div id="distribucionChart" style="height: 300px;"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Análisis por Sucursal -->
            <div class="row mb-4">
              <div class="col-md-12">
                <div class="card border-0 shadow bg-dark-card rounded">
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
      
      // Filtros locales por gráfico (sub-filtros opcionales)
      var filtrosLocales = {
        'card-comprasMaterial': { fechaInicio: '', fechaFin: '', sucursal: '', metrica: 'monto' },
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
      
      // Cargar materiales para el filtro
      function cargarMaterialesFiltro() {
        $.ajax({
          url: 'materiales/api.php?action=listar&estado=activos',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var select = $('#filtroMaterial');
              // Forzar opción por defecto al recargar listado
              select.empty().append('<option value="">Todos los materiales</option>');
              response.data.forEach(function(material) {
                select.append($('<option>', {
                  value: material.nombre,
                  text: material.nombre
                }));
              });
              // Mantener "Todos los materiales" como valor inicial
              select.val('');
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
              var selectsLocales = $('.chart-card .filter-sucursal');
              
              response.data.forEach(function(sucursal) {
                var option = $('<option>', {
                  value: sucursal.id,
                  text: sucursal.nombre
                });
                select.append(option);
                selectsLocales.each(function() {
                  $(this).append(option.clone());
                });
              });
              
              setTimeout(actualizarEtiquetasSucursal, 100);
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
      
      // Función para actualizar etiquetas de sucursal
      function actualizarEtiquetasSucursal() {
        var nombreSucursal = $('#filtroSucursal option:selected').text();
        var valorSucursal = $('#filtroSucursal').val();
        
        if (!valorSucursal || valorSucursal === "") {
          nombreSucursal = "Todas las Sucursales";
        }
        
        $('.info-sucursal').text(nombreSucursal);
        
        // También actualizar en opciones de ECharts si es necesario
        // Pero con las etiquetas HTML ya debería ser suficiente visualmente
      }

      // Aplicar filtros
      $('#btnAplicarFiltros').click(function() {
        filtrosActuales = {
          fechaInicio: $('#filtroFechaInicio').val(),
          fechaFin: $('#filtroFechaFin').val(),
          material: $('#filtroMaterial').val(),
          sucursal: $('#filtroSucursal').val()
        };
        
        // Actualizar etiquetas visuales
        actualizarEtiquetasSucursal();
        
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
        
        filtrosLocales['card-comprasMaterial'] = { fechaInicio: '', fechaFin: '', sucursal: '', metrica: 'monto' };
        filtrosLocales['card-ventasMaterial'] = { fechaInicio: '', fechaFin: '', sucursal: '', metrica: 'monto' };
        filtrosLocales['card-inventario'] = { fechaInicio: '', fechaFin: '', sucursal: '' };
        
        $('#card-comprasMaterial .filter-fecha-inicio, #card-comprasMaterial .filter-fecha-fin').val('');
        $('#card-ventasMaterial .filter-fecha-inicio, #card-ventasMaterial .filter-fecha-fin').val('');
        $('#card-inventario .filter-fecha-inicio, #card-inventario .filter-fecha-fin, #card-inventario .filter-sucursal').val('');
        $('#mc-monto, #mv-monto').prop('checked', true);
        $('.filter-badge').hide();
        
        // Actualizar etiquetas visuales
        actualizarEtiquetasSucursal();
        
        cargarTodosLosDatos();
      });
      
      // Actualizar indicador visual de sub-filtro aplicado
      function actualizarBadgeFiltro(cardId) {
        var card = $('#' + cardId);
        var badge = card.find('.filter-badge');
        var local = filtrosLocales[cardId] || {};
        var tieneFiltro = Boolean(local.fechaInicio || local.fechaFin || local.sucursal || (local.metrica && local.metrica !== 'monto'));
        badge.toggle(tieneFiltro);
      }
      
      // Toggle panel de sub-filtros por gráfico
      $(document).on('click', '.chart-filter-toggle', function() {
        $(this).closest('.chart-card').find('.chart-filter-panel').first().slideToggle(160);
      });
      
      // Filtros individuales eliminados
      
      // Construir parámetros de URL (globales + sub-filtros locales)
      function buildQueryParams(cardIdOrExclude) {
        var params = [];
        var cardId = null;
        var excludeMaterial = false;
        
        if (typeof cardIdOrExclude === 'boolean') {
          excludeMaterial = cardIdOrExclude;
        } else if (typeof cardIdOrExclude === 'string') {
          cardId = cardIdOrExclude;
        }
        
        var f = Object.assign({}, filtrosActuales);
        // Solo usar filtros globales
        if (f.fechaInicio) params.push('fecha_desde=' + f.fechaInicio);
        if (f.fechaFin) params.push('fecha_hasta=' + f.fechaFin);
        if (f.material) params.push('material=' + encodeURIComponent(f.material));
        if (f.sucursal) params.push('sucursal_id=' + f.sucursal);
        return params.length > 0 ? '&' + params.join('&') : '';
      }
      
      // Función para recargar un gráfico específico
      function recargarGrafico(cardId) {
          mostrarLoadingCard(cardId);
          switch(cardId) {
              case 'card-flujoDiario': cargarFlujoDiario(); break;
              case 'card-comprasMaterial': cargarComprasPorMaterial(); break;
              case 'card-ventasMaterial': cargarVentasPorMaterial(); break;
              case 'card-analisisSucursal': cargarAnalisisPorSucursal(); break;
              case 'card-inventario': cargarInventarioPorCategoria(); break;
              // case 'card-topProductos': cargarTopProductos(); break;
              // case 'card-transacciones': cargarEstadoTransacciones(); break;
          }
          setTimeout(function() { ocultarLoadingCard(cardId); }, 500);
      }
      
      function mostrarLoadingCard(cardId) {
          $('#' + cardId).find('.card-body').css('opacity', '0.5');
      }
      
      function ocultarLoadingCard(cardId) {
          $('#' + cardId).find('.card-body').css('opacity', '1');
      }

      // Función principal para cargar todos los datos
      function cargarTodosLosDatos() {
        cargarEstadisticas();
        cargarFlujoDiario();
        cargarComprasPorMaterial();
        cargarAnalisisPorSucursal();
        cargarInventarioPorCategoria();
        // cargarTopProductos(); // Deshabilitado para simplificar vista
        // cargarEstadoTransacciones(); // Deshabilitado para simplificar vista
      }
      
      // Función para formatear números
      function formatearMoneda(valor) {
        return '$' + valor.toLocaleString('es-ES', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
      }
      
      // Cargar estadísticas globales (sin ID de card específico, usan filtros globales)
      function cargarEstadisticas() {
        var params = buildQueryParams();

        // Cargar compras
        $.ajax({
          url: 'compras/api.php?action=listar' + params,
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
              var totalCompras = 0;
              response.data.forEach(function(compra) {
                if (compra.estado !== 'cancelada') {
                  totalCompras += parseFloat(compra.total || 0);
                }
              });
              $('#statTotalCompras').text(formatearMoneda(totalCompras));
              $('#statTotalCompras').data('valor', totalCompras);
            } else {
              $('#statTotalCompras').text('Sin datos');
              $('#statTotalCompras').data('valor', 0);
            }
          },
          error: function() { $('#statTotalCompras').text('Sin datos'); $('#statTotalCompras').data('valor', 0); }
        });

        // Cargar ventas y calcular margen
        $.ajax({
          url: 'ventas/api.php?action=listar' + params,
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
              var totalVentas = 0;
              response.data.forEach(function(venta) {
                if (venta.estado !== 'cancelada') {
                  totalVentas += parseFloat(venta.total || 0);
                }
              });
              $('#statTotalVentas').text(formatearMoneda(totalVentas));
              var totalCompras = $('#statTotalCompras').data('valor') || 0;
              var ganancia = totalVentas - totalCompras;
              var margen = totalVentas > 0 ? ((ganancia / totalVentas) * 100) : 0;
              $('#statGanancia').text(formatearMoneda(ganancia));
              $('#statMargen').text(margen.toFixed(1) + '%');
              var badgeMargen = $('#badgeMargen');
              badgeMargen.removeClass('bg-success bg-warning bg-danger');
              if (margen >= 30) {
                badgeMargen.addClass('bg-success').text('Excelente').show();
              } else if (margen >= 15) {
                badgeMargen.addClass('bg-warning').text('Bueno').show();
              } else if (margen > 0) {
                badgeMargen.addClass('bg-danger').text('Bajo').show();
              } else {
                badgeMargen.hide();
              }
            } else {
              $('#statTotalVentas').text('Sin datos');
              $('#statGanancia').text('Sin datos');
              $('#statMargen').text('Sin datos');
              $('#badgeMargen').hide();
            }
          },
          error: function() {
            $('#statTotalVentas').text('Sin datos');
            $('#statGanancia').text('Sin datos');
            $('#statMargen').text('Sin datos');
            $('#badgeMargen').hide();
          }
        });
      }

      // Gráfico 1: Flujo Diario de Compras y Ventas con ECharts
      function cargarFlujoDiario() {
        var params = buildQueryParams(false); // Aplica filtro material
        
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
                type: 'pie', // Cambio a pie para consistencia con compras/ventas
                data: {
                  labels: labels,
                  datasets: [{
                    data: valores,
                    backgroundColor: coloresProfesionales.slice(0, labels.length),
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
                                var label = context.label || '';
                                var value = context.parsed;
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var percentage = ((value / total) * 100).toFixed(1) + "%";
                                return label + ': ' + value + ' (' + percentage + ')';
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
          
          // Verificar si hay una sucursal preseleccionada (para usuarios con sucursal fija)
          var sucursalInicial = $('#filtroSucursal').val();
          if (sucursalInicial) {
             filtrosActuales.sucursal = sucursalInicial;
          }
          actualizarEtiquetasSucursal();
          
          cargarTodosLosDatos();
        }, 300);
        
        // Hacer los gráficos responsive
        window.addEventListener('resize', function() {
          if (chartFlujoDiario) chartFlujoDiario.resize();
          if (chartComprasMaterial) chartComprasMaterial.resize();
          if (chartVentasMaterial) chartVentasMaterial.resize();
          if (chartAnalisisSucursal) chartAnalisisSucursal.resize();
          if (chartInventario) chartInventario.resize();
        });
      });
    </script>
  </body>
</html>
