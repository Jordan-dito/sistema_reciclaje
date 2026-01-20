<?php
/**
 * Gestión de Empleados - Página de ejemplo
 */
// Verificar autenticación
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../../index.php');
    exit;
}

$usuario = $auth->getCurrentUser();
$usuarioNombre = $usuario['nombre'] ?? 'Usuario';
// Inicializar variables de estado para evitar warnings
$errors = [];
$success = null;
// Conexión a BD y lógica
require_once __DIR__ . '/../config/database.php';
$db = null;
try {
  $db = getDB();
} catch (Exception $e) {
  $errors[] = 'No se pudo conectar a la base de datos.';
}

// DETECCIÓN AUTOMÁTICA DE SUCURSAL DEL USUARIO
$sucursalId = null;
if ($db) {
  try {
    $stmtSuc = $db->prepare("SELECT id FROM sucursales WHERE responsable_id = ? OR id = (SELECT sucursal_id FROM usuarios WHERE id = ?)");
    $stmtSuc->execute([$usuario['id'], $usuario['id']]);
    $resSuc = $stmtSuc->fetch();
    if ($resSuc) {
      $sucursalId = $resSuc['id'];
    }
  } catch (Exception $e) {
    // ignore
  }
}

// Obtener sucursales para el select
$sucursales = [];
if ($db) {
  try {
    $stmtSuc = $db->query("SELECT id, nombre FROM sucursales ORDER BY nombre");
    $sucursales = $stmtSuc->fetchAll();
  } catch (Exception $e) {
    // ignore
  }
}

// Obtener empleados para listado (FILTRADO POR SUCURSAL DEL USUARIO)
$empleados = [];
if ($db) {
  try {
    $sql = "SELECT e.*, s.nombre AS sucursal_nombre FROM empleados e LEFT JOIN sucursales s ON e.sucursal_id = s.id WHERE 1=1";
    
    // Filtrar por sucursal si el usuario tiene una asignada
    if ($sucursalId) {
      $sql .= " AND e.sucursal_id = " . intval($sucursalId);
    }
    
    $sql .= " ORDER BY e.id DESC";
    
    $stmt = $db->query($sql);
    $empleados = $stmt->fetchAll();
  } catch (Exception $e) {
    // tabla puede no existir aún
  }
}

