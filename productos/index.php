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
    <title>Gestión de Productos - Sistema de Reciclaje</title>
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
            include __DIR__ . '/../includes/modal-foto-perfil.php';
          ?>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Gestión de Productos</h3>
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
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="productosTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>Nombre</th>
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
    <div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Material Comercializable</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formAgregarProducto">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Código <span class="text-danger">*</span></label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: 0001" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Material <span class="text-danger">*</span></label>
                    <select id="material_id" name="material_id" class="form-control" required>
                      <option value="">Seleccione un material</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Unidad <span class="text-danger">*</span></label>
                    <select id="unidad_id" name="unidad_id" class="form-control" required>
                      <option value="">Seleccione una unidad</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Precio de Venta ($)</label>
                    <input type="number" id="precio_venta" name="precio_venta" class="form-control" step="0.01" min="0" placeholder="0.00">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Precio de Compra ($)</label>
                    <input type="number" id="precio_compra" name="precio_compra" class="form-control" step="0.01" min="0" placeholder="0.00">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Descripción del producto"></textarea>
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
    <div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-hidden="true">
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
                    <label>Precio de Venta ($)</label>
                    <input type="number" id="edit_precio_venta" name="precio_venta" class="form-control" step="0.01" min="0">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Precio de Compra ($)</label>
                    <input type="number" id="edit_precio_compra" name="precio_compra" class="form-control" step="0.01" min="0">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="edit_descripcion" name="descripcion" class="form-control" rows="3"></textarea>
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
              response.data.forEach(function(mat) {
                selectAdd.append('<option value="' + mat.id + '">' + mat.nombre + '</option>');
                selectEdit.append('<option value="' + mat.id + '">' + mat.nombre + '</option>');
              });
              
              // Inicializar Select2 con búsqueda si no está ya inicializado
              if (!selectAdd.hasClass('select2-hidden-accessible')) {
                selectAdd.select2({
                  placeholder: 'Buscar o seleccionar material',
                  allowClear: true,
                  dropdownParent: $('#modalAgregarProducto')
                });
              } else {
                selectAdd.trigger('change');
              }
              
              if (!selectEdit.hasClass('select2-hidden-accessible')) {
                selectEdit.select2({
                  placeholder: 'Buscar o seleccionar material',
                  allowClear: true,
                  dropdownParent: $('#modalEditarProducto')
                });
              } else {
                selectEdit.trigger('change');
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
        
        var productosList = [];

        cargarMateriales();
        cargarUnidades();
        
        // Inicializar Select2 cuando se abre el modal de agregar
        $('#modalAgregarProducto').on('shown.bs.modal', function() {
          // Asegurar que Select2 esté inicializado para Material
          if (!$('#material_id').hasClass('select2-hidden-accessible')) {
            $('#material_id').select2({
              placeholder: 'Buscar o seleccionar material',
              allowClear: true,
              dropdownParent: $('#modalAgregarProducto')
            });
          }
        });
        
        // Limpiar Select2 y validación cuando se cierra el modal de agregar
        $('#modalAgregarProducto').on('hidden.bs.modal', function() {
          $('#material_id').val(null).trigger('change');
          $('#nombre').removeClass('is-valid is-invalid');
          $('#nombre').next('.invalid-feedback').remove();
        });
        
        // Inicializar Select2 cuando se abre el modal de editar
        $('#modalEditarProducto').on('shown.bs.modal', function() {
          // Asegurar que Select2 esté inicializado para Material (aunque esté deshabilitado)
          if (!$('#edit_material_id').hasClass('select2-hidden-accessible')) {
            $('#edit_material_id').select2({
              placeholder: 'Buscar o seleccionar material',
              allowClear: true,
              dropdownParent: $('#modalEditarProducto'),
              disabled: true // Select2 también debe estar deshabilitado
            });
          } else {
            // Si ya está inicializado, asegurar que esté deshabilitado
            $('#edit_material_id').prop('disabled', true);
          }
        });

        function cargarProductos() {
          $.ajax({
            url: 'api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                productosList = response.data; // Guardar lista para validación
                table.clear();
                response.data.forEach(function(producto) {
                  var badgeEstado = producto.estado === 'activo' 
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';
                  var precioVenta = producto.precio_venta ? '$' + parseFloat(producto.precio_venta).toFixed(2) : '-';
                  var precioCompra = producto.precio_compra ? '$' + parseFloat(producto.precio_compra).toFixed(2) : '-';
                  var unidad = producto.unidad_simbolo || producto.unidad_nombre || '-';
                  
                  table.row.add([
                    '<strong>' + producto.nombre + '</strong>',
                    producto.material_nombre || '-',
                    producto.categoria_nombre || '-',
                    unidad,
                    precioVenta,
                    precioCompra,
                    badgeEstado,
                    '<button class="btn btn-link btn-primary btn-sm" onclick="editarProducto(' + producto.id + ')"><i class="fa fa-edit"></i></button> ' +
                    '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarProducto(' + producto.id + ')"><i class="fa fa-times"></i></button>'
                  ]);
                });
                table.draw();
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar los productos", "error");
            }
          });
        }

        // Validar código en tiempo real
        $('#nombre').on('blur', function() {
          var nombre = $(this).val().trim();
          if (nombre.length > 0) {
            var codigoExiste = productosList.some(function(prod) {
              return prod.estado === 'activo' && prod.nombre.toLowerCase().trim() === nombre.toLowerCase().trim();
            });
            
            if (codigoExiste) {
              $(this).addClass('is-invalid');
              $(this).removeClass('is-valid');
              var feedback = $(this).next('.invalid-feedback');
              if (feedback.length === 0) {
                $(this).after('<div class="invalid-feedback">Ya existe un producto activo con este código</div>');
              }
            } else {
              $(this).removeClass('is-invalid');
              $(this).addClass('is-valid');
              $(this).next('.invalid-feedback').remove();
            }
          }
        });

        $('#btnGuardarProducto').click(function() {
          var form = $('#formAgregarProducto')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var nombre = $('#nombre').val().trim();
          
          // Validar que el código no exista
          var codigoExiste = productosList.some(function(prod) {
            return prod.estado === 'activo' && prod.nombre.toLowerCase().trim() === nombre.toLowerCase().trim();
          });
          
          if (codigoExiste) {
            swal("Error", "Ya existe un producto activo con el código \"" + nombre + "\"", "error");
            $('#nombre').focus();
            return;
          }
          
          var formData = {
            nombre: nombre,
            material_id: $('#material_id').val(),
            unidad_id: $('#unidad_id').val(),
            descripcion: $('#descripcion').val(),
            precio_venta: $('#precio_venta').val() || 0,
            precio_compra: $('#precio_compra').val() || 0,
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
                $('#modalAgregarProducto').modal('hide');
                $('#formAgregarProducto')[0].reset();
                cargarProductos();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar el producto';
              swal("Error", error, "error");
            }
          });
        });

        $('#btnActualizarProducto').click(function() {
          var form = $('#formEditarProducto')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          // Obtener valores de campos deshabilitados antes de enviar
          var formData = {
            id: $('#edit_id').val(),
            nombre: $('#edit_nombre').val(), // Campo deshabilitado pero necesario
            material_id: $('#edit_material_id').val(), // Campo deshabilitado pero necesario
            unidad_id: $('#edit_unidad_id').val(), // Campo deshabilitado pero necesario
            descripcion: $('#edit_descripcion').val(),
            precio_venta: $('#edit_precio_venta').val() || 0,
            precio_compra: $('#edit_precio_compra').val() || 0,
            estado: $('#edit_estado').val() || 'activo',
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
                $('#modalEditarProducto').modal('hide');
                cargarProductos();
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al actualizar el producto';
              swal("Error", error, "error");
            }
          });
        });

        window.editarProducto = function(id) {
          // Recargar dropdowns antes de abrir el modal para tener datos actualizados
          cargarMateriales();
          cargarUnidades();
          
          $.ajax({
            url: 'api.php?action=obtener&id=' + id,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var prod = response.data;
                
                // Esperar a que los dropdowns se carguen antes de establecer valores
                setTimeout(function() {
                  $('#edit_id').val(prod.id);
                  $('#edit_nombre').val(prod.nombre);
                  $('#edit_material_id').val(prod.material_id).trigger('change'); // Actualizar Select2
                  $('#edit_unidad_id').val(prod.unidad_id);
                  $('#edit_descripcion').val(prod.descripcion || '');
                  
                  // Deshabilitar campos que no se pueden editar (ya están deshabilitados en HTML)
                  // Los valores se mantienen para enviarlos en el formulario
                  
                  // Cargar precios
                  var precioVenta = 0;
                  var precioCompra = 0;
                  if (prod.precios) {
                    prod.precios.forEach(function(precio) {
                      if (precio.tipo_precio === 'venta' && precio.estado === 'activo') {
                        precioVenta = precio.precio_unitario;
                      }
                      if (precio.tipo_precio === 'compra' && precio.estado === 'activo') {
                        precioCompra = precio.precio_unitario;
                      }
                    });
                  }
                  $('#edit_precio_venta').val(precioVenta);
                  $('#edit_precio_compra').val(precioCompra);
                  
                  $('#modalEditarProducto').modal('show');
                }, 200); // Pequeño delay para asegurar que los dropdowns se cargaron
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

