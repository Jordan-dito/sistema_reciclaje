<?php
/**
 * Gastos Varios - Descuentos de Empleados
 * Sistema de Gestión de Reciclaje
 */

// Verificar autenticación
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../index.php');
    exit;
}

// Crear tabla si no existe
require_once __DIR__ . '/../config/database.php';
$db = getDB();

try {
    // Verificar si la tabla existe
    $stmt = $db->query("SHOW TABLES LIKE 'gastos_varios'");
    if ($stmt->rowCount() == 0) {
        // Crear la tabla sin relación (sin foreign key)
        $db->exec("
            CREATE TABLE gastos_varios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                empleado_id INT NOT NULL,
                concepto VARCHAR(100) NOT NULL,
                descripcion TEXT,
                monto DECIMAL(10,2) NOT NULL,
                fecha DATE NOT NULL,
                estado ENUM('pendiente', 'aplicado', 'cancelado') DEFAULT 'pendiente',
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
} catch (Exception $e) {
    error_log("Error al verificar/crear tabla gastos_varios: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Gastos Varios - Sistema de Reciclaje</title>
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
              $currentRoute = 'gastos_varios';
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
          <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
            <div class="container-fluid">
              <div class="collapse navbar-collapse justify-content-end" id="search-navbar">
                <?php
                  $basePath = '..';
                  include __DIR__ . '/../includes/user-header.php';
                ?>
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
                    <div>
                      <h3 class="fw-bold mb-2">Registro de Gastos y Descuentos</h3>
                      <p class="text-muted mb-0">Gestionar gastos y descuentos de empleados. Aquí puedes registrar almuerzos, adelantos, préstamos y otros conceptos que se descuentan del sueldo.</p>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <h4 class="card-title mb-0">Lista de Gastos</h4>
                      <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalGasto">
                        <i class="fa fa-plus"></i> Nuevo Gasto
                      </button>
                    </div>
                    <div class="table-responsive">
                      <table id="gastosTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Empleado</th>
                            <th>Concepto</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
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

    <!-- Modal Nuevo/Editar Gasto -->
    <div class="modal fade" id="modalGasto" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalGastoTitle">Nuevo Gasto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="formGasto">
            <input type="hidden" id="gasto_id" name="id">
            <div class="modal-body">
              <div class="form-group">
                <label for="empleado_id">Empleado *</label>
                <select class="form-control" id="empleado_id" name="empleado_id" required>
                  <option value="">Seleccione un empleado</option>
                </select>
              </div>
              <div class="form-group">
                <label for="concepto">Concepto *</label>
                <select class="form-control" id="concepto" name="concepto" required>
                  <option value="">Seleccione un concepto</option>
                  <option value="Almuerzo">Almuerzo</option>
                  <option value="Adelanto de sueldo">Adelanto de sueldo</option>
                  <option value="Préstamo">Préstamo</option>
                  <option value="Uniformes">Uniformes</option>
                  <option value="Herramientas">Herramientas</option>
                  <option value="Multa">Multa</option>
                  <option value="Seguro">Seguro</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>
              <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="2" placeholder="Detalles adicionales"></textarea>
              </div>
              <div class="form-group">
                <label for="monto">Monto *</label>
                <input type="number" class="form-control" id="monto" name="monto" step="0.01" min="0" required placeholder="0.00">
              </div>
              <div class="form-group">
                <label for="fecha">Fecha *</label>
                <input type="date" class="form-control" id="fecha" name="fecha" required>
              </div>
              <div class="form-group">
                <label for="estado">Estado</label>
                <select class="form-control" id="estado" name="estado">
                  <option value="pendiente">Pendiente</option>
                  <option value="aplicado">Aplicado</option>
                  <option value="cancelado">Cancelado</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
      $(document).ready(function() {
        // Establecer fecha actual por defecto
        $('#fecha').val(new Date().toISOString().split('T')[0]);

        // DataTable
        var table = $('#gastosTable').DataTable({
          pageLength: 10,
          language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
          },
          order: [[0, 'desc']]
        });

        // Cargar empleados
        cargarEmpleados();

        // Cargar gastos
        cargarGastos();

        // Submit form
        $('#formGasto').on('submit', function(e) {
          e.preventDefault();
          guardarGasto();
        });

        // Reset modal on close
        $('#modalGasto').on('hidden.bs.modal', function() {
          $('#formGasto')[0].reset();
          $('#gasto_id').val('');
          $('#modalGastoTitle').text('Nuevo Gasto');
          $('#fecha').val(new Date().toISOString().split('T')[0]);
        });
      });

      function cargarEmpleados() {
        $.ajax({
          url: 'api_gastos.php?action=list_empleados',
          type: 'GET',
          success: function(response) {
            if (response.success) {
              var select = $('#empleado_id');
              select.empty().append('<option value="">Seleccione un empleado</option>');
              response.data.forEach(function(emp) {
                select.append('<option value="' + emp.id + '">' + emp.nombre + ' ' + emp.apellido + '</option>');
              });
            }
          }
        });
      }

      function cargarGastos() {
        $.ajax({
          url: 'api_gastos.php?action=list',
          type: 'GET',
          success: function(response) {
            if (response.success) {
              var table = $('#gastosTable').DataTable();
              table.clear();

              response.data.forEach(function(gasto) {
                var estadoBadge = '';
                switch(gasto.estado) {
                  case 'pendiente':
                    estadoBadge = '<span class="badge bg-warning">Pendiente</span>';
                    break;
                  case 'aplicado':
                    estadoBadge = '<span class="badge bg-success">Aplicado</span>';
                    break;
                  case 'cancelado':
                    estadoBadge = '<span class="badge bg-danger">Cancelado</span>';
                    break;
                }

                table.row.add([
                  gasto.id,
                  gasto.empleado_nombre,
                  gasto.concepto,
                  gasto.descripcion || '-',
                  '$' + parseFloat(gasto.monto).toFixed(2),
                  gasto.fecha,
                  estadoBadge,
                  '<button class="btn btn-sm btn-warning" onclick="editarGasto(' + gasto.id + ')"><i class="fa fa-edit"></i></button> ' +
                  '<button class="btn btn-sm btn-danger" onclick="eliminarGasto(' + gasto.id + ')"><i class="fa fa-trash"></i></button>'
                ]);
              });

              table.draw();
            } else {
              swal("Error", response.message || "No se pudieron cargar los gastos", "error");
            }
          },
          error: function() {
            swal("Error", "Error de conexión con el servidor", "error");
          }
        });
      }

      function guardarGasto() {
        var formData = $('#formGasto').serialize();
        var action = $('#gasto_id').val() ? 'update' : 'create';

        $.ajax({
          url: 'api_gastos.php?action=' + action,
          type: 'POST',
          data: formData,
          success: function(response) {
            if (response.success) {
              swal("Éxito", response.message, "success");
              $('#modalGasto').modal('hide');
              cargarGastos();
            } else {
              swal("Error", response.message, "error");
            }
          },
          error: function() {
            swal("Error", "Error al guardar el gasto", "error");
          }
        });
      }

      function editarGasto(id) {
        $.ajax({
          url: 'api_gastos.php?action=get&id=' + id,
          type: 'GET',
          success: function(response) {
            if (response.success) {
              var gasto = response.data;
              $('#gasto_id').val(gasto.id);
              $('#empleado_id').val(gasto.empleado_id);
              $('#concepto').val(gasto.concepto);
              $('#descripcion').val(gasto.descripcion);
              $('#monto').val(gasto.monto);
              $('#fecha').val(gasto.fecha);
              $('#estado').val(gasto.estado);
              $('#modalGastoTitle').text('Editar Gasto');
              $('#modalGasto').modal('show');
            }
          }
        });
      }

      function eliminarGasto(id) {
        swal({
          title: "¿Está seguro?",
          text: "Esta acción no se puede deshacer",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            $.ajax({
              url: 'api_gastos.php?action=delete',
              type: 'POST',
              data: { id: id },
              success: function(response) {
                if (response.success) {
                  swal("Eliminado", response.message, "success");
                  cargarGastos();
                } else {
                  swal("Error", response.message, "error");
                }
              }
            });
          }
        });
      }
    </script>
  </body>
</html>