// Manejo POST: crear empleado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'create')) {
  $sucursal_id = $_POST['sucursal_id'] ?? '';
  $cedula = trim($_POST['cedula'] ?? '');
  $nombres = trim($_POST['nombres'] ?? '');
  $apellidos = trim($_POST['apellidos'] ?? '');
  $fecha_ingreso = $_POST['fecha_ingreso'] ?? '';
  $cargo = trim($_POST['cargo'] ?? null);
  $tipo_contrato = trim($_POST['tipo_contrato'] ?? null);
  $estado = 'ACTIVO'; // Siempre activo al crear

  if (empty($sucursal_id)) $errors[] = 'Seleccione una sucursal.';
  if ($cedula === '') $errors[] = 'La cédula es obligatoria.';
  if ($nombres === '') $errors[] = 'El nombre es obligatorio.';
  if ($apellidos === '') $errors[] = 'El apellido es obligatorio.';
  if ($fecha_ingreso === '') $errors[] = 'La fecha de ingreso es obligatoria.';

  if (empty($errors) && $db) {
    try {
      $sql = "INSERT INTO empleados (sucursal_id, cedula, nombres, apellidos, fecha_ingreso, cargo, tipo_contrato, estado) VALUES (:sucursal_id, :cedula, :nombres, :apellidos, :fecha_ingreso, :cargo, :tipo_contrato, :estado)";
      $st = $db->prepare($sql);
      $st->execute([
        ':sucursal_id' => $sucursal_id,
        ':cedula' => $cedula,
        ':nombres' => $nombres,
        ':apellidos' => $apellidos,
        ':fecha_ingreso' => $fecha_ingreso,
        ':cargo' => $cargo,
        ':tipo_contrato' => $tipo_contrato,
        ':estado' => $estado,
      ]);
      header('Location: ' . $_SERVER['REQUEST_URI']);
      exit;
    } catch (PDOException $ex) {
      // Error silencioso - la validación se maneja en el frontend
    }
  }
}
// Manejo POST: actualizar empleado (AJAX o submit normal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'update')) {
  $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $sucursal_id = $_POST['sucursal_id'] ?? '';
  $nombres = trim($_POST['nombres'] ?? '');
  $apellidos = trim($_POST['apellidos'] ?? '');
  $cargo = trim($_POST['cargo'] ?? '');
  $tipo_contrato = trim($_POST['tipo_contrato'] ?? '');
  $estado = $_POST['estado'] ?? 'ACTIVO';

  $response = ['success' => false, 'message' => ''];

  if ($id <= 0) {
    $response['message'] = 'ID inválido';
  } elseif ($nombres === '' || $apellidos === '') {
    $response['message'] = 'Nombre y apellido son obligatorios';
  } elseif ($db) {
    try {
      $sql = "UPDATE empleados SET sucursal_id = :sucursal_id, nombres = :nombres, apellidos = :apellidos, cargo = :cargo, tipo_contrato = :tipo_contrato, estado = :estado WHERE id = :id";
      $st = $db->prepare($sql);
      $st->execute([
        ':sucursal_id' => $sucursal_id,
        ':nombres' => $nombres,
        ':apellidos' => $apellidos,
        ':cargo' => $cargo,
        ':tipo_contrato' => $tipo_contrato,
        ':estado' => $estado,
        ':id' => $id,
      ]);
      $response['success'] = true;
      $response['message'] = 'Empleado actualizado correctamente';
      // return updated data for client
      $response['data'] = [
        'id' => $id,
        'sucursal_id' => $sucursal_id,
        'nombres' => $nombres,
        'apellidos' => $apellidos,
        'cargo' => $cargo,
        'tipo_contrato' => $tipo_contrato,
        'estado' => $estado,
      ];
    } catch (PDOException $ex) {
      $response['message'] = 'Error al actualizar en la base de datos';
    }
  } else {
    $response['message'] = 'Base de datos no disponible';
  }

  // Si es petición AJAX, devolver JSON
  $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
  if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
  } else {
    // redirigir de vuelta con mensaje simple
    if ($response['success']) {
      header('Location: ' . $_SERVER['REQUEST_URI']);
      exit;
    } else {
      $errors[] = $response['message'];
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Gestión de Empleados - <?php echo htmlspecialchars($usuarioNombre); ?></title>
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


    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <style>
      /* Table tweaks */
      #empleadosTable tbody td{vertical-align:middle}
      .badge{font-size:0.85em;padding:.4em .6em}
      .table thead th{letter-spacing:.02em}
      /* Action icons styling */
      .table-actions .btn {
        background: none !important;
        border: none !important;
        padding: 0;
      }
      /* Special styled edit button */
      .btn-edit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: linear-gradient(180deg,#ffd66b 0%,#ffbf47 100%);
        color: #0f1724;
        box-shadow: 0 3px 8px rgba(15,23,42,0.10);
        border: 1px solid rgba(0,0,0,0.06);
        transition: transform .10s ease, box-shadow .10s ease;
      }
      .btn-edit i { font-size: 14px; }
      .btn-edit:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(15,23,42,0.14); }
      /* ensure the edit button aligns right in its cell like other pages */
      .table-actions { padding-right: 1rem; }
      .table-actions .btn-link { background: none !important; border: none !important; padding: 0; }
      
      /* Estilos para los botones de filtro */
      .filter-buttons {
        display: flex;
        gap: 0;
        margin-bottom: 25px;
        background: #f5f5f5;
        padding: 4px;
        border-radius: 8px;
        width: fit-content;
      }
      .filter-btn {
        padding: 10px 24px;
        border: none;
        background: transparent;
        color: #666;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 6px;
        outline: none;
      }
      .filter-btn:hover {
        color: #333;
      }
      .filter-btn.active {
        background: #5d5fef;
        color: white;
        box-shadow: 0 2px 4px rgba(93, 95, 239, 0.3);
      }
      .tab-content-custom {
        min-height: 300px;
      }
    </style>
  </head>
  <body>
    <div class="wrapper">
      <div class="sidebar" data-background-color="dark">
        <?php
          $basePath = '..';
          include __DIR__ . '/../includes/sidebar-logo.php';
        ?>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <?php
              $basePath = '..';
              $currentRoute = 'gestion_empleados';
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
          <?php
            $basePath = '..';
            include __DIR__ . '/../includes/user-header.php';
            include __DIR__ . '/../includes/modal-foto-perfil.php';
            include __DIR__ . '/../includes/modal-cambiar-password.php';
          ?>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Gestión de Empleados</div>
                    <div class="text-muted">Usuario: <?php echo htmlspecialchars($usuarioNombre); ?></div>
                  </div>
                  <div class="card-body">
                    <p class="mb-3">Gestionar empleados. Aquí puedes listar, crear y editar registros del personal.</p>

                    <?php if (!empty($errors)): ?>
                      <div class="alert alert-danger">
                        <ul class="mb-0">
                          <?php foreach ($errors as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    <?php endif; ?>

                    <div class="mb-3">
                      <button id="btnNuevoEmpleado" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEmpleado">
                        <i class="fa fa-plus"></i> Nuevo Empleado
                      </button>
                    </div>

                    <!-- Botones de Filtro -->
                    <div class="filter-buttons">
                      <button class="filter-btn active" onclick="cambiarFiltro('activos')" id="btnActivos">
                        Activos
                      </button>
                      <button class="filter-btn" onclick="cambiarFiltro('inactivos')" id="btnInactivos">
                        Inactivos
                      </button>
                    </div>

                    <!-- Contenido de las tablas -->
                    <div class="tab-content-custom">
                      <!-- Empleados Activos -->
                      <div class="table-view" id="vistaActivos">
                        <div class="table-responsive">
                          <table class="table table-striped table-hover" id="empleadosActivosTable">
                            <thead>
                              <tr>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Cédula</th>
                                <th>Fecha ingreso</th>
                                <th>Cargo</th>
                                <th>Tipo contrato</th>
                                <th>Estado</th>
                                <th>Sucursal</th>
                                <th>Acciones</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (!empty($empleados)): ?>
                                <?php foreach ($empleados as $emp): ?>
                                  <?php if (($emp['estado'] ?? 'ACTIVO') === 'ACTIVO'): ?>
                                    <tr>
                                      <td><?php echo htmlspecialchars($emp['nombres'] ?? ''); ?></td>
                                      <td><?php echo htmlspecialchars($emp['apellidos'] ?? ''); ?></td>
                                      <td><?php echo htmlspecialchars($emp['cedula'] ?? ''); ?></td>
                                      <td><?php echo htmlspecialchars($emp['fecha_ingreso'] ?? ''); ?></td>
                                      <td><?php echo htmlspecialchars($emp['cargo'] ?? ''); ?></td>
                                      <td><?php echo htmlspecialchars($emp['tipo_contrato'] ?? ''); ?></td>
                                      <td><span class="badge badge-success">Activo</span></td>
                                      <td><?php echo htmlspecialchars($emp['sucursal_nombre'] ?? ''); ?></td>
                                      <td class="table-actions text-center">
                                        <button
                                          class="btn-edit btn-open-edit"
                                          title="Editar"
                                          data-bs-toggle="modal"
                                          data-bs-target="#modalEmpleado"
                                          data-id="<?php echo (int)($emp['id'] ?? 0); ?>"
                                          data-sucursal_id="<?php echo (int)($emp['sucursal_id'] ?? 0); ?>"
                                          data-cedula="<?php echo htmlspecialchars($emp['cedula'] ?? ''); ?>"
                                          data-nombres="<?php echo htmlspecialchars($emp['nombres'] ?? ''); ?>"
                                          data-apellidos="<?php echo htmlspecialchars($emp['apellidos'] ?? ''); ?>"
                                          data-fecha_ingreso="<?php echo htmlspecialchars($emp['fecha_ingreso'] ?? ''); ?>"
                                          data-cargo="<?php echo htmlspecialchars($emp['cargo'] ?? ''); ?>"
                                          data-tipo_contrato="<?php echo htmlspecialchars($emp['tipo_contrato'] ?? ''); ?>"
                                          data-estado="<?php echo htmlspecialchars($emp['estado'] ?? ''); ?>"
                                        ><i class="fa fa-edit"></i></button>
                                      </td>
                                    </tr>
                                  <?php endif; ?>
                                <?php endforeach; ?>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <!-- Empleados Inactivos -->
                      <div class="table-view" id="vistaInactivos" style="display: none;">
                        <div class="table-responsive">
                          <table class="table table-striped table-hover" id="empleadosInactivosTable">
                            <thead>
                              <tr>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Cédula</th>
                                <th>Fecha ingreso</th>
                                <th>Cargo</th>
                                <th>Tipo contrato</th>
                                <th>Estado</th>
                                <th>Sucursal</th>
                                <th>Acciones</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (!empty($empleados)): ?>
                                <?php foreach ($empleados as $emp): ?>
                                  <?php if (($emp['estado'] ?? 'ACTIVO') === 'INACTIVO'): ?>
                                    <tr>
                                      <td><?php echo htmlspecialchars($emp['nombres'] ?? ''); ?></td>
                                      <td><?php echo htmlspecialchars($emp['apellidos'] ?? ''); ?></td>
                                      <td><?php echo htmlspecialchars($emp['cedula'] ?? ''); ?></td>
                                      <td><?php echo htmlspecialchars($emp['fecha_ingreso'] ?? ''); ?></td>
                                      <td><?php echo htmlspecialchars($emp['cargo'] ?? ''); ?></td>
                                      <td><?php echo htmlspecialchars($emp['tipo_contrato'] ?? ''); ?></td>
                                      <td><span class="badge badge-danger">Inactivo</span></td>
                                      <td><?php echo htmlspecialchars($emp['sucursal_nombre'] ?? ''); ?></td>
                                      <td class="table-actions text-center">
                                        <button
                                          class="btn-edit btn-open-edit"
                                          title="Editar"
                                          data-bs-toggle="modal"
                                          data-bs-target="#modalEmpleado"
                                          data-id="<?php echo (int)($emp['id'] ?? 0); ?>"
                                          data-sucursal_id="<?php echo (int)($emp['sucursal_id'] ?? 0); ?>"
                                          data-cedula="<?php echo htmlspecialchars($emp['cedula'] ?? ''); ?>"
                                          data-nombres="<?php echo htmlspecialchars($emp['nombres'] ?? ''); ?>"
                                          data-apellidos="<?php echo htmlspecialchars($emp['apellidos'] ?? ''); ?>"
                                          data-fecha_ingreso="<?php echo htmlspecialchars($emp['fecha_ingreso'] ?? ''); ?>"
                                          data-cargo="<?php echo htmlspecialchars($emp['cargo'] ?? ''); ?>"
                                          data-tipo_contrato="<?php echo htmlspecialchars($emp['tipo_contrato'] ?? ''); ?>"
                                          data-estado="<?php echo htmlspecialchars($emp['estado'] ?? ''); ?>"
                                        ><i class="fa fa-edit"></i></button>
                                      </td>
                                    </tr>
                                  <?php endif; ?>
                                <?php endforeach; ?>
                              <?php endif; ?>
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
      </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/setting-demo.js"></script>
    <script src="../assets/js/validaciones.js"></script>
    <script>
      $(document).ready(function() {
        // Inicializar ambas tablas
        var tableActivos = $('#empleadosActivosTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          },
          "pageLength": 10
        });
        
        var tableInactivos = $('#empleadosInactivosTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          },
          "pageLength": 10
        });
        
        var currentEditRow = null;
        var currentTable = tableActivos; // Tabla actual
        
        // Función para cambiar entre vistas
        window.cambiarFiltro = function(tipo) {
          if (tipo === 'activos') {
            $('#vistaActivos').show();
            $('#vistaInactivos').hide();
            $('#btnActivos').addClass('active');
            $('#btnInactivos').removeClass('active');
            currentTable = tableActivos;
          } else {
            $('#vistaActivos').hide();
            $('#vistaInactivos').show();
            $('#btnActivos').removeClass('active');
            $('#btnInactivos').addClass('active');
            currentTable = tableInactivos;
          }
        };
        // Leave DataTables' default search control visible (no custom search)
        // Abrir modal Nuevo Empleado
        $('#btnNuevoEmpleado').on('click', function(){
          // reset form for create
          var form = $('#modalEmpleado form')[0];
          if (form) form.reset();
          $('#formAction').val('create');
          $('#empleado_id').val('');
          // remove validation states if any
          $('#cedula').removeClass('valid invalid');
          $('#cedulaValidation').removeClass('show valid invalid').html('');
          $('#cedulaError').text('');
          // make all fields editable for creation
          $('#cedula').prop('readonly', false);
          $('#fecha_ingreso').prop('readonly', false);
          $('#cargo').prop('readonly', false);
          $('#tipo_contrato').prop('readonly', false);
          $('#nombres').prop('readonly', false);
          $('#apellidos').prop('readonly', false);
          $('#sucursal_id').prop('disabled', false);
          $('#row-estado').hide(); // Ocultar estado en modo crear
          $('#modalEmpleadoLabel').text('Nuevo Empleado');
          $('#modalEmpleado button[type="submit"]').text('Guardar');
        });

        // Validación antes de enviar el formulario
        $('#formEmpleado').on('submit', function(e) {
          var action = $('#formAction').val();
          
          // Solo validar cédula al crear nuevo empleado (no al editar)
          if (action === 'create') {
            var cedula = $('#cedula').val();
            
            // Verificar que la cédula tenga 10 dígitos
            if (cedula.length !== 10) {
              e.preventDefault();
              swal({
                title: 'Error',
                text: 'La cédula debe tener exactamente 10 dígitos',
                icon: 'error',
                button: 'OK'
              });
              return false;
            }
            
            // Validar formato ecuatoriano con validación detallada
            var resultado = validarCedulaEcuatorianaDetallada(cedula);
            if (!resultado.valid) {
              e.preventDefault();
              swal({
                title: 'Cédula inválida',
                text: resultado.message || 'Por favor, ingrese una cédula ecuatoriana válida',
                icon: 'error',
                button: 'OK'
              });
              return false;
            }
            
            // Verificar que no esté marcada como inválida (por duplicado u otro motivo)
            if ($cedula.hasClass('invalid')) {
              e.preventDefault();
              var mensajeError = $cedulaError.text() || 'La cédula ingresada no es válida';
              swal({
                title: 'Error de validación',
                text: mensajeError,
                icon: 'warning',
                button: 'OK'
              });
              return false;
            }
          }
        });

        // Cerrar modal al pulsar Cancelar (compatible BS4/BS5)
        $('#modalEmpleado').on('click', 'button.btn-secondary', function(e){
          var modalEl = document.getElementById('modalEmpleado');
          if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var m = bootstrap.Modal.getOrCreateInstance(modalEl);
            m.hide();
          } else if ($('#modalEmpleado').modal) {
            $('#modalEmpleado').modal('hide');
          }
        });

        // Cerrar modal al pulsar la 'X' de la cabecera (compatible BS4/BS5)
        $('#modalEmpleado').on('click', '.close', function(e){
          var modalEl = document.getElementById('modalEmpleado');
          if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var m = bootstrap.Modal.getOrCreateInstance(modalEl);
            m.hide();
          } else if ($('#modalEmpleado').modal) {
            $('#modalEmpleado').modal('hide');
          }
        });

        // Reutilizar validación de cédula del proyecto
        var $cedula = $('#cedula');
        var $cedulaValidation = $('#cedulaValidation');
        var $cedulaError = $('#cedulaError');
        var cedulaVerificacionTimeout = null;

        function limpiarCedulaEstado() {
          $cedula.removeClass('valid invalid');
          $cedulaValidation.removeClass('show valid invalid').html('');
          $cedulaError.text('');
        }

        function validarCedulaEcuatorianaDetallada(cedula) {
          // Verificar que tenga 10 dígitos
          if (cedula.length !== 10) {
            return { valid: false, message: 'La cédula debe tener 10 dígitos' };
          }

          // Verificar que todos sean números
          if (!/^\d+$/.test(cedula)) {
            return { valid: false, message: 'La cédula solo debe contener números' };
          }

          // Los dos primeros dígitos deben corresponder a una provincia (01-24)
          var provincia = parseInt(cedula.substring(0, 2));
          if (provincia < 1 || provincia > 24) {
            return { valid: false, message: 'Los dos primeros dígitos deben estar entre 01 y 24 (código de provincia)' };
          }

          // El tercer dígito debe ser menor a 6 para personas naturales
          var tercerDigito = parseInt(cedula.charAt(2));
          if (tercerDigito >= 6) {
            return { valid: false, message: 'El tercer dígito debe ser menor a 6 (cédula de persona natural)' };
          }

          // Algoritmo de validación del dígito verificador
          var coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
          var suma = 0;
          
          for (var i = 0; i < 9; i++) {
            var valor = parseInt(cedula.charAt(i)) * coeficientes[i];
            if (valor >= 10) {
              valor -= 9;
            }
            suma += valor;
          }
          
          var digitoVerificador = suma % 10 === 0 ? 0 : 10 - (suma % 10);
          var ultimoDigito = parseInt(cedula.charAt(9));
          
          if (digitoVerificador !== ultimoDigito) {
            return { valid: false, message: 'Cédula inválida: el dígito verificador no coincide' };
          }

          return { valid: true, message: 'Cédula válida' };
        }

        function verificarCedulaEnDB(cedula, callback) {
          var empleado_id = $('#empleado_id').val() || 0;
          $.ajax({
            url: 'api.php',
            method: 'GET',
            data: {
              action: 'verificar_cedula',
              cedula: cedula,
              empleado_id: empleado_id
            },
            dataType: 'json',
            success: function(response) {
              callback(response);
            },
            error: function() {
              callback({success: false, existe: false});
            }
          });
        }

        $cedula.on('input', function() {
          var val = $(this).val().replace(/[^0-9]/g, '');
          $(this).val(val);
          $cedulaError.text('');

          // Limpiar timeout anterior
          if (cedulaVerificacionTimeout) {
            clearTimeout(cedulaVerificacionTimeout);
          }

          if (val.length === 0) {
            limpiarCedulaEstado();
            return;
          }

          if (val.length === 10) {
            // Usar la validación detallada personalizada
            var resultado = validarCedulaEcuatorianaDetallada(val);
            if (resultado.valid) {
              // Cédula válida, ahora verificar si ya existe en la BD
              cedulaVerificacionTimeout = setTimeout(function() {
                verificarCedulaEnDB(val, function(response) {
                  if (response.existe) {
                    $cedula.addClass('invalid').removeClass('valid');
                    $cedulaValidation.addClass('show invalid').html('<i class="fas fa-times-circle"></i>');
                    $cedulaError.text('✗ Esta cédula ya está registrada en el sistema');
                  } else {
                    $cedula.addClass('valid').removeClass('invalid');
                    $cedulaValidation.addClass('show valid').html('<i class="fas fa-check-circle"></i>');
                    $cedulaError.text('');
                  }
                });
              }, 500); // Esperar 500ms después de que el usuario deje de escribir
            } else {
              $cedula.addClass('invalid').removeClass('valid');
              $cedulaValidation.addClass('show invalid').html('<i class="fas fa-times-circle"></i>');
              $cedulaError.text('✗ ' + (resultado.message || 'Cédula inválida'));
            }
          } else {
            $cedula.addClass('invalid').removeClass('valid');
            $cedulaValidation.addClass('show invalid').html('<i class="fas fa-times-circle"></i>');
            $cedulaError.text('La cédula debe tener 10 dígitos');
          }
        });

        // Abrir modal en modo editar y poblar campos.
        $(document).on('click', '.btn-open-edit', function() {
          var btn = $(this);
          var id = btn.data('id');
          var sucursal_id = btn.data('sucursal_id');
          var cedula = btn.data('cedula') || '';
          var nombres = btn.data('nombres') || '';
          var apellidos = btn.data('apellidos') || '';
          var fecha_ingreso = btn.data('fecha_ingreso') || '';
          var cargo = btn.data('cargo') || '';
          var tipo_contrato = btn.data('tipo_contrato') || '';
          var estado = btn.data('estado') || 'ACTIVO';

          // set values
          $('#empleado_id').val(id);
          $('#sucursal_id').val(sucursal_id);
          $('#cedula').val(cedula).prop('readonly', true);
          $('#nombres').val(nombres).prop('readonly', false);
          $('#apellidos').val(apellidos).prop('readonly', false);
          $('#fecha_ingreso').val(fecha_ingreso).prop('readonly', true);
          $('#cargo').val(cargo).prop('readonly', false);
          $('#tipo_contrato').val(tipo_contrato).prop('readonly', false);
          $('#estado').val(estado);
          $('#row-estado').show(); // Mostrar estado en modo editar

          // remember current row for later update
          currentEditRow = table.row(btn.closest('tr'));

          // set action to update
          $('#formAction').val('update');
          $('#modalEmpleadoLabel').text('Editar Empleado');
          $('#modalEmpleado button[type="submit"]').text('Actualizar');

          // clear cedula validation state (already readonly)
          $('#cedulaValidation').removeClass('show valid invalid').html('');
          $('#cedulaError').text('');
        });

        // Intercept form submit to perform AJAX update when editing
        $('#modalEmpleado form').on('submit', function(e){
          var action = $('#formAction').val();
          if (action === 'update') {
            e.preventDefault();
            var payload = {
              action: 'update',
              id: $('#empleado_id').val(),
              sucursal_id: $('#sucursal_id').val(),
              nombres: $('#nombres').val(),
              apellidos: $('#apellidos').val(),
              cargo: $('#cargo').val(),
              tipo_contrato: $('#tipo_contrato').val(),
              estado: $('#estado').val()
            };
            $.ajax({
              url: '',
              method: 'POST',
              data: payload,
              dataType: 'json',
              success: function(resp) {
                if (resp.success) {
                  // update table row in place
                  if (currentEditRow) {
                    var node = currentEditRow.node();
                    var $cells = $(node).find('td');
                    $cells.eq(0).text(resp.data.nombres);
                    $cells.eq(1).text(resp.data.apellidos);
                    $cells.eq(4).text(resp.data.cargo);
                    $cells.eq(5).text(resp.data.tipo_contrato);
                    // Actualizar badge de estado
                    if (resp.data.estado === 'ACTIVO') {
                      $cells.eq(6).html('<span class="badge badge-success">Activo</span>');
                    } else {
                      $cells.eq(6).html('<span class="badge badge-danger">Inactivo</span>');
                    }
                    // update sucursal name by looking up select option text
                    var sucText = $('#sucursal_id option:selected').text() || '';
                    $cells.eq(7).text(sucText);

                    // also update the edit button's data-* attributes so future edits use latest values
                    var $editBtn = $(node).find('.btn-open-edit');
                    if ($editBtn.length) {
                      $editBtn.attr('data-nombres', resp.data.nombres);
                      $editBtn.attr('data-apellidos', resp.data.apellidos);
                      $editBtn.attr('data-cargo', resp.data.cargo);
                      $editBtn.attr('data-tipo_contrato', resp.data.tipo_contrato);
                      $editBtn.attr('data-estado', resp.data.estado);
                      // update sucursal id too
                      if (typeof resp.data.sucursal_id !== 'undefined') {
                        $editBtn.attr('data-sucursal_id', resp.data.sucursal_id);
                      }
                      // keep jQuery's internal data cache in sync
                      $editBtn.data('nombres', resp.data.nombres);
                      $editBtn.data('apellidos', resp.data.apellidos);
                      $editBtn.data('cargo', resp.data.cargo);
                      $editBtn.data('tipo_contrato', resp.data.tipo_contrato);
                      $editBtn.data('estado', resp.data.estado);
                      if (typeof resp.data.sucursal_id !== 'undefined') $editBtn.data('sucursal_id', resp.data.sucursal_id);
                    }
                    // clear remembered row
                    currentEditRow = null;
                  }
                  // close modal
                  var modalEl = document.getElementById('modalEmpleado');
                  if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var m = bootstrap.Modal.getOrCreateInstance(modalEl);
                    m.hide();
                  } else if ($('#modalEmpleado').modal) {
                    $('#modalEmpleado').modal('hide');
                  }
                  swal('¡Éxito!', resp.message, 'success');
                } else {
                  swal('Error', resp.message || 'No se pudo actualizar', 'error');
                }
              },
              error: function() {
                swal('Error', 'Error en la petición', 'error');
              }
            });
          }
        });
      });
    </script>
    <!-- Modal: Nuevo Empleado -->
    <div class="modal fade" id="modalEmpleado" tabindex="-1" role="dialog" aria-labelledby="modalEmpleadoLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <form method="post" action="" id="formEmpleado">
            <input type="hidden" id="formAction" name="action" value="create">
            <input type="hidden" id="empleado_id" name="id" value="">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEmpleadoLabel">Nuevo Empleado</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
            <div class="modal-body">
              <!-- Información Personal -->
              <div class="mb-4">
                <h6 class="mb-3 text-primary border-bottom pb-2">
                  <i class="fas fa-user me-2"></i> Información Personal
                </h6>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="cedula" class="form-label">Cédula <span class="text-danger">*</span></label>
                    <div class="input-wrapper">
                      <input type="text" name="cedula" id="cedula" class="form-control" required maxlength="10" placeholder="0912345675">
                      <span class="validation-icon" id="cedulaValidation"></span>
                    </div>
                    <small class="error-message" id="cedulaError"></small>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="fecha_ingreso" class="form-label">Fecha de ingreso <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                    <input type="text" name="nombres" id="nombres" class="form-control" required placeholder="María José">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                    <input type="text" name="apellidos" id="apellidos" class="form-control" required placeholder="López Quiñonez">
                  </div>
                </div>
              </div>

              <!-- Información Laboral -->
              <div class="mb-3">
                <h6 class="mb-3 text-primary border-bottom pb-2">
                  <i class="fas fa-briefcase me-2"></i> Información Laboral
                </h6>
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label for="sucursal_id" class="form-label">Sucursal <span class="text-danger">*</span></label>
                    <select name="sucursal_id" id="sucursal_id" class="form-control form-select" required>
                      <option value="">Seleccione una sucursal</option>
                      <?php foreach ($sucursales as $s): ?>
                        <option value="<?php echo (int)$s['id']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="cargo" class="form-label">Cargo <span class="text-danger">*</span></label>
                    <input type="text" name="cargo" id="cargo" class="form-control" required placeholder="Operario, Supervisor, etc.">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="tipo_contrato" class="form-label">Tipo de contrato <span class="text-danger">*</span></label>
                    <input type="text" name="tipo_contrato" id="tipo_contrato" class="form-control" required placeholder="Ej: Indefinido, Fijo, Temporal">
                  </div>
                </div>
                <div class="row" id="row-estado" style="display: none;">
                  <div class="col-md-6 mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-control form-select">
                      <option value="ACTIVO">ACTIVO</option>
                      <option value="INACTIVO">INACTIVO</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="alert alert-info py-2 mb-0">
                <small><i class="fas fa-info-circle me-1"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios</small>
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
  </body>
</html>
