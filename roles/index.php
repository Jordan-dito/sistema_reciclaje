<?php
/**
 * Gestión de Roles
 * Sistema de Gestión de Reciclaje
 */

// Verificar autenticación
require_once __DIR__ . '/../config/auth.php';

// Verificar si el usuario está autenticado
$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../index.php');
    exit;
}

// Obtener datos del usuario actual
$usuario = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Gestión de Roles - Sistema de Reciclaje</title>
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
        <div class="sidebar-logo">
          <div class="logo-header" data-background-color="dark">
            <a href="../Dashboard.php" class="logo">
              <img
                src="../assets/img/kaiadmin/logo_light.svg"
                alt="navbar brand"
                class="navbar-brand"
                height="20"
              />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <?php
              $basePath = '..';
              $currentRoute = 'roles';
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
                  src="../assets/img/kaiadmin/logo_light.svg"
                  alt="navbar brand"
                  class="navbar-brand"
                  height="20"
                />
              </a>
            </div>
          </div>
          <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
            <div class="container-fluid">
              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                      <img src="../assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <li><a class="dropdown-item" href="../config/logout.php">Cerrar Sesión</a></li>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Gestión de Roles</h3>
                <h6 class="op-7 mb-2">Administra los roles y permisos del sistema</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalAgregarRol">
                  <i class="fa fa-plus"></i> Nuevo Rol
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Gestión de Roles y Módulos</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <!-- Pestañas -->
                    <ul class="nav nav-pills nav-secondary" id="pills-tab" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-modulos-tab" data-bs-toggle="tab" data-bs-target="#pills-modulos" type="button" role="tab" aria-controls="pills-modulos" aria-selected="true">
                          <i class="fas fa-th-large"></i> Módulos
                        </button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-roles-tab" data-bs-toggle="tab" data-bs-target="#pills-roles" type="button" role="tab" aria-controls="pills-roles" aria-selected="false">
                          <i class="fas fa-user-tag"></i> Roles
                        </button>
                      </li>
                    </ul>
                    
                    <div class="tab-content mt-2 mb-3" id="pills-tabContent">
                      <!-- Pestaña de Módulos (por defecto) -->
                      <div class="tab-pane fade show active" id="pills-modulos" role="tabpanel" aria-labelledby="pills-modulos-tab">
                        <div class="row mt-3">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label>Seleccionar Rol</label>
                              <select id="selectRolModulos" class="form-control">
                                <option value="">-- Seleccione un rol --</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="row mt-3">
                          <div class="col-md-12">
                            <div class="table-responsive">
                              <table id="modulosTable" class="display table table-striped table-hover">
                                <thead>
                                  <tr>
                                    <th>Módulo</th>
                                    <th>Descripción</th>
                                    <th>Ruta</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Pestaña de Roles -->
                      <div class="tab-pane fade" id="pills-roles" role="tabpanel" aria-labelledby="pills-roles-tab">
                        <div class="table-responsive mt-3">
                          <table id="rolesTable" class="display table table-striped table-hover">
                            <thead>
                              <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                              </tr>
                            </thead>
                            <tbody>
                              <!-- Los datos se cargarán dinámicamente -->
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

        <footer class="footer">
          <div class="container-fluid d-flex justify-content-between">
            <div class="copyright">
              2024, Sistema de Gestión de Reciclaje
            </div>
          </div>
        </footer>
      </div>
    </div>

    <!-- Modal Agregar Rol -->
    <div class="modal fade" id="modalAgregarRol" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Rol</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formAgregarRol">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Nombre del Rol</label>
                    <input type="text" class="form-control" placeholder="Ej: Supervisor" required>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Descripción</label>
                    <textarea class="form-control" rows="3" placeholder="Descripción del rol"></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Estado</label>
                    <select class="form-control">
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
            <button type="button" class="btn btn-primary">Guardar Rol</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Editar Rol -->
    <div class="modal fade" id="modalEditarRol" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Rol</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formEditarRol">
              <input type="hidden" id="editar_id" name="id">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Nombre del Rol *</label>
                    <input type="text" id="editar_nombre" name="nombre" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="editar_descripcion" name="descripcion" class="form-control" rows="3"></textarea>
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
            <button type="button" class="btn btn-primary" id="btnActualizarRol">Actualizar Rol</button>
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
        var table = $('#rolesTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          },
          "order": [[0, "desc"]]
        });
        
        var modulosTable = $('#modulosTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          },
          "order": [[0, "asc"]]
        });
        
        // Cargar roles en el select y en la tabla
        window.cargarRoles = function() {
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
                response.data.forEach(function(rol) {
                  var badgeEstado = rol.estado === 'activo' 
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';
                  
                  // Botón de estado: Activar si está inactivo, Desactivar si está activo
                  var botonEstado = '';
                  if (rol.estado === 'activo') {
                    botonEstado = '<button class="btn btn-link btn-danger btn-sm" onclick="desactivarRol(' + rol.id + ')" title="Desactivar"><i class="fa fa-times"></i></button>';
                  } else {
                    botonEstado = '<button class="btn btn-link btn-success btn-sm" onclick="activarRol(' + rol.id + ')" title="Activar"><i class="fa fa-check"></i></button>';
                  }
                  
                  table.row.add([
                    rol.id,
                    '<strong>' + rol.nombre + '</strong>',
                    rol.descripcion || '-',
                    badgeEstado,
                    rol.fecha_creacion || '-',
                    '<button class="btn btn-link btn-primary btn-sm" onclick="editarRol(' + rol.id + ')" title="Editar"><i class="fa fa-edit"></i></button> ' +
                    botonEstado
                  ]);
                });
                table.draw();
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar los roles", "error");
            }
          });
        }
        
        // Cargar roles al iniciar
        cargarRoles();
        
        // Cargar roles en el select de módulos
        function cargarRolesEnSelect() {
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
                var select = $('#selectRolModulos');
                select.empty().append('<option value="">-- Seleccione un rol --</option>');
                var rolGerenteId = null;
                response.data.forEach(function(rol) {
                  select.append('<option value="' + rol.id + '">' + rol.nombre + '</option>');
                  // Identificar el rol Gerente
                  if (rol.nombre.toLowerCase() === 'gerente') {
                    rolGerenteId = rol.id;
                  }
                });
                
                // Si existe el rol Gerente, seleccionarlo automáticamente y cargar sus módulos
                if (rolGerenteId) {
                  select.val(rolGerenteId);
                  cargarModulosPorRol(rolGerenteId);
                }
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar los roles", "error");
            }
          });
        }
        
        // Cargar módulos cuando se selecciona un rol
        $('#selectRolModulos').on('change', function() {
          var rolId = $(this).val();
          if (rolId) {
            cargarModulosPorRol(rolId);
          } else {
            modulosTable.clear().draw();
          }
        });
        
        // Cargar módulos por rol (función global)
        window.cargarModulosPorRol = function(rolId) {
          $.ajax({
            url: 'api.php?action=modulos_por_rol&rol_id=' + rolId,
            method: 'GET',
            dataType: 'json',
            xhrFields: {
              withCredentials: true
            },
            crossDomain: false,
            success: function(response) {
              if (response.success) {
                modulosTable.clear();
                
                // Primero obtener todos los módulos disponibles
                $.ajax({
                  url: 'api.php?action=listar_modulos',
                  method: 'GET',
                  dataType: 'json',
                  xhrFields: {
                    withCredentials: true
                  },
                  crossDomain: false,
                  success: function(modulosResponse) {
                    if (modulosResponse.success) {
                      modulosResponse.data.forEach(function(modulo) {
                        var estaAsignado = modulo.asignado === 1 || false;
                        var badgeEstado = modulo.estado === 'activo' 
                          ? '<span class="badge badge-success">Activo</span>'
                          : '<span class="badge badge-danger">Inactivo</span>';
                        
                        var botonAccion = '';
                        if (estaAsignado) {
                          botonAccion = '<span class="badge badge-success"><i class="fa fa-check"></i> Asignado</span>';
                        } else {
                          botonAccion = '<span class="badge badge-secondary"><i class="fa fa-times"></i> No asignado</span>';
                        }
                        
                        modulosTable.row.add([
                          '<i class="' + (modulo.icono || 'fas fa-cube') + '"></i> <strong>' + modulo.nombre + '</strong>',
                          modulo.descripcion || '-',
                          modulo.ruta || '-',
                          badgeEstado,
                          botonAccion
                        ]);
                      });
                      modulosTable.draw();
                    }
                  },
                  error: function() {
                    swal("Error", "No se pudieron cargar los módulos", "error");
                  }
                });
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar los módulos del rol", "error");
            }
          });
        }
        
        // Cargar roles en el select al iniciar
        cargarRolesEnSelect();
        
        // Actualizar rol
        $('#btnActualizarRol').click(function() {
          var form = $('#formEditarRol')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var formData = {
            id: $('#editar_id').val(),
            nombre: $('#editar_nombre').val(),
            descripcion: $('#editar_descripcion').val(),
            estado: $('#editar_estado').val(),
            action: 'actualizar'
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
                $('#modalEditarRol').modal('hide');
                cargarRoles();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al actualizar el rol';
              swal("Error", error, "error");
            }
          });
        });
      });
      
      function editarRol(id) {
        // Cargar datos del rol
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
              var r = response.data;
              $('#editar_id').val(r.id);
              $('#editar_nombre').val(r.nombre);
              $('#editar_descripcion').val(r.descripcion || '');
              $('#editar_estado').val(r.estado);
              $('#modalEditarRol').modal('show');
            } else {
              swal("Error", response.message || "No se pudo cargar el rol", "error");
            }
          },
          error: function(xhr) {
            var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al cargar el rol';
            swal("Error", error, "error");
          }
        });
      }
      
      function desactivarRol(id) {
        swal({
          title: "¿Está seguro?",
          text: "El rol será marcado como inactivo",
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
              data: { id: id, action: 'desactivar' },
              dataType: 'json',
              success: function(response) {
                if (response.success) {
                  swal("¡Éxito!", response.message, "success");
                  cargarRoles();
                } else {
                  swal("Error", response.message, "error");
                }
              },
              error: function(xhr) {
                var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al desactivar el rol';
                swal("Error", error, "error");
              }
            });
          }
        });
      }
      
      function activarRol(id) {
        swal({
          title: "¿Activar rol?",
          text: "El rol será marcado como activo",
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
              data: { id: id, action: 'activar' },
              dataType: 'json',
              success: function(response) {
                if (response.success) {
                  swal("¡Éxito!", response.message, "success");
                  cargarRoles();
                } else {
                  swal("Error", response.message, "error");
                }
              },
              error: function(xhr) {
                var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al activar el rol';
                swal("Error", error, "error");
              }
            });
          }
        });
      }
      
      // Asignar módulo a un rol
      function asignarModulo(rolId, ruta) {
        $.ajax({
          url: 'api.php',
          method: 'POST',
          data: {
            action: 'asignar_modulo',
            rol_id: rolId,
            ruta: ruta
          },
          dataType: 'json',
          xhrFields: {
            withCredentials: true
          },
          crossDomain: false,
          success: function(response) {
            if (response.success) {
              swal("¡Éxito!", response.message, "success");
              cargarModulosPorRol(rolId);
            } else {
              swal("Error", response.message, "error");
            }
          },
          error: function(xhr) {
            var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al asignar el módulo';
            swal("Error", error, "error");
          }
        });
      }
      
      // Quitar módulo de un rol
      function quitarModulo(rolId, ruta) {
        swal({
          title: "¿Está seguro?",
          text: "El módulo será removido de este rol",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            $.ajax({
              url: 'api.php',
              method: 'POST',
              data: {
                action: 'quitar_modulo',
                rol_id: rolId,
                ruta: ruta
              },
              dataType: 'json',
              xhrFields: {
                withCredentials: true
              },
              crossDomain: false,
              success: function(response) {
                if (response.success) {
                  swal("¡Éxito!", response.message, "success");
                  cargarModulosPorRol(rolId);
                } else {
                  swal("Error", response.message, "error");
                }
              },
              error: function(xhr) {
                var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al quitar el módulo';
                swal("Error", error, "error");
              }
            });
          }
        });
      }
    </script>
  </body>
</html>

