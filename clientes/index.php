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
          ?>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Gestión de Clientes</h3>
                <h6 class="op-7 mb-2">Gestionar los clientes del sistema</h6>
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
                    <div class="card-category">
                      <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="pills-activos-tab" data-bs-toggle="pill" href="#pills-activos" role="tab" onclick="cambiarFiltroEstado('activos')">Activos</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="pills-inactivos-tab" data-bs-toggle="pill" href="#pills-inactivos" role="tab" onclick="cambiarFiltroEstado('inactivos')">Inactivos</a>
                        </li>
                      </ul>
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

    <!-- Modal Editar Cliente -->
    <div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalClienteTitle">Nuevo Cliente</h5>
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

    <!-- Modales Globales -->
    <?php 
      include __DIR__ . '/../includes/modal-foto-perfil.php';
      include __DIR__ . '/../includes/modal-cambiar-password.php';
    ?>

    <!-- Core JS Files -->
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
        var table = $('#clientesTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          }
        });
        
        var estadoActual = 'activos';

        window.cambiarFiltroEstado = function(nuevoEstado) {
          estadoActual = nuevoEstado;
          cargarClientes();
        };

        // Cargar clientes
        function cargarClientes() {
          $.ajax({
            url: 'api.php?action=listar&estado=' + estadoActual,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                table.clear();
                response.data.forEach(function(cliente) {
                  var badgeEstado = cliente.estado === 'activo' 
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';
                  
                  var botones = '<button class="btn btn-link btn-primary btn-sm" onclick="editarCliente(' + cliente.id + ')"><i class="fa fa-edit"></i></button> ';
                  
                  if (estadoActual === 'activos') {
                    botones += '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarCliente(' + cliente.id + ')"><i class="fa fa-times"></i></button>';
                  } else {
                    botones += '<button class="btn btn-link btn-success btn-sm" onclick="activarCliente(' + cliente.id + ')"><i class="fa fa-check"></i></button>';
                  }

                  table.row.add([
                    '<strong>' + cliente.nombre + '</strong>',
                    cliente.cedula_ruc || '-',
                    cliente.email || '-',
                    cliente.telefono || '-',
                    cliente.direccion || '-',
                    badgeEstado,
                    botones
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
        
        // Guardar cliente (crear o editar)
        $('#btnGuardarCliente').click(function() {
          var form = $('#formAgregarCliente')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var clienteId = $('#cliente_id').val();
          var action = clienteId ? 'editar' : 'crear';
          
          var formData = {
            id: clienteId,
            nombre: $('#nombre').val(),
            cedula_ruc: $('#cedula_ruc').val(),
            tipo_documento: $('#tipo_documento').val(),
            direccion: $('#direccion').val(),
            telefono: $('#telefono').val(),
            email: $('#email').val(),
            contacto: $('#contacto').val(),
            tipo_cliente: $('#tipo_cliente').val(),
            estado: 'activo',
            notas: $('#notas').val(),
            action: action
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
        
        // Resetear formulario al cerrar el modal
        $('#modalAgregarCliente').on('hidden.bs.modal', function() {
          $('#formAgregarCliente')[0].reset();
          $('#cliente_id').val('');
          $('#modalClienteTitle').text('Nuevo Cliente');
          // Habilitar campos al crear nuevo cliente
          $('#nombre').prop('readonly', false);
          $('#cedula_ruc').prop('readonly', false);
          $('#tipo_documento').prop('disabled', false);
          // Actualizar color de selects
          actualizarColorSelect();
        });
        
        // Cargar datos al iniciar
        cargarClientes();
      });
      
      function editarCliente(id) {
        // Obtener datos del cliente
        $.ajax({
          url: 'api.php',
          method: 'GET',
          data: { id: id, action: 'obtener' },
          dataType: 'json',
          success: function(response) {
            if (response.success && response.data) {
              var cliente = response.data;
              
              // Llenar el formulario con los datos
              $('#cliente_id').val(cliente.id);
              $('#nombre').val(cliente.nombre);
              $('#cedula_ruc').val(cliente.cedula_ruc || '');
              $('#tipo_documento').val(cliente.tipo_documento || '');
              $('#email').val(cliente.email || '');
              $('#telefono').val(cliente.telefono || '');
              $('#direccion').val(cliente.direccion || '');
              $('#contacto').val(cliente.contacto || '');
              $('#tipo_cliente').val(cliente.tipo_cliente || '');
              $('#notas').val(cliente.notas || '');
              
              // Deshabilitar campos que no se pueden editar
              $('#nombre').prop('readonly', true);
              $('#cedula_ruc').prop('readonly', true);
              $('#tipo_documento').prop('disabled', true);
              
              // Cambiar título del modal
              $('#modalClienteTitle').text('Editar Cliente');
              
              // Abrir modal
              $('#modalAgregarCliente').modal('show');
            } else {
              swal("Error", response.message || "No se pudo cargar el cliente", "error");
            }
          },
          error: function() {
            swal("Error", "Error al obtener los datos del cliente", "error");
          }
        });
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

      function activarCliente(id) {
        swal({
          title: "¿Desea activar el cliente?",
          text: "El cliente volverá a estar activo en el sistema",
          icon: "info",
          buttons: true,
        })
        .then((willActivate) => {
          if (willActivate) {
            $.ajax({
              url: 'api.php',
              method: 'POST',
              data: { id: id, action: 'activar' },
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

