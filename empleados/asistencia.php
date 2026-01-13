<?php
/**
 * Control de Asistencia y Pagos Diarios - Frontend
 * Sistema de Gestión de Reciclaje
 */
require_once __DIR__ . '/../config/auth.php';

// Configurar Zona Horaria de Ecuador
date_default_timezone_set('America/Guayaquil');

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../index.php');
    exit;
}

$usuario = $auth->getCurrentUser();

// Lógica de fechas para la semana
// Si recibimos fecha por GET, la usamos. Si no, usamos HOY (Ecuador).
$fechaRef = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$timestamp = strtotime($fechaRef);

// Calcular el Lunes de esa semana
// "w" devuelve 0 (Domingo) a 6 (Sábado)
$diaSemana = date('w', $timestamp);

if ($diaSemana == 0) { // Si es Domingo
    // El lunes de "esta semana" para PHP sería mañana.
    // Nosotros queremos el lunes de la semana que está terminando.
    $lunesTimestamp = strtotime('monday last week', $timestamp);
} else {
    // Si es Lun-Sab, buscamos el lunes de esta misma semana
    $lunesTimestamp = strtotime('monday this week', $timestamp);
}
$domingoTimestamp = strtotime('sunday this week', $lunesTimestamp);

// Generar array con los 7 días de esa semana para las cabeceras
$diasNombres = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'];
$diasKeys = ['lun', 'mar', 'mie', 'jue', 'vie', 'sab', 'dom'];
$fechasSemana = [];

