<?php
/**
 * Gestión de Sucursales
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
    <title>Gestión de Sucursales - Sistema de Reciclaje</title>
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
              $currentRoute = 'sucursales';
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
                <h3 class="fw-bold mb-3">Gestión de Sucursales</h3>
                <h6 class="op-7 mb-2">Administra las sucursales del sistema</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalAgregarSucursal">
                  <i class="fa fa-plus"></i> Nueva Sucursal
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Lista de Sucursales</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="sucursalesTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Responsable</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Los datos se cargarán dinámicamente desde la base de datos -->
                        </tbody>
                      </table>
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

    <!-- Modal Agregar Sucursal -->
    <div class="modal fade" id="modalAgregarSucursal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nueva Sucursal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formAgregarSucursal">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Nombre de la Sucursal *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Sucursal Este" required>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Dirección</label>
                    <textarea id="direccion" name="direccion" class="form-control" rows="2" placeholder="Dirección completa"></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" class="form-control" placeholder="555-0000">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="sucursal@email.com">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Responsable</label>
                    <select id="responsable_id" name="responsable_id" class="form-control">
                      <option value="">Seleccione un responsable</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Estado</label>
                    <select id="estado" name="estado" class="form-control">
                      <option value="activa">Activa</option>
                      <option value="inactiva">Inactiva</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarSucursal">Guardar Sucursal</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Editar Sucursal -->
    <div class="modal fade" id="modalEditarSucursal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Sucursal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formEditarSucursal">
              <input type="hidden" id="editar_id" name="id">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Nombre de la Sucursal *</label>
                    <input type="text" id="editar_nombre" name="nombre" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Dirección</label>
                    <textarea id="editar_direccion" name="direccion" class="form-control" rows="2"></textarea>
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
                    <label>Email</label>
                    <input type="email" id="editar_email" name="email" class="form-control">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Responsable</label>
                    <select id="editar_responsable_id" name="responsable_id" class="form-control">
                      <option value="">Seleccione un responsable</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Estado</label>
                    <select id="editar_estado" name="estado" class="form-control">
                      <option value="activa">Activa</option>
                      <option value="inactiva">Inactiva</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnActualizarSucursal">Actualizar Sucursal</button>
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
        var table = $('#sucursalesTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          }
        });
        
        var usuarios = [];
        
        // Cargar usuarios para el select de responsables
        function cargarUsuarios() {
          $.ajax({
            url: '../usuarios/api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                usuarios = response.data;
                var select = $('#responsable_id');
                select.empty().append('<option value="">Seleccione un responsable</option>');
                response.data.forEach(function(usuario) {
                  if (usuario.estado === 'activo') {
                    select.append('<option value="' + usuario.id + '">' + usuario.nombre + '</option>');
                  }
                });
              }
            }
          });
        }
        
        // Cargar sucursales
        function cargarSucursales() {
          $.ajax({
            url: 'api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                table.clear();
                response.data.forEach(function(sucursal) {
                  var badgeEstado = sucursal.estado === 'activa' 
                    ? '<span class="badge badge-success">Activa</span>'
                    : '<span class="badge badge-danger">Inactiva</span>';
                  
                  table.row.add([
                    sucursal.id,
                    '<strong>' + sucursal.nombre + '</strong>',
                    sucursal.direccion || '-',
                    sucursal.telefono || '-',
                    sucursal.email || '-',
                    sucursal.responsable_nombre || '-',
                    badgeEstado,
                    '<button class="btn btn-link btn-primary btn-sm" onclick="editarSucursal(' + sucursal.id + ')"><i class="fa fa-edit"></i></button> ' +
                    '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarSucursal(' + sucursal.id + ')"><i class="fa fa-times"></i></button>'
                  ]);
                });
                table.draw();
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar las sucursales", "error");
            }
          });
        }
        
        window.cargarSucursales = cargarSucursales;
        
        // Cargar datos al iniciar
        cargarUsuarios();
        cargarSucursales();
        
        // Guardar nueva sucursal
        $('#btnGuardarSucursal').click(function() {
          var form = $('#formAgregarSucursal')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var formData = {
            nombre: $('#nombre').val(),
            direccion: $('#direccion').val(),
            telefono: $('#telefono').val(),
            email: $('#email').val(),
            responsable_id: $('#responsable_id').val(),
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
                $('#modalAgregarSucursal').modal('hide');
                $('#formAgregarSucursal')[0].reset();
                cargarSucursales();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar la sucursal';
              swal("Error", error, "error");
            }
          });
        });
        
        // Actualizar sucursal
        $('#btnActualizarSucursal').click(function() {
          var form = $('#formEditarSucursal')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var formData = {
            id: $('#editar_id').val(),
            nombre: $('#editar_nombre').val(),
            direccion: $('#editar_direccion').val(),
            telefono: $('#editar_telefono').val(),
            email: $('#editar_email').val(),
            responsable_id: $('#editar_responsable_id').val(),
            estado: $('#editar_estado').val(),
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
                $('#modalEditarSucursal').modal('hide');
                cargarSucursales();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al actualizar la sucursal';
              swal("Error", error, "error");
            }
          });
        });
      });
      
      function editarSucursal(id) {
        // Cargar usuarios en el select de edición
        $.ajax({
          url: '../usuarios/api.php?action=listar',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              var select = $('#editar_responsable_id');
              select.empty().append('<option value="">Seleccione un responsable</option>');
              response.data.forEach(function(usuario) {
                if (usuario.estado === 'activo') {
                  select.append('<option value="' + usuario.id + '">' + usuario.nombre + '</option>');
                }
              });
            }
          }
        });
        
        $.ajax({
          url: 'api.php?action=obtener&id=' + id,
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              var s = response.data;
              $('#editar_id').val(s.id);
              $('#editar_nombre').val(s.nombre);
              $('#editar_direccion').val(s.direccion);
              $('#editar_telefono').val(s.telefono);
              $('#editar_email').val(s.email);
              $('#editar_responsable_id').val(s.responsable_id);
              $('#editar_estado').val(s.estado);
              $('#modalEditarSucursal').modal('show');
            }
          }
        });
      }
      
      function eliminarSucursal(id) {
        swal({
          title: "¿Está seguro?",
          text: "La sucursal será desactivada",
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
                  cargarSucursales();
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

