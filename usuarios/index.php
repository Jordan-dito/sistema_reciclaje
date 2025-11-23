<?php
/**
 * Gestión de Usuarios
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
    <title>Gestión de Usuarios - Sistema de Reciclaje</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="../assets/img/kaiadmin/favicon.ico"
      type="image/x-icon"
    />

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
              $currentRoute = 'usuarios';
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
                <img
                  src="../assets/img/logo.jpg"
                  alt="HNOSYÁNEZ S.A."
                  class="navbar-brand"
                  height="50"
                  style="object-fit: contain; border-radius: 8px;"
                />
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
                <h3 class="fw-bold mb-3">Gestión de Usuarios</h3>
                <h6 class="op-7 mb-2">Administra los usuarios del sistema</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalAgregarUsuario">
                  <i class="fa fa-plus"></i> Nuevo Usuario
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Lista de Usuarios</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="usuariosTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Los usuarios se cargarán dinámicamente desde la base de datos -->
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

    <!-- Modal Agregar Usuario -->
    <div class="modal fade" id="modalAgregarUsuario" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Usuario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formAgregarUsuario">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Nombre Completo *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Juan Pérez" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Cédula *</label>
                    <input type="text" id="cedula" name="cedula" class="form-control" placeholder="0912345678" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email *</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="usuario@email.com" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" class="form-control" placeholder="0998765432">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Rol *</label>
                    <select id="rol_id" name="rol_id" class="form-control" required>
                      <option value="">Seleccione un rol</option>
                      <option value="1">Administrador</option>
                      <option value="2">Gerente</option>
                      <option value="3">Usuario</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Contraseña *</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Estado</label>
                    <select id="estado" name="estado" class="form-control">
                      <option value="activo">Activo</option>
                      <option value="inactivo">Inactivo</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarUsuario">Guardar Usuario</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Usuario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formEditarUsuario">
              <input type="hidden" id="editar_id" name="id">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Nombre Completo *</label>
                    <input type="text" id="editar_nombre" name="nombre" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Cédula *</label>
                    <input type="text" id="editar_cedula" name="cedula" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email *</label>
                    <input type="email" id="editar_email" name="email" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" id="editar_telefono" name="telefono" class="form-control">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Rol *</label>
                    <select id="editar_rol_id" name="rol_id" class="form-control" required>
                      <option value="">Seleccione un rol</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nueva Contraseña (dejar vacío para no cambiar)</label>
                    <input type="password" id="editar_password" name="password" class="form-control" placeholder="Mínimo 8 caracteres">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Estado</label>
                    <select id="editar_estado" name="estado" class="form-control">
                      <option value="activo">Activo</option>
                      <option value="inactivo">Inactivo</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnActualizarUsuario">Actualizar Usuario</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/setting-demo.js"></script>
    <script>
      $(document).ready(function() {
        // Inicializar DataTable
        var table = $('#usuariosTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          }
        });
        
        // Cargar usuarios desde la base de datos
        function cargarUsuarios() {
          $.ajax({
            url: 'api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            xhrFields: {
              withCredentials: true
            },
            crossDomain: false,
            success: function(response) {
              if (response.success) {
                table.clear();
                response.data.forEach(function(usuario) {
                  var badgeRol = '';
                  if (usuario.rol_nombre === 'Administrador') {
                    badgeRol = '<span class="badge badge-primary">Administrador</span>';
                  } else if (usuario.rol_nombre === 'Gerente') {
                    badgeRol = '<span class="badge badge-info">Gerente</span>';
                  } else {
                    badgeRol = '<span class="badge badge-secondary">Usuario</span>';
                  }
                  
                  var badgeEstado = usuario.estado === 'activo' 
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';
                  
                  // Botón de estado: Activar si está inactivo, Desactivar si está activo
                  var botonEstado = '';
                  if (usuario.estado === 'activo') {
                    botonEstado = '<button class="btn btn-link btn-danger btn-sm" onclick="desactivarUsuario(' + usuario.id + ')" title="Desactivar"><i class="fa fa-times"></i></button>';
                  } else {
                    botonEstado = '<button class="btn btn-link btn-success btn-sm" onclick="activarUsuario(' + usuario.id + ')" title="Activar"><i class="fa fa-check"></i></button>';
                  }
                  
                  table.row.add([
                    '<strong>' + usuario.nombre + '</strong>',
                    usuario.cedula,
                    usuario.email,
                    usuario.telefono || '-',
                    badgeRol,
                    badgeEstado,
                    '<button class="btn btn-link btn-primary btn-sm" onclick="editarUsuario(' + usuario.id + ')" title="Editar"><i class="fa fa-edit"></i></button> ' +
                    botonEstado
                  ]);
                });
                table.draw();
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar los usuarios", "error");
            }
          });
        }
        
        // Cargar usuarios al iniciar
        cargarUsuarios();
        
        // Guardar nuevo usuario
        $('#btnGuardarUsuario').click(function() {
          var form = $('#formAgregarUsuario')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var formData = {
            nombre: $('#nombre').val(),
            cedula: $('#cedula').val(),
            email: $('#email').val(),
            telefono: $('#telefono').val(),
            password: $('#password').val(),
            rol_id: $('#rol_id').val(),
            estado: $('#estado').val(),
            action: 'crear'
          };
          
          $.ajax({
            url: 'api.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            xhrFields: {
              withCredentials: true
            },
            crossDomain: false,
            success: function(response) {
              if (response.success) {
                swal("¡Éxito!", response.message, "success");
                $('#modalAgregarUsuario').modal('hide');
                $('#formAgregarUsuario')[0].reset();
                cargarUsuarios();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar el usuario';
              swal("Error", error, "error");
            }
          });
        });
        
        // Actualizar usuario
        $('#btnActualizarUsuario').click(function() {
          var form = $('#formEditarUsuario')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var formData = {
            id: $('#editar_id').val(),
            nombre: $('#editar_nombre').val(),
            cedula: $('#editar_cedula').val(),
            email: $('#editar_email').val(),
            telefono: $('#editar_telefono').val(),
            rol_id: $('#editar_rol_id').val(),
            estado: $('#editar_estado').val(),
            action: 'actualizar'
          };
          
          // Solo incluir password si se proporcionó uno nuevo
          var password = $('#editar_password').val();
          if (password && password.trim() !== '') {
            formData.password = password;
          }
          
          $.ajax({
            url: 'api.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            xhrFields: {
              withCredentials: true
            },
            crossDomain: false,
            success: function(response) {
              if (response.success) {
                swal("¡Éxito!", response.message, "success");
                $('#modalEditarUsuario').modal('hide');
                cargarUsuarios();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al actualizar el usuario';
              swal("Error", error, "error");
            }
          });
        });
      });
      
      // Función para editar usuario
      function editarUsuario(id) {
        // Cargar roles en el select de edición
        $.ajax({
          url: '../roles/api.php?action=listar',
          method: 'GET',
          dataType: 'json',
          xhrFields: {
            withCredentials: true
          },
          crossDomain: false,
          success: function(response) {
            if (response.success) {
              var select = $('#editar_rol_id');
              select.empty().append('<option value="">Seleccione un rol</option>');
              response.data.forEach(function(rol) {
                if (rol.estado === 'activo') {
                  select.append('<option value="' + rol.id + '">' + rol.nombre + '</option>');
                }
              });
            }
          }
        });
        
        // Cargar datos del usuario
        $.ajax({
          url: 'api.php?action=obtener&id=' + id,
          method: 'GET',
          dataType: 'json',
          xhrFields: {
            withCredentials: true
          },
          crossDomain: false,
          success: function(response) {
            if (response.success) {
              var u = response.data;
              $('#editar_id').val(u.id);
              $('#editar_nombre').val(u.nombre);
              $('#editar_cedula').val(u.cedula);
              $('#editar_email').val(u.email);
              $('#editar_telefono').val(u.telefono || '');
              $('#editar_rol_id').val(u.rol_id);
              $('#editar_estado').val(u.estado);
              $('#editar_password').val(''); // Limpiar campo de contraseña
              $('#modalEditarUsuario').modal('show');
            } else {
              swal("Error", response.message || "No se pudo cargar el usuario", "error");
            }
          },
          error: function(xhr) {
            var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al cargar el usuario';
            swal("Error", error, "error");
          }
        });
      }
      
      // Función para desactivar usuario
      function desactivarUsuario(id) {
        swal({
          title: "¿Está seguro?",
          text: "El usuario será marcado como inactivo",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            $.ajax({
              url: 'api.php',
              method: 'POST',
              xhrFields: {
                withCredentials: true
              },
              crossDomain: false,
              data: {
                id: id,
                action: 'desactivar'
              },
              dataType: 'json',
              success: function(response) {
                if (response.success) {
                  swal("¡Éxito!", response.message, "success");
                  cargarUsuarios();
                } else {
                  swal("Error", response.message, "error");
                }
              },
              error: function(xhr) {
                var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al desactivar el usuario';
                swal("Error", error, "error");
              }
            });
          }
        });
      }
      
      // Función para activar usuario
      function activarUsuario(id) {
        swal({
          title: "¿Activar usuario?",
          text: "El usuario será marcado como activo",
          icon: "info",
          buttons: true,
        })
        .then((willActivate) => {
          if (willActivate) {
            $.ajax({
              url: 'api.php',
              method: 'POST',
              xhrFields: {
                withCredentials: true
              },
              crossDomain: false,
              data: {
                id: id,
                action: 'activar'
              },
              dataType: 'json',
              success: function(response) {
                if (response.success) {
                  swal("¡Éxito!", response.message, "success");
                  cargarUsuarios();
                } else {
                  swal("Error", response.message, "error");
                }
              },
              error: function(xhr) {
                var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al activar el usuario';
                swal("Error", error, "error");
              }
            });
          }
        });
      }
    </script>
  </body>
</html>

