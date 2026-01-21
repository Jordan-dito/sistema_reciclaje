<?php
/**
 * Gestión de Productos
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
    <title>Material Comercializable - Sistema de Reciclaje</title>
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
              $currentRoute = 'productos';
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
                <h3 class="fw-bold mb-3">Material Comercializable</h3>
                <h6 class="op-7 mb-2">Administra los productos con sus precios y unidades</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
                  <i class="fa fa-plus"></i> Nuevo Producto
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Lista de Productos</div>
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
                      <table id="productosTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>Código</th>
                            <th>Material</th>
                            <th>Categoría</th>
                            <th>Unidad</th>
                            <th>Precio Venta</th>
                            <th>Precio Compra</th>
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

    <!-- Modal Agregar Producto -->
    <div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formAgregarProducto">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="material_id">Material <span class="text-danger">*</span></label>
                    <select id="material_id" name="material_id" class="form-control" required>
                      <option value="">Seleccione un material</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="unidad_id">Unidad <span class="text-danger">*</span></label>
                    <select id="unidad_id" name="unidad_id" class="form-control" required disabled>
                      <option value="">Seleccione una unidad</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="descripcion">Descripción <span class="text-danger">*</span></label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="precio_venta">Precio de Venta ($) <span class="text-danger">*</span></label>
                    <input type="number" id="precio_venta" name="precio_venta" class="form-control" step="0.01" min="0.01" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="precio_compra">Precio de Compra ($) <span class="text-danger">*</span></label>
                    <input type="number" id="precio_compra" name="precio_compra" class="form-control" step="0.01" min="0.01" required>
                  </div>
                </div>
                <input type="hidden" id="estado" name="estado" value="activo">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarProducto">Guardar Producto</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Editar Producto -->
    <div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formEditarProducto">
              <input type="hidden" id="edit_id" name="id">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Código</label>
                    <input type="text" id="edit_nombre" name="nombre" class="form-control" disabled readonly>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Material</label>
                    <select id="edit_material_id" name="material_id" class="form-control" disabled>
                      <option value="">Seleccione un material</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Unidad</label>
                    <select id="edit_unidad_id" name="unidad_id" class="form-control" disabled>
                      <option value="">Seleccione una unidad</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Precio de Venta ($) <span class="text-danger">*</span></label>
                    <input type="number" id="edit_precio_venta" name="precio_venta" class="form-control" step="0.01" min="0.01" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Precio de Compra ($) <span class="text-danger">*</span></label>
                    <input type="number" id="edit_precio_compra" name="precio_compra" class="form-control" step="0.01" min="0.01" required>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Descripción <span class="text-danger">*</span></label>
                    <textarea id="edit_descripcion" name="descripcion" class="form-control" rows="3" required></textarea>
                  </div>
                </div>
                <input type="hidden" id="edit_estado" name="estado" value="activo">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnActualizarProducto">Actualizar Producto</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modales Globales -->
    <?php 
      include __DIR__ . '/../includes/modal-foto-perfil.php';
      include __DIR__ . '/../includes/modal-cambiar-password.php';
    ?>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugin/select2/select2.full.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/setting-demo.js"></script>
    <?php
      $basePath = '..';
      include __DIR__ . '/../includes/footer-scripts.php';
    ?>
    <script>
      var materialesList = [];
      var productosList = [];

      function cargarMateriales() {
        $.ajax({
          url: 'api.php?action=materiales',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              var selectAdd = $('#material_id');
              var selectEdit = $('#edit_material_id');
              selectAdd.html('<option value="">Seleccione un material</option>');
              selectEdit.html('<option value="">Seleccione un material</option>');
              
              materialesList = response.data;
              response.data.forEach(function(mat) {
                selectAdd.append('<option value="' + mat.id + '">' + mat.nombre + '</option>');
                selectEdit.append('<option value="' + mat.id + '">' + mat.nombre + '</option>');
              });
              
              if (!selectAdd.hasClass('select2-hidden-accessible')) {
                selectAdd.select2({
                  placeholder: 'Buscar o seleccionar material',
                  allowClear: true,
                  dropdownParent: $('#modalAgregarProducto')
                });
              }
              
              if (!selectEdit.hasClass('select2-hidden-accessible')) {
                selectEdit.select2({
                  placeholder: 'Buscar o seleccionar material',
                  allowClear: true,
                  dropdownParent: $('#modalEditarProducto')
                });
              }
            }
          }
        });
      }

      function cargarUnidades() {
        $.ajax({
          url: 'api.php?action=unidades',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              var selectAdd = $('#unidad_id');
              var selectEdit = $('#edit_unidad_id');
              selectAdd.html('<option value="">Seleccione una unidad</option>');
              selectEdit.html('<option value="">Seleccione una unidad</option>');
              response.data.forEach(function(uni) {
                var texto = uni.nombre + ' (' + uni.simbolo + ')';
                selectAdd.append('<option value="' + uni.id + '">' + texto + '</option>');
                selectEdit.append('<option value="' + uni.id + '">' + texto + '</option>');
              });
            }
          }
        });
      }

      $(document).ready(function() {
        var table = $('#productosTable').DataTable({
          "language": { "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" }
        });
        
        var estadoActual = 'activos';

        window.cambiarFiltroEstado = function(nuevoEstado) {
          estadoActual = nuevoEstado;
          cargarProductos();
        };

        cargarMateriales();
        cargarUnidades();

        $('#material_id').on('change', function() {
          var selectedMaterialId = $(this).val();
          var selectUnidad = $('#unidad_id');
          
          if (selectedMaterialId) {
            // 1. Buscar si ya hay un producto ACTIVO (para evitar duplicados)
            var productoActivo = productosList.find(function(p) {
              return p.material_id == selectedMaterialId && p.estado === 'activo';
            });

            if (productoActivo) {
              swal({
                title: "Material ya registrado",
                text: "Este material ya tiene un producto activo (Código: " + productoActivo.nombre + "). No puede registrarlo de nuevo.",
                icon: "error"
              });
              $(this).val(null).trigger('change');
              selectUnidad.val('').trigger('change').prop('disabled', true);
              return;
            }

            // 2. Buscar si el material ya se ha usado antes (para sugerir la unidad)
            var registroPrevio = productosList.find(function(p) {
              return p.material_id == selectedMaterialId;
            });

            if (registroPrevio) {
              // Si existe un registro previo, autocompletar la unidad y bloquear
              selectUnidad.val(registroPrevio.unidad_id).trigger('change').prop('disabled', true);
            } else {
              // Si es un material totalmente nuevo, permitir elegir unidad
              selectUnidad.val('').trigger('change').prop('disabled', false);
            }
          } else {
            selectUnidad.val('').trigger('change').prop('disabled', true);
          }
        });
        
        $('#modalAgregarProducto').on('shown.bs.modal', function() {
          if (!$('#material_id').hasClass('select2-hidden-accessible')) {
            $('#material_id').select2({
              placeholder: 'Buscar o seleccionar material',
              allowClear: true,
              dropdownParent: $('#modalAgregarProducto')
            });
          }
          $('#unidad_id').prop('disabled', true);
          $('#material_id').val(null).trigger('change');
        });
        
        $('#modalAgregarProducto').on('hidden.bs.modal', function() {
          $('#material_id').val(null).trigger('change');
          $('#unidad_id').val('').trigger('change').prop('disabled', true);
          $('#formAgregarProducto')[0].reset();
        });
        
        $('#modalEditarProducto').on('shown.bs.modal', function() {
          if (!$('#edit_material_id').hasClass('select2-hidden-accessible')) {
            $('#edit_material_id').select2({
              placeholder: 'Buscar o seleccionar material',
              allowClear: true,
              dropdownParent: $('#modalEditarProducto'),
              disabled: true
            });
          } else {
            $('#edit_material_id').prop('disabled', true);
          }
          $('#edit_unidad_id').prop('disabled', true);
        });

        function cargarProductos() {
          $.ajax({
            url: 'api.php?action=listar&estado=' + estadoActual,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                productosList = response.data;
                table.clear();
                response.data.forEach(function(producto) {
                  var badgeEstado = producto.estado === 'activo' 
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';
                  var precioVenta = producto.precio_venta ? '$' + parseFloat(producto.precio_venta).toFixed(2) : '-';
                  var precioCompra = producto.precio_compra ? '$' + parseFloat(producto.precio_compra).toFixed(2) : '-';
                  var unidad = producto.unidad_simbolo || producto.unidad_nombre || '-';
                  
                  var botones = '<button class="btn btn-link btn-primary btn-sm" onclick="editarProducto(' + producto.id + ')"><i class="fa fa-edit"></i></button> ';
                  
                  if (estadoActual === 'activos') {
                    botones += '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarProducto(' + producto.id + ')"><i class="fa fa-times"></i></button>';
                  } else {
                    botones += '<button class="btn btn-link btn-success btn-sm" onclick="activarProducto(' + producto.id + ')"><i class="fa fa-check"></i></button>';
                  }

                  table.row.add([
                    '<strong>' + producto.nombre + '</strong>',
                    producto.material_nombre || '-',
                    producto.categoria_nombre || '-',
                    unidad,
                    precioVenta,
                    precioCompra,
                    badgeEstado,
                    botones
                  ]);
                });
                table.draw();
              }
            }
          });
        }

        $('#btnGuardarProducto').click(function() {
          var form = $('#formAgregarProducto')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var material_id = $('#material_id').val();
          var unidad_id = $('#unidad_id').val();
          var descripcion = $('#descripcion').val().trim();
          var precio_venta = parseFloat($('#precio_venta').val());
          var precio_compra = parseFloat($('#precio_compra').val());
          
          if (!material_id || !unidad_id || !descripcion || isNaN(precio_venta) || precio_venta <= 0 || isNaN(precio_compra) || precio_compra <= 0) {
            swal("Error", "Todos los campos son obligatorios y los precios deben ser mayores a 0", "error");
            return;
          }
          
          $.ajax({
            url: 'api.php',
            method: 'POST',
            data: {
              material_id: material_id,
              unidad_id: unidad_id,
              descripcion: descripcion,
              precio_venta: precio_venta,
              precio_compra: precio_compra,
              estado: $('#estado').val(),
              action: 'crear'
            },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                swal("¡Éxito!", response.message, "success");
                $('#modalAgregarProducto').modal('hide');
                cargarProductos();
              } else {
                swal("Error", response.message, "error");
              }
            }
          });
        });

        $('#btnActualizarProducto').click(function() {
          var form = $('#formEditarProducto')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var id = $('#edit_id').val();
          var material_id = $('#edit_material_id').val();
          var unidad_id = $('#edit_unidad_id').val();
          var descripcion = $('#edit_descripcion').val().trim();
          var precio_venta = parseFloat($('#edit_precio_venta').val());
          var precio_compra = parseFloat($('#edit_precio_compra').val());
          
          $.ajax({
            url: 'api.php',
            method: 'POST',
            data: {
              id: id,
              nombre: $('#edit_nombre').val(),
              material_id: material_id,
              unidad_id: unidad_id,
              descripcion: descripcion,
              precio_venta: precio_venta,
              precio_compra: precio_compra,
              estado: $('#edit_estado').val() || 'activo',
              action: 'actualizar'
            },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                swal("¡Éxito!", response.message, "success");
                $('#modalEditarProducto').modal('hide');
                cargarProductos();
              } else {
                swal("Error", response.message, "error");
              }
            }
          });
        });

        window.editarProducto = function(id) {
          $.ajax({
            url: 'api.php?action=obtener&id=' + id,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var prod = response.data;
                $('#edit_id').val(prod.id);
                $('#edit_nombre').val(prod.nombre);
                $('#edit_material_id').val(prod.material_id).trigger('change');
                $('#edit_unidad_id').val(prod.unidad_id).trigger('change');
                $('#edit_descripcion').val(prod.descripcion || '');
                $('#edit_material_id').prop('disabled', true);
                $('#edit_unidad_id').prop('disabled', true);
                
                var precioVenta = 0;
                var precioCompra = 0;
                if (prod.precios) {
                  prod.precios.forEach(function(precio) {
                    if (precio.tipo_precio === 'venta' && precio.estado === 'activo') precioVenta = precio.precio_unitario;
                    if (precio.tipo_precio === 'compra' && precio.estado === 'activo') precioCompra = precio.precio_unitario;
                  });
                }
                $('#edit_precio_venta').val(precioVenta);
                $('#edit_precio_compra').val(precioCompra);
                $('#modalEditarProducto').modal('show');
              }
            }
          });
        };

        window.eliminarProducto = function(id) {
          swal({
            title: "¿Está seguro?",
            text: "El producto será desactivado",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          }).then((willDelete) => {
            if (willDelete) {
              $.ajax({
                url: 'api.php',
                method: 'POST',
                data: { id: id, action: 'eliminar' },
                dataType: 'json',
                success: function(response) {
                  if (response.success) {
                    swal("¡Éxito!", response.message, "success");
                    cargarProductos();
                  } else {
                    swal("Error", response.message, "error");
                  }
                }
              });
            }
          });
        };

        window.activarProducto = function(id) {
          swal({
            title: "¿Desea activar el producto?",
            text: "El producto volverá a estar disponible",
            icon: "info",
            buttons: true,
          }).then((willActivate) => {
            if (willActivate) {
              $.ajax({
                url: 'api.php',
                method: 'POST',
                data: { id: id, action: 'activar' },
                dataType: 'json',
                success: function(response) {
                  if (response.success) {
                    swal("¡Éxito!", response.message, "success");
                    cargarProductos();
                  } else {
                    swal("Error", response.message, "error");
                  }
                }
              });
            }
          });
        };

        cargarProductos();
      });
    </script>
  </body>
</html>