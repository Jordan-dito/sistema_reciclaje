<?php
/**
 * Gestión de Unidades
 * Sistema de Gestión de Reciclaje
 */

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
    <title>Gestión de Unidades - Sistema de Reciclaje</title>
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
    <link rel="stylesheet" href="../assets/css/demo.css" />
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
              $currentRoute = 'unidades';
              include __DIR__ . '/../includes/sidebar.php';
            ?>
          </div>
        </div>
      </div>

      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            <div class="logo-header" data-background-color="dark">
              <a href="../Dashboard.php" class="logo">
                <img src="../assets/img/logo.jpg" alt="HNOSYÁNEZ S.A." class="navbar-brand" height="50" style="object-fit: contain; border-radius: 8px;" />
              </a>
            </div>
          </div>
          <?php
            $basePath = '..';
            include __DIR__ . '/../includes/user-header.php';
            include __DIR__ . '/../includes/modal-foto-perfil.php';
          ?>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Gestión de Unidades</h3>
                <h6 class="op-7 mb-2">Administra las unidades de medida (kg, L, und, etc.)</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalAgregarUnidad">
                  <i class="fa fa-plus"></i> Nueva Unidad
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Lista de Unidades</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="unidadesTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>Nombre</th>
                            <th>Símbolo</th>
                            <th>Tipo</th>
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

    <!-- Modal Agregar Unidad -->
    <div class="modal fade" id="modalAgregarUnidad" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nueva Unidad</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formAgregarUnidad">
              <div class="form-group">
                <label>Nombre <span class="text-danger">*</span></label>
                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Kilogramos" required>
              </div>
              <div class="form-group">
                <label>Símbolo</label>
                <input type="text" id="simbolo" name="simbolo" class="form-control" placeholder="Ej: kg" maxlength="10">
                <small class="form-text text-muted">Símbolo de la unidad (opcional)</small>
              </div>
              <div class="form-group">
                <label>Tipo <span class="text-danger">*</span></label>
                <select id="tipo" name="tipo" class="form-control" required>
                  <option value="peso">Peso</option>
                  <option value="volumen">Volumen</option>
                  <option value="longitud">Longitud</option>
                  <option value="cantidad">Cantidad</option>
                </select>
              </div>
              <div class="form-group">
                <label>Estado</label>
                <select id="estado" name="estado" class="form-control">
                  <option value="activo">Activo</option>
                  <option value="inactivo">Inactivo</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarUnidad">Guardar Unidad</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Editar Unidad -->
    <div class="modal fade" id="modalEditarUnidad" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Unidad</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formEditarUnidad">
              <input type="hidden" id="edit_id" name="id">
              <div class="form-group">
                <label>Nombre <span class="text-danger">*</span></label>
                <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Símbolo</label>
                <input type="text" id="edit_simbolo" name="simbolo" class="form-control" maxlength="10">
              </div>
              <div class="form-group">
                <label>Tipo <span class="text-danger">*</span></label>
                <select id="edit_tipo" name="tipo" class="form-control" required>
                  <option value="peso">Peso</option>
                  <option value="volumen">Volumen</option>
                  <option value="longitud">Longitud</option>
                  <option value="cantidad">Cantidad</option>
                </select>
              </div>
              <div class="form-group">
                <label>Estado</label>
                <select id="edit_estado" name="estado" class="form-control">
                  <option value="activo">Activo</option>
                  <option value="inactivo">Inactivo</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnActualizarUnidad">Actualizar Unidad</button>
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
    <?php
      $basePath = '..';
      include __DIR__ . '/../includes/footer-scripts.php';
    ?>
    <script>
      $(document).ready(function() {
        var table = $('#unidadesTable').DataTable({
          "language": { "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" }
        });
        
        var unidadesList = [];

        function cargarUnidades() {
          $.ajax({
            url: 'api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                unidadesList = response.data;
                table.clear();
                response.data.forEach(function(unidad) {
                  var badgeEstado = unidad.estado === 'activo' 
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';
                  
                  var tipoLabels = {
                    'peso': 'Peso',
                    'volumen': 'Volumen',
                    'longitud': 'Longitud',
                    'cantidad': 'Cantidad'
                  };
                  
                  table.row.add([
                    '<strong>' + unidad.nombre + '</strong>',
                    unidad.simbolo || '-',
                    tipoLabels[unidad.tipo] || unidad.tipo,
                    badgeEstado,
                    '<button class="btn btn-link btn-primary btn-sm" onclick="editarUnidad(' + unidad.id + ')"><i class="fa fa-edit"></i></button> ' +
                    '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarUnidad(' + unidad.id + ')"><i class="fa fa-times"></i></button>'
                  ]);
                });
                table.draw();
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar las unidades", "error");
            }
          });
        }

        // Validar nombre en tiempo real
        $('#nombre').on('blur', function() {
          var nombre = $(this).val().trim();
          if (nombre.length > 0) {
            var nombreExiste = unidadesList.some(function(uni) {
              return uni.estado === 'activo' && uni.nombre.toLowerCase().trim() === nombre.toLowerCase().trim();
            });
            
            if (nombreExiste) {
              $(this).addClass('is-invalid');
              $(this).removeClass('is-valid');
              var feedback = $(this).next('.invalid-feedback');
              if (feedback.length === 0) {
                $(this).after('<div class="invalid-feedback">Ya existe una unidad activa con este nombre</div>');
              }
            } else {
              $(this).removeClass('is-invalid');
              $(this).addClass('is-valid');
              $(this).next('.invalid-feedback').remove();
            }
          }
        });
        
        $('#edit_nombre').on('blur', function() {
          var nombre = $(this).val().trim();
          var idActual = $('#edit_id').val();
          if (nombre.length > 0) {
            var nombreExiste = unidadesList.some(function(uni) {
              return uni.estado === 'activo' 
                && uni.id != idActual 
                && uni.nombre.toLowerCase().trim() === nombre.toLowerCase().trim();
            });
            
            if (nombreExiste) {
              $(this).addClass('is-invalid');
              $(this).removeClass('is-valid');
              var feedback = $(this).next('.invalid-feedback');
              if (feedback.length === 0) {
                $(this).after('<div class="invalid-feedback">Ya existe otra unidad activa con este nombre</div>');
              }
            } else {
              $(this).removeClass('is-invalid');
              $(this).addClass('is-valid');
              $(this).next('.invalid-feedback').remove();
            }
          }
        });

        $('#btnGuardarUnidad').click(function() {
          var form = $('#formAgregarUnidad')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var nombre = $('#nombre').val().trim();
          
          // Validar que el nombre no exista
          var nombreExiste = unidadesList.some(function(uni) {
            return uni.estado === 'activo' && uni.nombre.toLowerCase().trim() === nombre.toLowerCase().trim();
          });
          
          if (nombreExiste) {
            swal("Error", "Ya existe una unidad activa con el nombre \"" + nombre + "\"", "error");
            $('#nombre').focus();
            return;
          }
          
          var formData = {
            nombre: nombre,
            simbolo: $('#simbolo').val(),
            tipo: $('#tipo').val(),
            estado: $('#estado').val(),
            action: 'crear'
          };
          
          $.ajax({
            url: 'api.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                swal("¡Éxito!", response.message, "success");
                $('#modalAgregarUnidad').modal('hide');
                $('#formAgregarUnidad')[0].reset();
                $('#nombre').removeClass('is-valid is-invalid');
                $('#nombre').next('.invalid-feedback').remove();
                cargarUnidades();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar la unidad';
              swal("Error", error, "error");
            }
          });
        });

        $('#btnActualizarUnidad').click(function() {
          var form = $('#formEditarUnidad')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var idActual = $('#edit_id').val();
          var nombre = $('#edit_nombre').val().trim();
          
          // Validar que el nombre no exista en otra unidad
          var nombreExiste = unidadesList.some(function(uni) {
            return uni.estado === 'activo' 
              && uni.id != idActual 
              && uni.nombre.toLowerCase().trim() === nombre.toLowerCase().trim();
          });
          
          if (nombreExiste) {
            swal("Error", "Ya existe otra unidad activa con el nombre \"" + nombre + "\"", "error");
            $('#edit_nombre').focus();
            return;
          }
          
          var formData = {
            id: idActual,
            nombre: nombre,
            simbolo: $('#edit_simbolo').val(),
            tipo: $('#edit_tipo').val(),
            estado: $('#edit_estado').val(),
            action: 'actualizar'
          };
          
          $.ajax({
            url: 'api.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                swal("¡Éxito!", response.message, "success");
                $('#modalEditarUnidad').modal('hide');
                $('#edit_nombre').removeClass('is-valid is-invalid');
                $('#edit_nombre').next('.invalid-feedback').remove();
                cargarUnidades();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al actualizar la unidad';
              swal("Error", error, "error");
            }
          });
        });

        window.editarUnidad = function(id) {
          $.ajax({
            url: 'api.php?action=obtener&id=' + id,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var unidad = response.data;
                $('#edit_id').val(unidad.id);
                $('#edit_nombre').val(unidad.nombre);
                $('#edit_simbolo').val(unidad.simbolo || '');
                $('#edit_tipo').val(unidad.tipo);
                $('#edit_estado').val(unidad.estado);
                $('#modalEditarUnidad').modal('show');
              }
            }
          });
        };

        window.eliminarUnidad = function(id) {
          swal({
            title: "¿Está seguro?",
            text: "La unidad será desactivada",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              $.ajax({
                url: 'api.php',
                method: 'POST',
                data: { id: id, action: 'eliminar' },
                dataType: 'json',
                success: function(response) {
                  if (response.success) {
                    swal("¡Éxito!", response.message, "success");
                    cargarUnidades();
                  } else {
                    swal("Error", response.message, "error");
                  }
                }
              });
            }
          });
        };

        cargarUnidades();
      });
    </script>
  </body>
</html>

