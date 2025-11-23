<?php
/**
 * Gestión de Inventarios
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
    <title>Gestión de Inventarios - Sistema de Reciclaje</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

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
              $currentRoute = 'inventarios';
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
                <h3 class="fw-bold mb-3">Gestión de Inventarios</h3>
                <h6 class="op-7 mb-2">Control de inventario por producto y sucursal</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalAgregarInventario">
                  <i class="fa fa-plus"></i> Nuevo Registro
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Lista de Inventarios</div>
                      <div class="card-tools">
                        <select class="form-control form-control-sm" id="filtroSucursal" style="width: 200px; display: inline-block;">
                          <option value="">Todas las Sucursales</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="inventariosTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>Sucursal</th>
                            <th>Producto</th>
                            <th>Material</th>
                            <th>Categoría</th>
                            <th>Peso</th>
                            <th>Unidad</th>
                            <th>Precio Venta</th>
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

    <!-- Modal Agregar Inventario -->
    <div class="modal fade" id="modalAgregarInventario" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Registro de Inventario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formAgregarInventario">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Sucursales * <small class="text-muted">(Puede seleccionar múltiples)</small></label>
                    <div id="sucursalesContainer" class="border rounded p-3" style="max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                      <div class="text-center text-muted">
                        <i class="fa fa-spinner fa-spin"></i> Cargando sucursales...
                      </div>
                    </div>
                    <small class="form-text text-muted">
                      <i class="fa fa-info-circle"></i> Seleccione una o más sucursales donde desea crear el inventario.
                    </small>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Producto *</label>
                    <button type="button" class="btn btn-primary btn-block mb-2" data-bs-toggle="modal" data-bs-target="#modalBuscarProducto">
                      <i class="fa fa-search"></i> Buscar y Seleccionar Producto
                    </button>
                    <div id="productosSeleccionados" class="border rounded p-3" style="display: none; max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Productos seleccionados: <span id="contadorProductos">0</span></strong>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="limpiarProductos()">
                          <i class="fa fa-times"></i> Limpiar todo
                        </button>
                      </div>
                      <div id="listaProductosSeleccionados"></div>
                    </div>
                    <input type="hidden" id="productos_ids" name="productos_ids">
                    <small class="form-text text-muted"><i class="fa fa-info-circle"></i> Haga clic en el botón para buscar y seleccionar uno o más productos.</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Stock Mínimo</label>
                    <input type="number" step="0.01" id="stock_minimo" name="stock_minimo" class="form-control" placeholder="0.00">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Stock Máximo</label>
                    <input type="number" step="0.01" id="stock_maximo" name="stock_maximo" class="form-control" placeholder="0.00">
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarInventario">Guardar Inventario</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Buscar Producto -->
    <div class="modal fade" id="modalBuscarProducto" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Buscar y Seleccionar Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Filtros de búsqueda -->
            <div class="row mb-3">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Buscar por nombre</label>
                  <input type="text" id="filtroNombre" class="form-control" placeholder="Nombre del producto...">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Filtrar por material</label>
                  <select id="filtroMaterial" class="form-control">
                    <option value="">Todos los materiales</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Filtrar por categoría</label>
                  <select id="filtroCategoria" class="form-control">
                    <option value="">Todas las categorías</option>
                  </select>
                </div>
              </div>
            </div>
            
            <!-- Tabla de productos -->
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
              <table class="table table-hover table-striped" id="tablaProductos">
                <thead class="thead-dark" style="position: sticky; top: 0; background: white; z-index: 10;">
                  <tr>
                    <th style="width: 50px;">
                      <input type="checkbox" id="seleccionarTodos" title="Seleccionar todos">
                    </th>
                    <th>Nombre</th>
                    <th>Material</th>
                    <th>Categoría</th>
                    <th>Unidad</th>
                  </tr>
                </thead>
                <tbody id="tbodyProductos">
                  <tr>
                    <td colspan="5" class="text-center">
                      <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="sinResultados" class="alert alert-info mt-3" style="display: none;">
              <i class="fa fa-info-circle"></i> No se encontraron productos con los filtros seleccionados.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnConfirmarProductos">
              <i class="fa fa-check"></i> Confirmar Selección (<span id="contadorSeleccionados">0</span>)
            </button>
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
    <script>
      $(document).ready(function() {
        var table = $('#inventariosTable').DataTable({
          "language": { "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
          "order": [[0, "asc"]]
        });
        
        function cargarSucursales() {
          $.ajax({
            url: '../sucursales/api.php?action=activas',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var filtro = $('#filtroSucursal');
                var container = $('#sucursalesContainer');
                
                filtro.empty().append('<option value="">Todas las Sucursales</option>');
                container.empty();
                
                response.data.forEach(function(sucursal) {
                  filtro.append('<option value="' + sucursal.id + '">' + sucursal.nombre + '</option>');
                  
                  var checkbox = $('<div class="form-check mb-2">')
                    .append($('<input>')
                      .attr('type', 'checkbox')
                      .attr('class', 'form-check-input sucursal-checkbox')
                      .attr('id', 'sucursal_' + sucursal.id)
                      .attr('value', sucursal.id)
                      .on('change', function() {
                        actualizarSucursalesSeleccionadas();
                      })
                    )
                    .append($('<label>')
                      .attr('class', 'form-check-label')
                      .attr('for', 'sucursal_' + sucursal.id)
                      .text(sucursal.nombre)
                    );
                  
                  container.append(checkbox);
                });
              }
            }
          });
        }
        
        function actualizarSucursalesSeleccionadas() {
          sucursalesSeleccionadas = [];
          $('.sucursal-checkbox:checked').each(function() {
            sucursalesSeleccionadas.push($(this).val());
          });
        }
        
        var todosLosProductos = [];
        var productosSeleccionados = []; // Array para múltiples productos
        var sucursalesSeleccionadas = []; // Array para múltiples sucursales
        
        function cargarProductos() {
          $.ajax({
            url: 'api.php?action=productos',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                todosLosProductos = response.data;
                // Cargar materiales únicos para el filtro
                var materiales = [...new Set(response.data.map(p => p.material_nombre).filter(Boolean))];
                var selectMaterial = $('#filtroMaterial');
                selectMaterial.empty().append('<option value="">Todos los materiales</option>');
                materiales.sort().forEach(function(material) {
                  selectMaterial.append('<option value="' + material + '">' + material + '</option>');
                });
                
                // Cargar categorías únicas para el filtro
                var categorias = [...new Set(response.data.map(p => p.categoria_nombre).filter(Boolean))];
                var selectCategoria = $('#filtroCategoria');
                selectCategoria.empty().append('<option value="">Todas las categorías</option>');
                categorias.sort().forEach(function(categoria) {
                  selectCategoria.append('<option value="' + categoria + '">' + categoria + '</option>');
                });
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar los productos", "error");
            }
          });
        }
        
        // Cargar productos en el modal cuando se abre
        $('#modalBuscarProducto').on('show.bs.modal', function() {
          // Limpiar filtros
          $('#filtroNombre').val('');
          $('#filtroMaterial').val('');
          $('#filtroCategoria').val('');
          cargarProductosEnModal();
        });
        
        // Función para cargar y mostrar productos en el modal
        function cargarProductosEnModal() {
          var tbody = $('#tbodyProductos');
          tbody.html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></td></tr>');
          
          if (todosLosProductos.length === 0) {
            cargarProductos();
            setTimeout(cargarProductosEnModal, 500);
            return;
          }
          
          filtrarYMostrarProductos();
        }
        
        // Función para filtrar y mostrar productos
        function filtrarYMostrarProductos() {
          var filtroNombre = $('#filtroNombre').val().toLowerCase();
          var filtroMaterial = $('#filtroMaterial').val();
          var filtroCategoria = $('#filtroCategoria').val();
          var tbody = $('#tbodyProductos');
          var sinResultados = $('#sinResultados');
          
          var productosFiltrados = todosLosProductos.filter(function(producto) {
            var coincideNombre = !filtroNombre || producto.nombre.toLowerCase().includes(filtroNombre);
            var coincideMaterial = !filtroMaterial || producto.material_nombre === filtroMaterial;
            var coincideCategoria = !filtroCategoria || producto.categoria_nombre === filtroCategoria;
            
            return coincideNombre && coincideMaterial && coincideCategoria;
          });
          
          tbody.empty();
          
          if (productosFiltrados.length === 0) {
            sinResultados.show();
            return;
          }
          
          sinResultados.hide();
          
          productosFiltrados.forEach(function(producto) {
            var categoria = producto.categoria_nombre || '-';
            var nombreEscapado = $('<div>').text(producto.nombre).html();
            var productoId = producto.id;
            var estaSeleccionado = productosSeleccionados.some(p => p.id === productoId);
            
            var fila = $('<tr>');
            var checkboxCell = $('<td>');
            var checkbox = $('<input>')
              .attr('type', 'checkbox')
              .attr('class', 'producto-checkbox')
              .attr('data-producto-id', productoId)
              .prop('checked', estaSeleccionado)
              .on('change', function() {
                actualizarContadorSeleccionados();
              });
            checkboxCell.append(checkbox);
            
            fila.append(checkboxCell);
            fila.append($('<td>').html('<strong>' + nombreEscapado + '</strong>'));
            fila.append($('<td>').text(producto.material_nombre));
            fila.append($('<td>').text(categoria));
            fila.append($('<td>').text(producto.unidad));
            
            tbody.append(fila);
          });
          
          actualizarContadorSeleccionados();
        }
        
        // Seleccionar/deseleccionar todos
        $(document).on('change', '#seleccionarTodos', function() {
          var isChecked = $(this).prop('checked');
          $('.producto-checkbox').prop('checked', isChecked);
          actualizarContadorSeleccionados();
        });
        
        function actualizarContadorSeleccionados() {
          var seleccionados = $('.producto-checkbox:checked').length;
          $('#contadorSeleccionados').text(seleccionados);
          
          // Actualizar estado del checkbox "seleccionar todos"
          var total = $('.producto-checkbox').length;
          $('#seleccionarTodos').prop('checked', total > 0 && seleccionados === total);
        }
        
        // Confirmar selección de productos
        $('#btnConfirmarProductos').click(function() {
          productosSeleccionados = [];
          $('.producto-checkbox:checked').each(function() {
            var productoId = $(this).data('producto-id');
            var producto = todosLosProductos.find(p => p.id === productoId);
            if (producto) {
              productosSeleccionados.push(producto);
            }
          });
          
          if (productosSeleccionados.length === 0) {
            swal("Advertencia", "Debe seleccionar al menos un producto", "warning");
            return;
          }
          
          actualizarListaProductosSeleccionados();
          
          // Cerrar el modal
          var modal = bootstrap.Modal.getInstance(document.getElementById('modalBuscarProducto'));
          if (modal) {
            modal.hide();
          }
          
          swal({
            title: "¡Productos seleccionados!",
            text: productosSeleccionados.length + " producto(s) seleccionado(s)",
            icon: "success",
            timer: 1500,
            buttons: false
          });
        });
        
        function actualizarListaProductosSeleccionados() {
          var lista = $('#listaProductosSeleccionados');
          var contador = $('#contadorProductos');
          var container = $('#productosSeleccionados');
          
          lista.empty();
          contador.text(productosSeleccionados.length);
          
          if (productosSeleccionados.length === 0) {
            container.slideUp();
            return;
          }
          
          productosSeleccionados.forEach(function(producto) {
            var item = $('<div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">')
              .append($('<div>')
                .append($('<strong>').text(producto.nombre))
                .append($('<small class="d-block text-muted">')
                  .text('Material: ' + (producto.material_nombre || '-') + 
                        ' | Categoría: ' + (producto.categoria_nombre || '-') + 
                        ' | Unidad: ' + (producto.unidad || '-')))
              )
              .append($('<button>')
                .attr('type', 'button')
                .addClass('btn btn-sm btn-outline-danger')
                .html('<i class="fa fa-times"></i>')
                .on('click', function() {
                  productosSeleccionados = productosSeleccionados.filter(p => p.id !== producto.id);
                  actualizarListaProductosSeleccionados();
                  // Actualizar checkboxes en el modal
                  $('.producto-checkbox[data-producto-id="' + producto.id + '"]').prop('checked', false);
                  actualizarContadorSeleccionados();
                })
              );
            lista.append(item);
          });
          
          container.slideDown();
          
          // Actualizar input hidden
          var productosIds = productosSeleccionados.map(p => p.id).join(',');
          $('#productos_ids').val(productosIds);
        }
        
        // Limpiar productos seleccionados
        window.limpiarProductos = function() {
          productosSeleccionados = [];
          $('#productos_ids').val('');
          $('#productosSeleccionados').slideUp();
          $('.producto-checkbox').prop('checked', false);
          actualizarContadorSeleccionados();
        };
        }
        
        // Aplicar filtros cuando cambian
        $('#filtroNombre, #filtroMaterial, #filtroCategoria').on('input change', function() {
          filtrarYMostrarProductos();
        });
        
        
        window.cargarInventarios = function(sucursal_id = null) {
          var url = 'api.php?action=listar';
          if (sucursal_id) {
            url += '&sucursal_id=' + sucursal_id;
          }
          
          $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                table.clear();
                response.data.forEach(function(inventario) {
                  var precioVenta = parseFloat(inventario.precio_venta) || 0;
                  
                  table.row.add([
                    inventario.sucursal_nombre,
                    '<strong>' + (inventario.producto_nombre || '-') + '</strong>',
                    inventario.material_nombre || '-',
                    inventario.categoria_nombre || '-',
                    inventario.cantidad,
                    inventario.unidad_simbolo || inventario.unidad_nombre || '-',
                    precioVenta > 0 ? '$' + precioVenta.toFixed(2) : '-',
                    '<button class="btn btn-link btn-primary btn-sm" onclick="editarInventario(' + inventario.id + ')"><i class="fa fa-edit"></i></button> ' +
                    '<button class="btn btn-link btn-danger btn-sm" onclick="eliminarInventario(' + inventario.id + ')"><i class="fa fa-times"></i></button>'
                  ]);
                });
                table.draw();
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar los inventarios", "error");
            }
          });
        };
        
        $('#filtroSucursal').change(function() {
          cargarInventarios($(this).val() || null);
        });
        
        $('#btnGuardarInventario').click(function() {
          actualizarSucursalesSeleccionadas();
          
          if (sucursalesSeleccionadas.length === 0) {
            swal("Error", "Debe seleccionar al menos una sucursal", "error");
            return;
          }
          
          if (productosSeleccionados.length === 0) {
            swal("Error", "Debe seleccionar al menos un producto", "error");
            return;
          }
          
          var stockMinimo = parseFloat($('#stock_minimo').val()) || 0;
          var stockMaximo = parseFloat($('#stock_maximo').val()) || 0;
          
          // Crear todas las combinaciones de productos x sucursales
          var combinaciones = [];
          productosSeleccionados.forEach(function(producto) {
            sucursalesSeleccionadas.forEach(function(sucursalId) {
              combinaciones.push({
                sucursal_id: sucursalId,
                producto_id: producto.id,
                stock_minimo: stockMinimo,
                stock_maximo: stockMaximo
              });
            });
          });
          
          // Mostrar confirmación
          var mensaje = 'Se crearán ' + combinaciones.length + ' registro(s) de inventario:\n\n';
          mensaje += '- ' + productosSeleccionados.length + ' producto(s)\n';
          mensaje += '- ' + sucursalesSeleccionadas.length + ' sucursal(es)\n\n';
          mensaje += '¿Desea continuar?';
          
          swal({
            title: "Confirmar creación",
            text: mensaje,
            icon: "info",
            buttons: {
              cancel: "Cancelar",
              confirm: {
                text: "Crear registros",
                value: true
              }
            }
          }).then((confirmar) => {
            if (!confirmar) return;
            
            // Deshabilitar botón mientras se procesa
            var btnGuardar = $('#btnGuardarInventario');
            btnGuardar.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
            
            // Crear registros uno por uno o en lote
            var total = combinaciones.length;
            var completados = 0;
            var errores = [];
            
            function crearSiguiente() {
              if (completados >= total) {
                // Todos completados
                btnGuardar.prop('disabled', false).html('Guardar Inventario');
                
                if (errores.length === 0) {
                  swal("¡Éxito!", "Se crearon " + total + " registro(s) de inventario exitosamente", "success");
                  $('#modalAgregarInventario').modal('hide');
                  $('#formAgregarInventario')[0].reset();
                  limpiarProductos();
                  $('.sucursal-checkbox').prop('checked', false);
                  actualizarSucursalesSeleccionadas();
                  cargarInventarios($('#filtroSucursal').val() || null);
                } else {
                  swal({
                    title: "Proceso completado con errores",
                    text: "Se crearon " + (total - errores.length) + " registro(s) exitosamente.\n" +
                          "Errores: " + errores.length,
                    icon: "warning"
                  });
                  cargarInventarios($('#filtroSucursal').val() || null);
                }
                return;
              }
              
              var combinacion = combinaciones[completados];
              var formData = {
                sucursal_id: combinacion.sucursal_id,
                producto_id: combinacion.producto_id,
                cantidad: null,
                stock_minimo: combinacion.stock_minimo,
                stock_maximo: combinacion.stock_maximo,
                estado: 'disponible',
                action: 'crear'
              };
              
              $.ajax({
                url: 'api.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                  completados++;
                  if (!response.success) {
                    errores.push(response.message || 'Error desconocido');
                  }
                  crearSiguiente();
                },
                error: function(xhr) {
                  completados++;
                  var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar';
                  errores.push(error);
                  crearSiguiente();
                }
              });
            }
            
            // Iniciar creación
            crearSiguiente();
          });
        });
        
        $('#modalAgregarInventario').on('hidden.bs.modal', function() {
          $('#formAgregarInventario')[0].reset();
          limpiarProductos();
          $('.sucursal-checkbox').prop('checked', false);
          actualizarSucursalesSeleccionadas();
        });
        
        cargarSucursales();
        cargarProductos();
        cargarInventarios();
      });
      
      function editarInventario(id) {
        swal("Próximamente", "La funcionalidad de edición estará disponible pronto", "info");
      }
      
      function eliminarInventario(id) {
        swal({
          title: "¿Está seguro?",
          text: "El inventario será desactivado",
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
                  window.cargarInventarios($('#filtroSucursal').val() || null);
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
