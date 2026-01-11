<?php
/**
 * Control de Asistencia - Frontend
 * Sistema de Gestión de Reciclaje
 */
require_once __DIR__ . '/../config/auth.php';
$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
$db = getDB();

$usuario = $auth->getCurrentUser();
$usuarioNombre = $usuario['nombre'] ?? 'Usuario';

// Obtener sucursales para el filtro
$sucursales = [];
try {
    $stmtSuc = $db->query("SELECT id, nombre FROM sucursales ORDER BY nombre");
    $sucursales = $stmtSuc->fetchAll();
} catch (Exception $e) {
    // ignore
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Control de Asistencia - Sistema de Reciclaje</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/logo.jpg" type="image/jpeg" />

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
        .day-card { 
            border: 2px solid #ebedf2; 
            border-radius: 12px; 
            padding: 15px 10px; 
            text-align: center; 
            cursor: pointer; 
            transition: all 0.3s ease;
            background: #fff;
            margin-bottom: 10px;
        }
        .day-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .day-card.active { 
            background: linear-gradient(135deg, #1572e8 0%, #064ea3 100%); 
            color: white; 
            border-color: transparent; 
        }
        .day-card input { display: none; }
        .chart-container { position: relative; height: 300px; }
        .card-stats .icon-big { font-size: 2.5rem; }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <?php $basePath = '..'; include __DIR__ . '/../includes/sidebar-logo.php'; ?>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <?php 
                      $basePath = '..'; 
                      $currentRoute = 'asistencia_personal'; 
                      include __DIR__ . '/../includes/sidebar.php'; 
                    ?>
                </div>
            </div>
        </div>

        <div class="main-panel">
            <div class="main-header">
                <?php $basePath = '..'; include __DIR__ . '/../includes/main-header-logo.php'; ?>
                <?php 
                  $basePath = '..'; 
                  include __DIR__ . '/../includes/user-header.php'; 
                  include __DIR__ . '/../includes/modal-foto-perfil.php';
                  include __DIR__ . '/../includes/modal-cambiar-password.php';
                ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">Control de Asistencia</h3>
                            <h6 class="op-7 mb-2">Configuración de jornada y reportes de puntualidad</h6>
                        </div>
                    </div>

                    <!-- Configuración de Días Laborables -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="card-title">Configuración de Jornada Semanal</div>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-4">Seleccione los días de la semana en los que el personal debe registrar asistencia:</p>
                                    <div class="row">
                                        <?php 
                                        $dias = [
                                            ['n' => 'Lunes', 'val' => 'lun'],
                                            ['n' => 'Martes', 'val' => 'mar'],
                                            ['n' => 'Miércoles', 'val' => 'mie'],
                                            ['n' => 'Jueves', 'val' => 'jue'],
                                            ['n' => 'Viernes', 'val' => 'vie'],
                                            ['n' => 'Sábado', 'val' => 'sab'],
                                            ['n' => 'Domingo', 'val' => 'dom']
                                        ];
                                        foreach($dias as $dia): ?>
                                            <div class="col-6 col-sm-4 col-md-1-7" style="flex: 0 0 14.28%; max-width: 14.28%;">
                                                <label class="day-card w-100 <?php echo $dia['val'] != 'dom' ? 'active' : ''; ?>">
                                                    <input type="checkbox" name="laborales[]" value="<?php echo $dia['val']; ?>" <?php echo $dia['val'] != 'dom' ? 'checked' : ''; ?>>
                                                    <div class="fw-bold"><?php echo $dia['n']; ?></div>
                                                    <small><?php echo $dia['val'] != 'dom' ? 'Laborable' : 'Descanso'; ?></small>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="mt-3 text-end">
                                        <button class="btn btn-primary" onclick="swal('¡Éxito!', 'Jornada actualizada correctamente', 'success')">
                                            <i class="fas fa-save me-2"></i> Guardar Calendario
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Rápidas -->
                    <div class="row">
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                <i class="fas fa-user-check"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Asistencias Hoy</p>
                                                <h4 class="card-title">32</h4>
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
                                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                                <i class="fas fa-user-times"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Faltas Hoy</p>
                                                <h4 class="card-title">5</h4>
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
                                                <i class="fas fa-clock"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Atrasos</p>
                                                <h4 class="card-title">8</h4>
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
                                                <i class="fas fa-percentage"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Eficiencia</p>
                                                <h4 class="card-title">92%</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-body">
                                    <form class="row align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Sucursal</label>
                                            <select class="form-select" id="filtroSucursal">
                                                <option value="">Todas las sucursales</option>
                                                <?php foreach($sucursales as $s): ?>
                                                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Desde</label>
                                            <input type="date" class="form-control" value="<?php echo date('Y-m-d', strtotime('-7 days')); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Hasta</label>
                                            <input type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-secondary w-100">
                                                <i class="fas fa-search"></i> Filtrar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="card-title">Histórico de Asistencia (Últimos 7 días)</div>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="asistenciaChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="card-title">Distribución de Hoy</div>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="height: 250px;">
                                        <canvas id="pieAsistencia"></canvas>
                                    </div>
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Asistieron</span>
                                            <span class="fw-bold">32</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Faltaron</span>
                                            <span class="fw-bold text-danger">5</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Con Permiso</span>
                                            <span class="fw-bold text-warning">3</span>
                                        </div>
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

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        // Manejo visual de los días laborables
        $('.day-card').click(function() {
            $(this).toggleClass('active');
            let checkbox = $(this).find('input');
            let isChecked = checkbox.prop('checked');
            checkbox.prop('checked', !isChecked);
            $(this).find('small').text(!isChecked ? 'Laborable' : 'Descanso');
        });

        // Gráfico de Barras (Histórico)
        var ctx = document.getElementById('asistenciaChart').getContext('2d');
        var asistenciaChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Lun 01', 'Mar 02', 'Mie 03', 'Jue 04', 'Vie 05', 'Sab 06', 'Dom 07'],
                datasets: [{
                    label: 'Asistencias',
                    backgroundColor: '#1572e8',
                    borderColor: '#1572e8',
                    data: [45, 42, 48, 40, 46, 38, 44]
                }, {
                    label: 'Faltas',
                    backgroundColor: '#f3545d',
                    borderColor: '#f3545d',
                    data: [5, 8, 2, 10, 4, 12, 6]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    y: { 
                        beginAtZero: true,
                        grid: { drawBorder: false, color: '#f1f1f1' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });

        // Gráfico de Pastel (Hoy)
        var ctxPie = document.getElementById('pieAsistencia').getContext('2d');
        var pieAsistencia = new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [32, 5, 3],
                    backgroundColor: ['#1572e8', '#f3545d', '#ffad46'],
                    borderWidth: 0
                }],
                labels: ['Asistieron', 'Faltaron', 'Permiso']
            },
            options: {
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '70%'
            }
        });
    </script>
</body>
</html>
