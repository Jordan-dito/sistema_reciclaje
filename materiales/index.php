<?php
/**
 * Gestión de Materiales
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
    <title>Gestión de Materiales - Sistema de Reciclaje</title>
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
              $currentRoute = 'materiales';
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
          ?>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Gestión de Materiales</h3>
                <h6 class="op-7 mb-2">Administra los materiales reciclables por categoría</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalAgregarMaterial">
                  <i class="fa fa-plus"></i> Nuevo Material
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Lista de Materiales</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="materialesTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
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

    <!-- Modal Agregar Material -->
    <div class="modal fade" id="modalAgregarMaterial" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Material</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formAgregarMaterial">
              <div class="form-group">
                <label>Nombre <span class="text-danger">*</span></label>
                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: PET" required>
              </div>
              <div class="form-group">
                <label>Categoría</label>
                <select id="categoria_id" name="categoria_id" class="form-control">
                  <option value="">Seleccione una categoría</option>
                </select>
              </div>
              <div class="form-group">
                <label>Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Descripción del material"></textarea>
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
            <button type="button" class="btn btn-primary" id="btnGuardarMaterial">Guardar Material</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Editar Material -->
    <div class="modal fade" id="modalEditarMaterial" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Material</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formEditarMaterial">
              <input type="hidden" id="edit_id" name="id">
              <div class="form-group">
                <label>Nombre <span class="text-danger">*</span></label>
                <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Categoría</label>
                <select id="edit_categoria_id" name="categoria_id" class="form-control">
                  <option value="">Seleccione una categoría</option>
                </select>
              </div>
              <div class="form-group">
                <label>Descripción</label>
                <textarea id="edit_descripcion" name="descripcion" class="form-control" rows="3"></textarea>
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
            <button type="button" class="btn btn-primary" id="btnActualizarMaterial">Actualizar Material</button>
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
      var categoriasList = [];
      
      function cargarCategorias() {
        $.ajax({
          url: 'api.php?action=categorias',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              categoriasList = response.data;
              var selectAdd = $('#categoria_id');
              var selectEdit = $('#edit_categoria_id');
              selectAdd.html('<option value="">Seleccione una categoría</option>');
              selectEdit.html('<option value="">Seleccione una categoría</option>');
              response.data.forEach(function(cat) {
                selectAdd.append('<option value="' + cat.id + '">' + cat.nombre + '</option>');
                selectEdit.append('<option value="' + cat.id + '">' + cat.nombre + '</option>');
              });
            }
          }
        });
      }

      $(document).ready(function() {
        var table = $('#materialesTable').DataTable({
          "language": { "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" }
        });
        
        var materialesList = [];

        cargarCategorias();

        function cargarMateriales() {
          $.ajax({
            url: 'api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                materialesList = response.data;
                table.clear();
                response.data.forEach(function(material) {
                  var badgeEstado = material.estado === 'activo' 
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';
                  
                  table.row.add([
                    '<strong>' + material.nombre + '</strong>',
                    material.categoria_nombre || '-',
                    material.descripcion || '-',
                    badgeEstado,
                    '<button class="btn btn-link btn-primary btn-sm" onclick="editarMaterial(' + material.id + ')"><i class="fa fa-edit"></i></button> ' +
                    '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarMaterial(' + material.id + ')"><i class="fa fa-times"></i></button>'
                  ]);
                });
                table.draw();
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar los materiales", "error");
            }
          });
        }

        // Validar nombre en tiempo real
        $('#nombre').on('blur', function() {
          var nombre = $(this).val().trim();
          if (nombre.length > 0) {
            var nombreExiste = materialesList.some(function(mat) {
              return mat.estado === 'activo' && mat.nombre.toLowerCase().trim() === nombre.toLowerCase().trim();
            });
            
            if (nombreExiste) {
              $(this).addClass('is-invalid');
              $(this).removeClass('is-valid');
              var feedback = $(this).next('.invalid-feedback');
              if (feedback.length === 0) {
                $(this).after('<div class="invalid-feedback">Ya existe un material activo con este nombre</div>');
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
            var nombreExiste = materialesList.some(function(mat) {
              return mat.estado === 'activo' 
                && mat.id != idActual 
                && mat.nombre.toLowerCase().trim() === nombre.toLowerCase().trim();
            });
            
            if (nombreExiste) {
              $(this).addClass('is-invalid');
              $(this).removeClass('is-valid');
              var feedback = $(this).next('.invalid-feedback');
              if (feedback.length === 0) {
                $(this).after('<div class="invalid-feedback">Ya existe otro material activo con este nombre</div>');
              }
            } else {
              $(this).removeClass('is-invalid');
              $(this).addClass('is-valid');
              $(this).next('.invalid-feedback').remove();
            }
          }
        });

        $('#btnGuardarMaterial').click(function() {
          var form = $('#formAgregarMaterial')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var nombre = $('#nombre').val().trim();
          
          // Validar que el nombre no exista
          var nombreExiste = materialesList.some(function(mat) {
            return mat.estado === 'activo' && mat.nombre.toLowerCase().trim() === nombre.toLowerCase().trim();
          });
          
          if (nombreExiste) {
            swal("Error", "Ya existe un material activo con el nombre \"" + nombre + "\"", "error");
            $('#nombre').focus();
            return;
          }
          
          var formData = {
            nombre: nombre,
            categoria_id: $('#categoria_id').val() || null,
            descripcion: $('#descripcion').val(),
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
                $('#modalAgregarMaterial').modal('hide');
                $('#formAgregarMaterial')[0].reset();
                $('#nombre').removeClass('is-valid is-invalid');
                $('#nombre').next('.invalid-feedback').remove();
                cargarMateriales();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar el material';
              swal("Error", error, "error");
            }
          });
        });

        $('#btnActualizarMaterial').click(function() {
          var form = $('#formEditarMaterial')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var idActual = $('#edit_id').val();
          var nombre = $('#edit_nombre').val().trim();
          
          // Validar que el nombre no exista en otro material
          var nombreExiste = materialesList.some(function(mat) {
            return mat.estado === 'activo' 
              && mat.id != idActual 
              && mat.nombre.toLowerCase().trim() === nombre.toLowerCase().trim();
          });
          
          if (nombreExiste) {
            swal("Error", "Ya existe otro material activo con el nombre \"" + nombre + "\"", "error");
            $('#edit_nombre').focus();
            return;
          }
          
          var formData = {
            id: idActual,
            nombre: nombre,
            categoria_id: $('#edit_categoria_id').val() || null,
            descripcion: $('#edit_descripcion').val(),
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
                $('#modalEditarMaterial').modal('hide');
                $('#edit_nombre').removeClass('is-valid is-invalid');
                $('#edit_nombre').next('.invalid-feedback').remove();
                cargarMateriales();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al actualizar el material';
              swal("Error", error, "error");
            }
          });
        });

        window.editarMaterial = function(id) {
          // Recargar categorías antes de abrir el modal para tener datos actualizados
          cargarCategorias();
          
          $.ajax({
            url: 'api.php?action=obtener&id=' + id,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var mat = response.data;
                
                // Esperar a que el dropdown se cargue antes de establecer valores
                setTimeout(function() {
                  $('#edit_id').val(mat.id);
                  $('#edit_nombre').val(mat.nombre);
                  $('#edit_categoria_id').val(mat.categoria_id || '');
                  $('#edit_descripcion').val(mat.descripcion || '');
                  $('#edit_estado').val(mat.estado);
                  $('#modalEditarMaterial').modal('show');
                }, 200); // Pequeño delay para asegurar que el dropdown se cargó
              }
            }
          });
        };

        window.eliminarMaterial = function(id) {
          swal({
            title: "¿Está seguro?",
            text: "El material será desactivado",
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
                    cargarMateriales();
                  } else {
                    swal("Error", response.message, "error");
                  }
                }
              });
            }
          });
        };

        cargarMateriales();
      });
    </script>
  </body>
</html>