for($i = 0; $i < 7; $i++) {
    $fechaD = date('Y-m-d', strtotime("+$i days", $lunesTimestamp));
    $fechasSemana[] = [
        'nombre' => $diasNombres[$i],
        'key' => $diasKeys[$i],
        'n' => date('d', strtotime($fechaD)), // Solo el día (ej: 12)
        'full' => $fechaD // Fecha completa YYYY-MM-DD
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Asistencia y Pagos - Sistema de Reciclaje</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/logo.jpg" type="image/jpeg" />
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
          urls: ["../assets/css/fonts.min.css"],
        },
        active: function () { sessionStorage.fonts = true; },
      });
    </script>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <style>
        .day-card { 
            border: 2px dashed #d1d1d1;
            border-radius: 15px; 
            padding: 15px 5px; 
            text-align: center; 
            cursor: pointer; 
            transition: all 0.2s;
            background: #f8f9fa;
            color: #888;
            margin-bottom: 10px;
        }
        .day-card:hover { background: #e9ecef; transform: translateY(-2px); }
        .day-card.active { 
            background: linear-gradient(135deg, #1572e8 0%, #064ea3 100%); 
            color: white; 
            border: 2px solid #1572e8;
            box-shadow: 0 4px 15px rgba(21, 114, 232, 0.4);
        }
        .day-card.active .status-text { color: rgba(255,255,255, 0.9); font-weight: 500; }
        .day-card input { display: none; }
        
        .day-off { 
            background-color: #f2f2f2 !important; 
            background-image: linear-gradient(45deg, #e6e6e6 25%, transparent 25%, transparent 50%, #e6e6e6 50%, #e6e6e6 75%, transparent 75%, transparent);
            background-size: 10px 10px;
        }
        
        .cell-dia { 
            height: 60px; 
            position: relative; 
            vertical-align: middle !important;
        }
        .cell-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-pago-dia {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 1px solid #ddd;
            background: white;
            color: #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.8rem;
        }
        .btn-pago-dia:hover {
            background: #f0f0f0;
            color: #666;
        }
        .btn-pago-dia.pagado {
            background: #31ce36;
            color: white;
            border-color: #31ce36;
            box-shadow: 0 2px 5px rgba(49, 206, 54, 0.3);
        }
        .btn-pago-dia.pendiente {
            color: #ffad46;
            border-color: #ffad46;
        }
        
        .no-asistencia .btn-pago-dia { display: none; }
        .total-money { font-weight: bold; color: #31ce36; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="sidebar" data-background-color="dark">
            <?php $basePath = '..'; include __DIR__ . '/../includes/sidebar-logo.php'; ?>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <?php $basePath = '..'; $currentRoute = 'asistencia_personal'; include __DIR__ . '/../includes/sidebar.php'; ?>
                </div>
            </div>
        </div>

        <div class="main-panel">
            <div class="main-header">
                <?php $basePath = '..'; include __DIR__ . '/../includes/main-header-logo.php'; ?>
                <?php $basePath = '..'; include __DIR__ . '/../includes/user-header.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">Control de Asistencia y Pagos</h3>
                            <h6 class="op-7 mb-2">Gestión de asistencia y pagos</h6>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0 d-flex align-items-center">
                            <label class="me-2 fw-bold">Semana del:</label>
                            <!-- El input type="date" mostrará la fecha seleccionada. Al cambiar, recarga la página. -->
                            <input type="date" class="form-control form-control-sm me-2" 
                                   value="<?php echo $fechaRef; ?>" 
                                   onchange="location.href='?fecha='+this.value">
                            
                            <span class="badge badge-primary px-3">
                                <?php echo date('d M', $lunesTimestamp); ?> - <?php echo date('d M', $domingoTimestamp); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Configuración de Días Laborables -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="card-title">Configuración de Jornada</div>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="container-config-dias">
                                        <?php foreach($fechasSemana as $f): ?>
                                            <div class="col" style="flex: 0 0 14.28%;">
                                                <label class="day-card w-100" id="card-<?php echo $f['key']; ?>">
                                                    <input type="checkbox" class="config-day" 
                                                           id="check-config-<?php echo $f['key']; ?>"
                                                           value="<?php echo $f['key']; ?>">
                                                    <div class="fw-bold"><?php echo $f['nombre']; ?></div>
                                                    <!-- Fecha pequeña debajo del día -->
                                                    <small style="font-size: 0.7rem; color: #999;"><?php echo $f['n']; ?></small>
                                                    <div class="status-text mt-1" style="font-size: 0.65rem;">...</div>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Planilla -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-header d-flex align-items-center">
                                    <h4 class="card-title">Planilla de Pagos Diarios</h4>
                                    <div class="ms-auto">
                                        <small class="text-muted me-2"><i class="fas fa-check-square"></i> Asistencia</small>
                                        <small class="text-muted me-2"><i class="fas fa-dollar-sign text-warning"></i> Por Pagar</small>
                                        <small class="text-muted"><i class="fas fa-check-circle text-success"></i> Pagado</small>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-attendance">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 250px">Empleado / Caja</th>
                                                    <?php foreach($fechasSemana as $f): ?>
                                                        <th class="text-center col-dia-<?php echo $f['key']; ?>">
                                                            <?php echo $f['nombre']; ?>
                                                            <span class="col-fecha"><?php echo $f['n']; ?></span>
                                                        </th>
                                                    <?php endforeach; ?>
                                                    <th class="text-center">Pagado Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabla-body">
                                                <tr><td colspan="9" class="text-center p-4">Cargando datos...</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pago -->
    <div class="modal fade" id="modalPago" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="modalPagoTitle">Registrar Pago</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="pago_emp_id">
                    <input type="hidden" id="pago_fecha">
                    
                    <div class="form-group text-center">
                        <label>Monto a Pagar ($)</label>
                        <input type="number" class="form-control form-control-lg text-center font-weight-bold" id="pago_monto" step="0.01">
                        <small class="form-text text-muted mt-2">Este valor se descontará de la caja de la sucursal.</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger btn-sm me-auto" id="btnEliminarPago" style="display:none">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button type="button" class="btn btn-success" onclick="confirmarPago()">Guardar Pago</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    
    <script>
        const lunesSemana = '<?php echo date('Y-m-d', $lunesTimestamp); ?>';
        // Map de fechas PHP a JS
        const fechasSemanaMap = {
            <?php foreach($fechasSemana as $f): ?>
            '<?php echo $f['key']; ?>': '<?php echo $f['full']; ?>',
            <?php endforeach; ?>
        };
        const diasKeys = ['lun', 'mar', 'mie', 'jue', 'vie', 'sab', 'dom'];

        $(document).ready(function() {
            // Cargar datos vía AJAX
            cargarDatosSemana();

            // Listeners
            $(document).on('click', '.day-card', function(e) {
                if($(e.target).is('input')) return;
                toggleDiaConfig($(this).find('input').val());
            });

            $(document).on('change', '.check-asistencia', function() {
                guardarAsistencia($(this));
            });

            $('#btnEliminarPago').click(function() {
                eliminarPagoActual();
            });
        });

        function cargarDatosSemana() {
            $.ajax({
                url: 'api.php',
                data: { action: 'get_semana', fecha: lunesSemana },
                dataType: 'json',
                success: function(resp) {
                    if(resp.success) {
                        renderConfigDias(resp.dias_laborables);
                        renderTablaEmpleados(resp.empleados, resp.dias_laborables);
                    } else {
                        swal('Error', resp.message, 'error');
                    }
                },
                error: function() {
                    swal('Error', 'Error al cargar datos. Revise consola.', 'error');
                }
            });
        }

        function renderConfigDias(diasLaborables) {
            diasKeys.forEach(key => {
                let card = $('#card-' + key);
                let check = $('#check-config-' + key);
                let esLaborable = diasLaborables.includes(key);
                
                check.prop('checked', esLaborable);
                
                if (esLaborable) {
                    card.addClass('active');
                    card.find('.status-text').text('Laborable');
                    $('.col-dia-' + key).removeClass('day-off');
                } else {
                    card.removeClass('active');
                    card.find('.status-text').text('Descanso');
                    $('.col-dia-' + key).addClass('day-off');
                }
            });
        }

        function renderTablaEmpleados(empleados, diasLaborables) {
            let tbody = $('#tabla-body');
            tbody.empty();

            if (!empleados || empleados.length === 0) {
                tbody.html('<tr><td colspan="9" class="text-center">No hay empleados activos.</td></tr>');
                return;
            }

            empleados.forEach(emp => {
                let htmlDias = '';
                
                diasKeys.forEach((key, idx) => {
                    let diaData = emp.dias[idx];
                    let fecha = fechasSemanaMap[key];
                    let disabled = diasLaborables.includes(key) ? '' : 'disabled';
                    
                    let btnClass = 'pendiente'; 
                    let btnIcon = '<i class="fas fa-dollar-sign"></i>';
                    let title = 'Registrar Pago';
                    let montoActual = emp.tarifa; 

                    if (diaData.pagado) {
                        btnClass = 'pagado';
                        btnIcon = '<i class="fas fa-check"></i>';
                        title = 'Pagado: $' + parseFloat(diaData.monto).toFixed(2);
                        montoActual = diaData.monto;
                    }

                    let cellClass = diaData.asistio ? 'con-asistencia' : 'no-asistencia';

                    htmlDias += `
                        <td class="text-center col-dia-${key} ${diasLaborables.includes(key) ? '' : 'day-off'} cell-dia">
                            <div class="cell-content ${cellClass}" id="cell-${emp.id}-${idx}">
                                <input type="checkbox" class="check-asistencia" 
                                       data-emp="${emp.id}" 
                                       data-fecha="${fecha}"
                                       data-idx="${idx}"
                                       ${diaData.asistio ? 'checked' : ''} ${disabled}>
                                
                                <button class="btn-pago-dia ${btnClass}" 
                                        onclick="abrirModalPago(${emp.id}, '${fecha}', ${montoActual}, ${diaData.pagado})"
                                        title="${title}">
                                    ${btnIcon}
                                </button>
                            </div>
                        </td>
                    `;
                });

                // Mostrar saldo de caja de la sucursal
                let saldoVal = parseFloat(emp.saldo_sucursal || 0);
                let saldoClass = saldoVal < 0 ? 'text-danger' : 'text-success';
                let nombreSucursal = emp.s || 'Sin Sucursal';

                let tr = `
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">${emp.n}</div>
                            <small class="text-muted d-block">${nombreSucursal}</small>
                            <div class="mt-1" style="font-size: 0.75rem; border-top: 1px solid #eee; padding-top: 2px;">
                                Caja: <span class="${saldoClass} fw-bold">$${saldoVal.toFixed(2)}</span>
                            </div>
                        </td>
                        ${htmlDias}
                        <td class="text-center">
                            <span class="total-money fw-bold" style="font-size: 1.1em;">$${parseFloat(emp.total_pagado).toFixed(2)}</span>
                        </td>
                    </tr>
                `;
                tbody.append(tr);
            });
        }

        // --- Acciones ---

        function toggleDiaConfig(dayKey) {
            let checkbox = $('#check-config-' + dayKey);
            let newState = !checkbox.prop('checked'); 
            checkbox.prop('checked', newState);

            let diasSelected = [];
            $('.config-day:checked').each(function() { diasSelected.push($(this).val()); });

            $.post('api.php', { action: 'save_config_dias', semana_inicio: lunesSemana, dias: diasSelected }, function() {
                cargarDatosSemana();
            });
        }

        function guardarAsistencia(checkbox) {
            let empId = checkbox.data('emp');
            let fecha = checkbox.data('fecha');
            let idx = checkbox.data('idx');
            let estado = checkbox.is(':checked') ? 1 : 0;
            let cell = $('#cell-' + empId + '-' + idx);

            $.post('api.php', {
                action: 'toggle_asistencia',
                empleado_id: empId,
                fecha: fecha,
                estado: estado
            }, function(resp) {
                if(!resp.success) {
                    checkbox.prop('checked', !estado); // Revertir
                    swal('Aviso', resp.message, 'warning');
                } else {
                    if (estado) cell.removeClass('no-asistencia').addClass('con-asistencia');
                    else cell.removeClass('con-asistencia').addClass('no-asistencia');
                }
            });
        }

        function abrirModalPago(empId, fecha, monto, esPagado) {
            $('#pago_emp_id').val(empId);
            $('#pago_fecha').val(fecha);
            $('#pago_monto').val(parseFloat(monto).toFixed(2));
            
            if (esPagado) {
                $('#modalPagoTitle').text('Editar Pago');
                $('#btnEliminarPago').show();
            } else {
                $('#modalPagoTitle').text('Registrar Pago');
                $('#btnEliminarPago').hide();
            }
            $('#modalPago').modal('show');
        }

        function confirmarPago() {
            let empId = $('#pago_emp_id').val();
            let fecha = $('#pago_fecha').val();
            let monto = $('#pago_monto').val();

            $.post('api.php', {
                action: 'pagar_dia',
                empleado_id: empId,
                fecha: fecha,
                monto: monto
            }, function(resp) {
                if(resp.success) {
                    $('#modalPago').modal('hide');
                    swal({
                        title: "¡Pago Registrado!",
                        text: "Se ha descontado de la caja correctamente.",
                        icon: "success",
                        timer: 1500,
                        buttons: false
                    });
                    cargarDatosSemana();
                } else {
                    swal('Error', resp.message, 'error');
                }
            });
        }

        function eliminarPagoActual() {
            let empId = $('#pago_emp_id').val();
            let fecha = $('#pago_fecha').val();
            
            if(!confirm('¿Estás seguro de eliminar este pago? El dinero volverá a la caja.')) return;

            $.post('api.php', {
                action: 'eliminar_pago_dia',
                empleado_id: empId,
                fecha: fecha
            }, function(resp) {
                $('#modalPago').modal('hide');
                cargarDatosSemana();
            });
        }
    </script>
</body>
</html>
