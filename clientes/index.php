<?php
/**
 * Gestión de Clientes
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
    <title>Gestión de Clientes - Sistema de Reciclaje</title>
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
              $currentRoute = 'clientes';
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
                <h3 class="fw-bold mb-3">Gestión de Clientes</h3>
                <h6 class="op-7 mb-2">Administra los clientes del sistema</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalAgregarCliente">
                  <i class="fa fa-plus"></i> Nuevo Cliente
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Lista de Clientes</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="clientesTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>Nombre/Razón Social</th>
                            <th>Cédula/RUC</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
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

        <?php include __DIR__ . '/../includes/footer.php'; ?>
      </div>
    </div>

    <!-- Modal Agregar Cliente -->
    <div class="modal fade" id="modalAgregarCliente" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formAgregarCliente">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Nombre / Razón Social <span class="text-danger">*</span></label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Industrias ABC" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Cédula / RUC</label>
                    <input type="text" id="cedula_ruc" name="cedula_ruc" class="form-control" placeholder="0998765432001">
                    <small class="form-text text-muted">Cédula (10 dígitos) o RUC (13 dígitos)</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Tipo de Documento</label>
                    <select id="tipo_documento" name="tipo_documento" class="form-control">
                      <option value="cedula">Cédula</option>
                      <option value="ruc">RUC</option>
                      <option value="pasaporte">Pasaporte</option>
                      <option value="otro">Otro</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="cliente@email.com">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" class="form-control" placeholder="02-2345678">
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
                    <label>Persona de Contacto</label>
                    <input type="text" id="contacto" name="contacto" class="form-control" placeholder="Ej: Juan Pérez">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Tipo de Cliente</label>
                    <select id="tipo_cliente" name="tipo_cliente" class="form-control">
                      <option value="minorista">Minorista</option>
                      <option value="mayorista">Mayorista</option>
                      <option value="empresa">Empresa</option>
                      <option value="institucion">Institución</option>
                    </select>
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
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Notas</label>
                    <textarea id="notas" name="notas" class="form-control" rows="2" placeholder="Notas adicionales"></textarea>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarCliente">Guardar Cliente</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Editar Cliente -->
    <div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formEditarCliente">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Nombre / Razón Social <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="Industrias ABC" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Cédula / RUC <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="0998765432001" required>
                    <small class="form-text text-muted">Cédula (10 dígitos) o RUC (13 dígitos)</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Tipo</label>
                    <select class="form-control">
                      <option value="empresa" selected>Empresa</option>
                      <option value="persona_natural">Persona Natural</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" value="contacto@industriasabc.com">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" class="form-control" value="02-2345678">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Dirección</label>
                    <textarea class="form-control" rows="2">Av. Principal 123, Quito</textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Ciudad</label>
                    <input type="text" class="form-control" value="Quito">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Estado</label>
                    <select class="form-control">
                      <option value="activo" selected>Activo</option>
                      <option value="inactivo">Inactivo</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary">Actualizar Cliente</button>
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
        var table = $('#clientesTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          }
        });
        
        // Cargar clientes
        function cargarClientes() {
          $.ajax({
            url: 'api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                table.clear();
                response.data.forEach(function(cliente) {
                  var badgeEstado = cliente.estado === 'activo' 
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';
                  
                  table.row.add([
                    '<strong>' + cliente.nombre + '</strong>',
                    cliente.cedula_ruc || '-',
                    cliente.email || '-',
                    cliente.telefono || '-',
                    cliente.direccion || '-',
                    badgeEstado,
                    '<button class="btn btn-link btn-primary btn-sm" onclick="editarCliente(' + cliente.id + ')"><i class="fa fa-edit"></i></button> ' +
                    '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarCliente(' + cliente.id + ')"><i class="fa fa-times"></i></button>'
                  ]);
                });
                table.draw();
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar los clientes", "error");
            }
          });
        }
        
        window.cargarClientes = cargarClientes;
        
        // Guardar nuevo cliente
        $('#btnGuardarCliente').click(function() {
          var form = $('#formAgregarCliente')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var formData = {
            nombre: $('#nombre').val(),
            cedula_ruc: $('#cedula_ruc').val(),
            tipo_documento: $('#tipo_documento').val(),
            direccion: $('#direccion').val(),
            telefono: $('#telefono').val(),
            email: $('#email').val(),
            contacto: $('#contacto').val(),
            tipo_cliente: $('#tipo_cliente').val(),
            estado: $('#estado').val(),
            notas: $('#notas').val(),
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
                $('#modalAgregarCliente').modal('hide');
                $('#formAgregarCliente')[0].reset();
                cargarClientes();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar el cliente';
              swal("Error", error, "error");
            }
          });
        });
        
        // Cargar datos al iniciar
        cargarClientes();
      });
      
      function editarCliente(id) {
        swal("Próximamente", "La funcionalidad de edición estará disponible pronto", "info");
      }
      
      function eliminarCliente(id) {
        swal({
          title: "¿Está seguro?",
          text: "El cliente será desactivado",
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
                  cargarClientes();
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

