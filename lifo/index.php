<?php
/**
 * Reporte de Flujo LIFO de Inventario
 * Sistema de Gestión de Reciclaje
 */

// Verificar autenticación
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../index.php');
    exit;
}

$currentRoute = 'lifo';
$basePath = '..';
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Flujo FIFO - Sistema de Reciclaje</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/logo.jpg" type="image/jpeg" />

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
              $currentRoute = 'lifo';
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
                <h3 class="fw-bold mb-3">Método del Promedio Ponderado</h3>
                <h6 class="op-7 mb-2">Visualiza el flujo de entradas y salidas aplicando el método del costo promedio ponderado</h6>
              </div>
            </div>

            <!-- Filtros -->
            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Filtros de Búsqueda</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form id="formFiltros">
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Fecha Desde <span class="text-danger">*</span></label>
                            <input type="date" id="fecha_desde" name="fecha_desde" class="form-control" required>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Fecha Hasta <span class="text-danger">*</span></label>
                            <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control" required>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Sucursal</label>
                            <select id="sucursal_id" name="sucursal_id" class="form-control">
                              <option value="">Todas las Sucursales</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Material</label>
                            <input type="text" id="material" name="material" class="form-control" list="materiales_list" placeholder="Todos los materiales">
                            <datalist id="materiales_list"></datalist>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <button type="button" class="btn btn-primary" id="btnBuscar">
                            <i class="fa fa-search"></i> Buscar
                          </button>
                          <button type="button" class="btn btn-success" id="btnExportar" style="display: none;">
                            <i class="fa fa-file-pdf"></i> Exportar PDF
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <!-- Información Promedio Ponderado -->
            <div class="row mt-3">
              <!-- Sección informativa eliminada por solicitud -->
            </div>

            <!-- Resultados -->
            <div class="row mt-3" id="resultadosContainer" style="display: none;">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Resultados del Método Promedio</div>
                    </div>
                  </div>
                  <div class="card-body" id="resultadosContent">
                    <!-- Los resultados se cargarán aquí -->
                  </div>
                </div>
              </div>
            </div>

            <!-- Loading -->
            <div class="row mt-3" id="loadingContainer" style="display: none;">
              <div class="col-md-12">
                <div class="text-center py-5">
                  <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Cargando...</span>
                  </div>
                  <p class="mt-3">Cargando datos...</p>
                </div>
              </div>
            </div>

          </div>
        </div>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
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
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/setting-demo.js"></script>

    <script>
      $(document).ready(function() {
        // Establecer fechas por defecto (último mes)
        var hoy = new Date();
        var hace30Dias = new Date();
        hace30Dias.setDate(hoy.getDate() - 30);
        
        $('#fecha_hasta').val(hoy.toISOString().split('T')[0]);
        $('#fecha_desde').val(hace30Dias.toISOString().split('T')[0]);
        
        // Cargar sucursales
        cargarSucursales();
        
        // Cargar materiales
        cargarMateriales();
        
        // Evento buscar
        $('#btnBuscar').click(function() {
          buscarFlujoFIFO();
        });
        
        // Evento exportar
        $('#btnExportar').click(function() {
          exportarPDF();
        });
        
        function cargarSucursales() {
          $.ajax({
            url: '../sucursales/api.php?action=activas',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var select = $('#sucursal_id');
                select.empty().append('<option value="">Todas las Sucursales</option>');
                response.data.forEach(function(sucursal) {
                  select.append('<option value="' + sucursal.id + '">' + sucursal.nombre + '</option>');
                });
              }
            }
          });
        }
        
        function cargarMateriales() {
          $.ajax({
            url: '../materiales/api.php?action=listar',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var datalist = $('#materiales_list');
                datalist.empty();
                response.data.forEach(function(material) {
                  if (material.estado === 'activo') {
                    datalist.append('<option value="' + material.nombre + '">');
                  }
                });
              }
            }
          });
        }
        
        function buscarFlujoFIFO() {
          var form = $('#formFiltros')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var fechaDesde = $('#fecha_desde').val();
          var fechaHasta = $('#fecha_hasta').val();
          
          if (new Date(fechaDesde) > new Date(fechaHasta)) {
            swal("Error", "La fecha desde debe ser menor o igual a la fecha hasta", "error");
            return;
          }
          
          var params = {
            action: 'promedio',
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta,
            sucursal_id: $('#sucursal_id').val(),
            material: $('#material').val()
          };
          
          $('#loadingContainer').show();
          $('#resultadosContainer').hide();
          $('#btnExportar').hide();
          
          $.ajax({
            url: 'api.php',
            method: 'GET',
            data: params,
            dataType: 'json',
            success: function(response) {
              $('#loadingContainer').hide();
              
              if (response.success) {
                $('#resultadosContent').html(response.html);
                $('#resultadosContainer').fadeIn();
                $('#btnExportar').show();
              } else {
                swal("Error", response.message || "Error al cargar los datos", "error");
              }
            },
            error: function(xhr) {
              $('#loadingContainer').hide();
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al cargar los datos';
              swal("Error", error, "error");
            }
          });
        }
        
        function exportarPDF() {
          var params = new URLSearchParams({
            fecha_desde: $('#fecha_desde').val(),
            fecha_hasta: $('#fecha_hasta').val(),
            sucursal_id: $('#sucursal_id').val(),
            material: $('#material').val()
          });
          
          window.open('pdf.php?' + params.toString(), '_blank');
        }
      });
    </script>
  </body>
</html>
