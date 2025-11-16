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
        <div class="sidebar-logo">
          <div class="logo-header" data-background-color="dark">
            <a href="../Dashboard.php" class="logo">
              <img src="../assets/img/logo.jpg" alt="HNOSYÁNEZ S.A." class="navbar-brand" height="50" style="object-fit: contain; border-radius: 8px;" />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
            </div>
          </div>
        </div>
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
          <div class="main-header-logo">
            <div class="logo-header" data-background-color="dark">
              <a href="../Dashboard.php" class="logo">
                <img src="../assets/img/logo.jpg" alt="HNOSYÁNEZ S.A." class="navbar-brand" height="50" style="object-fit: contain; border-radius: 8px;" />
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
                            <th>ID</th>
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

        <footer class="footer">
          <div class="container-fluid d-flex justify-content-between">
            <div class="copyright">2024, Sistema de Gestión de Reciclaje</div>
          </div>
        </footer>
      </div>
    </div>

    <!-- Modal Agregar Producto -->
    <div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formAgregarProducto">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Nombre <span class="text-danger">*</span></label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Botellas PET" required>
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
                    <label>Nombre <span class="text-danger">*</span></label>
                    <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Material <span class="text-danger">*</span></label>
                    <select id="edit_material_id" name="material_id" class="form-control" required>
                      <option value="">Seleccione un material</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Unidad <span class="text-danger">*</span></label>
                    <select id="edit_unidad_id" name="unidad_id" class="form-control" required>
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
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Estado</label>
                    <select id="edit_estado" name="estado" class="form-control">
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

        cargarMateriales();
        cargarUnidades();

        function cargarProductos() {
          $.ajax({
            url: 'api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                table.clear();
                response.data.forEach(function(producto) {
                  var badgeEstado = producto.estado === 'activo' 
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';
                  var precioVenta = producto.precio_venta ? '$' + parseFloat(producto.precio_venta).toFixed(2) : '-';
                  var precioCompra = producto.precio_compra ? '$' + parseFloat(producto.precio_compra).toFixed(2) : '-';
                  var unidad = producto.unidad_simbolo || producto.unidad_nombre || '-';
                  
                  table.row.add([
                    producto.id,
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

        $('#btnGuardarProducto').click(function() {
          var form = $('#formAgregarProducto')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var formData = {
            nombre: $('#nombre').val(),
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
          
          var formData = {
            id: $('#edit_id').val(),
            nombre: $('#edit_nombre').val(),
            material_id: $('#edit_material_id').val(),
            unidad_id: $('#edit_unidad_id').val(),
            descripcion: $('#edit_descripcion').val(),
            precio_venta: $('#edit_precio_venta').val() || 0,
            precio_compra: $('#edit_precio_compra').val() || 0,
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
          $.ajax({
            url: 'api.php?action=obtener&id=' + id,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var prod = response.data;
                $('#edit_id').val(prod.id);
                $('#edit_nombre').val(prod.nombre);
                $('#edit_material_id').val(prod.material_id);
                $('#edit_unidad_id').val(prod.unidad_id);
                $('#edit_descripcion').val(prod.descripcion || '');
                $('#edit_estado').val(prod.estado);
                
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

