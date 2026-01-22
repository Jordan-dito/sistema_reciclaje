<?php
/**
 * Gastos Varios - Control Operativo de Sucursales
 * Sistema de Gestión de Reciclaje
 */

// Verificar autenticación
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Gastos Operativos - Sistema de Reciclaje</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

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
  </head>
  <body>
    <div class="wrapper">
      <div class="sidebar" data-background-color="dark">
        <?php $basePath = '..'; include __DIR__ . '/../includes/sidebar-logo.php'; ?>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <?php $basePath = '..'; $currentRoute = 'gastos_varios'; include __DIR__ . '/../includes/sidebar.php'; ?>
          </div>
        </div>
      </div>

      <div class="main-panel">
        <div class="main-header">
          <?php $basePath = '..'; include __DIR__ . '/../includes/main-header-logo.php'; ?>
          <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
            <div class="container-fluid">
              <div class="collapse navbar-collapse justify-content-end" id="search-navbar">
                <?php $basePath = '..'; include __DIR__ . '/../includes/user-header.php'; ?>
              </div>
            </div>
          </nav>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                      <div>
                        <h3 class="fw-bold mb-2">Gastos Operativos de Sucursal</h3>
                        <p class="text-muted mb-0">Gestión de servicios básicos y mantenimiento (Descontado de Caja).</p>
                      </div>
                      <div class="text-end">
                        <h4 class="fw-bold text-success mb-0">Saldo en Caja: <span id="saldoCaja">$0.00</span></h4>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label>Sucursal</label>
                            <select id="filtroSucursal" class="form-control">
                                <option value="">Todas las sucursales</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Filtrar por Mes</label>
                            <select id="filtroMes" class="form-control">
                                <option value="01">Enero</option><option value="02">Febrero</option>
                                <option value="03">Marzo</option><option value="04">Abril</option>
                                <option value="05">Mayo</option><option value="06">Junio</option>
                                <option value="07">Julio</option><option value="08">Agosto</option>
                                <option value="09">Septiembre</option><option value="10">Octubre</option>
                                <option value="11">Noviembre</option><option value="12">Diciembre</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Año</label>
                            <input type="number" id="filtroAnio" class="form-control" value="<?php echo date('Y'); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-secondary w-100" onclick="cargarGastos()"><i class="fa fa-filter"></i> Filtrar</button>
                        </div>
                        <div class="col-md-3 d-flex align-items-end justify-content-end">
                            <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalGasto">
                                <i class="fa fa-plus"></i> Registrar Gasto
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                      <table id="gastosTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
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

    <!-- Modal Nuevo Gasto -->
    <div class="modal fade" id="modalGasto" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Registrar Gasto Operativo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="formGasto">
            <div class="modal-body">
              <div class="form-group" id="groupSucursalModal">
                <label>Sucursal *</label>
                <select class="form-control" id="sucursal_id" name="sucursal_id" required>
                    <option value="">Seleccione una sucursal</option>
                </select>
              </div>
              <div class="form-group">
                <label>Fecha *</label>
                <input type="date" class="form-control" id="fecha" name="fecha" required>
              </div>
              <div class="form-group">
                <label>Concepto *</label>
                <select class="form-control" id="concepto" name="concepto" required>
                  <option value="">Seleccione un concepto</option>
                  <option value="Electricidad">Electricidad (Luz)</option>
                  <option value="Agua">Agua</option>
                  <option value="Internet">Internet</option>
                  <option value="Mantenimiento de Vehículo">Mantenimiento de Vehículo</option>
                  <option value="Combustible">Combustible</option>
                  <option value="Almuerzo Equipo">Almuerzo Equipo</option>
                  <option value="Otros Suministros">Otros Suministros</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>
              <div class="form-group">
                <label>Descripción / Notas</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="2" placeholder="Detalles del gasto..."></textarea>
              </div>
              <div class="form-group">
                <label>Monto a Descontar *</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="monto" name="monto" step="0.01" min="0.01" required>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Guardar y Descontar de Caja</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
      $(document).ready(function() {
        $('#fecha').val(new Date().toISOString().split('T')[0]);
        $('#filtroMes').val(('0' + (new Date().getMonth() + 1)).slice(-2));

        $('#gastosTable').DataTable({
          pageLength: 10,
          language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
          order: [[0, 'desc']]
        });
        
        cargarSucursales();

        $('#formGasto').on('submit', function(e) {
          e.preventDefault();
          guardarGasto();
        });
      });

      function cargarSucursales() {
        $.ajax({
            url: '../sucursales/api.php?action=activas',
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    var filtro = $('#filtroSucursal');
                    var modalSelect = $('#sucursal_id');
                    
                    // Limpiar (mantener opción por defecto en filtro)
                    filtro.find('option:not(:first)').remove();
                    modalSelect.find('option:not(:first)').remove();
                    
                    var sucursales = response.data;
                    
                    sucursales.forEach(function(suc) {
                        filtro.append('<option value="'+suc.id+'">'+suc.nombre+'</option>');
                        modalSelect.append('<option value="'+suc.id+'">'+suc.nombre+'</option>');
                    });

                    // Si solo hay una sucursal (usuario restringido), seleccionarla automáticamente
                    if (sucursales.length === 1) {
                        filtro.val(sucursales[0].id).prop('disabled', true);
                        modalSelect.val(sucursales[0].id);
                        // Ocultar el selector en modal si es obvio
                        $('#groupSucursalModal').hide(); 
                    } else {
                        // Si hay varias, asegurarse que el modal las muestre
                        $('#groupSucursalModal').show();
                    }
                    
                    // Cargar gastos después de tener sucursales (por si hay preselección)
                    cargarGastos();
                }
            }
        });
      }

      function cargarGastos() {
        var mes = $('#filtroMes').val();
        var anio = $('#filtroAnio').val();
        var sucursal_id = $('#filtroSucursal').val();

        $.ajax({
          url: 'api_gastos.php?action=list',
          type: 'GET',
          data: { 
            mes: mes, 
            anio: anio,
            sucursal_id: sucursal_id
          },
          success: function(response) {
            if (response.success) {
              var table = $('#gastosTable').DataTable();
              table.clear();
              $('#saldoCaja').text('$' + parseFloat(response.saldo_sucursal || 0).toFixed(2));
              
              response.data.forEach(function(gasto) {
                var estadoBadge = gasto.estado === 'completado' ? '<span class="badge bg-success">Pagado</span>' : '<span class="badge bg-danger">Cancelado</span>';
                var acciones = gasto.estado === 'completado' ? '<button class="btn btn-sm btn-danger" onclick="eliminarGasto(' + gasto.id + ')"><i class="fa fa-times"></i></button>' : '<i class="fa fa-ban text-muted"></i>';
                
                // Mostrar nombre de sucursal si hay filtro de "Todas" o si es admin viendo varias
                var conceptoHtml = '<strong>' + gasto.concepto + '</strong>';
                if (!sucursal_id && gasto.sucursal_nombre) {
                    conceptoHtml += '<br><small class="text-muted">' + gasto.sucursal_nombre + '</small>';
                }

                table.row.add([
                  gasto.fecha, 
                  conceptoHtml, 
                  gasto.descripcion || '-',
                  '<strong>$' + parseFloat(gasto.monto).toFixed(2) + '</strong>', 
                  estadoBadge, 
                  acciones
                ]);
              });
              table.draw();
            }
          }
        });
      }

      function guardarGasto() {
        $.ajax({
          url: 'api_gastos.php?action=create',
          type: 'POST',
          data: $('#formGasto').serialize(),
          success: function(response) {
            if (response.success) {
              swal("¡Éxito!", response.message, "success");
              $('#modalGasto').modal('hide');
              $('#formGasto')[0].reset();
              // Restablecer fecha
              $('#fecha').val(new Date().toISOString().split('T')[0]);
              cargarGastos();
            } else {
              swal("Error", response.message, "error");
            }
          }
        });
      }

      function eliminarGasto(id) {
        swal({
          title: "¿Cancelar gasto?",
          text: "El monto volverá al saldo de la sucursal.",
          icon: "warning",
          buttons: ["No", "Sí, cancelar"],
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            $.ajax({
              url: 'api_gastos.php?action=delete',
              type: 'POST',
              data: { id: id },
              success: function(response) {
                if (response.success) {
                  swal("Cancelado", response.message, "success");
                  cargarGastos();
                } else {
                    swal("Error", response.message || "Error al cancelar", "error");
                }
              }
            });
          }
        });
      }
    </script>
  </body>
</html>
