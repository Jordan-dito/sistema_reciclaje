<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Registro de Ventas - Sistema de Reciclaje</title>
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
            <ul class="nav nav-secondary">
              <li class="nav-item">
                <a href="../Dashboard.php">
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Administración</h4>
              </li>
              <li class="nav-item">
                <a href="../usuarios/index.php">
                  <i class="fas fa-users"></i>
                  <p>Usuarios</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../sucursales/index.php">
                  <i class="fas fa-building"></i>
                  <p>Sucursales</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../inventarios/index.php">
                  <i class="fas fa-boxes"></i>
                  <p>Inventarios</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../clientes/index.php">
                  <i class="fas fa-user-tie"></i>
                  <p>Clientes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../proveedores/index.php">
                  <i class="fas fa-truck"></i>
                  <p>Proveedores</p>
                </a>
              </li>
              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Operaciones</h4>
              </li>
              <li class="nav-item">
                <a href="../compras/index.php">
                  <i class="fas fa-shopping-cart"></i>
                  <p>Compras</p>
                </a>
              </li>
              <li class="nav-item active">
                <a href="index.php">
                  <i class="fas fa-cash-register"></i>
                  <p>Ventas</p>
                </a>
              </li>
            </ul>
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
                <h3 class="fw-bold mb-3">Registro de Ventas</h3>
                <h6 class="op-7 mb-2">Registra ventas de materiales reciclables - Actualiza inventario automáticamente</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalNuevaVenta">
                  <i class="fa fa-plus"></i> Nueva Venta
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Historial de Ventas</div>
                      <div class="card-tools">
                        <input type="date" class="form-control form-control-sm" id="filtroFecha" style="width: 200px; display: inline-block;">
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="ventasTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Sucursal</th>
                            <th>Categoría</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                            <th>Cliente</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td>2024-11-05</td>
                            <td>Sucursal Central</td>
                            <td><strong>PET</strong></td>
                            <td>50.00</td>
                            <td>kg</td>
                            <td>$3.00</td>
                            <td><strong>$150.00</strong></td>
                            <td>Industrias ABC</td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalVerVenta">
                                <i class="fa fa-eye"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td>2024-11-04</td>
                            <td>Sucursal Norte</td>
                            <td><strong>ALUMINIO</strong></td>
                            <td>30.00</td>
                            <td>kg</td>
                            <td>$3.50</td>
                            <td><strong>$105.00</strong></td>
                            <td>Metalúrgica XYZ</td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm">
                                <i class="fa fa-eye"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>3</td>
                            <td>2024-11-03</td>
                            <td>Sucursal Sur</td>
                            <td><strong>PAPEL</strong></td>
                            <td>100.00</td>
                            <td>kg</td>
                            <td>$2.00</td>
                            <td><strong>$200.00</strong></td>
                            <td>Papelera del Sur</td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm">
                                <i class="fa fa-eye"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>4</td>
                            <td>2024-11-02</td>
                            <td>Sucursal Central</td>
                            <td><strong>COBRE 3,5</strong></td>
                            <td>20.00</td>
                            <td>kg</td>
                            <td>$4.50</td>
                            <td><strong>$90.00</strong></td>
                            <td>Cobre y Metales S.A.</td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm">
                                <i class="fa fa-eye"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>5</td>
                            <td>2024-11-01</td>
                            <td>Sucursal Norte</td>
                            <td><strong>VIDRIO</strong></td>
                            <td>80.00</td>
                            <td>kg</td>
                            <td>$1.80</td>
                            <td><strong>$144.00</strong></td>
                            <td>Vidrios Premium</td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm">
                                <i class="fa fa-eye"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>6</td>
                            <td>2024-10-31</td>
                            <td>Sucursal Sur</td>
                            <td><strong>CARTON 0,12</strong></td>
                            <td>60.00</td>
                            <td>kg</td>
                            <td>$2.80</td>
                            <td><strong>$168.00</strong></td>
                            <td>Cartones del Este</td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm">
                                <i class="fa fa-eye"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
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

    <!-- Modal Nueva Venta -->
    <div class="modal fade" id="modalNuevaVenta" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nueva Venta de Material Reciclable</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formNuevaVenta">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Fecha de Venta <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Sucursal <span class="text-danger">*</span></label>
                    <select class="form-control" required>
                      <option value="">Seleccione una sucursal</option>
                      <option value="1">Sucursal Central</option>
                      <option value="2">Sucursal Norte</option>
                      <option value="3">Sucursal Sur</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Categoría de Material <span class="text-danger">*</span></label>
                    <select class="form-control" id="categoriaMaterial" required>
                      <option value="">Seleccione una categoría</option>
                      <option>PET</option>
                      <option>hogar</option>
                      <option>soplado</option>
                      <option>CARTON 0,12</option>
                      <option>PAPEL</option>
                      <option>COBRE 3,5</option>
                      <option>cobre 3,2</option>
                      <option>BRONCE</option>
                      <option>ALUMINIO</option>
                      <option>PERFIL</option>
                      <option>duplex</option>
                      <option>BATERIA</option>
                      <option>BATE/PEQ</option>
                      <option>FILL</option>
                      <option>PVC</option>
                      <option>VIDRIO</option>
                      <option>JABAS</option>
                      <option>REVISTA</option>
                      <option>RADIADOR</option>
                      <option>carton 0,13</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Cliente <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="Nombre del cliente" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Cantidad <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="cantidadVenta" placeholder="0.00" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Unidad <span class="text-danger">*</span></label>
                    <select class="form-control" required>
                      <option value="kg">kg</option>
                      <option value="litros">litros</option>
                      <option value="unidades">unidades</option>
                      <option value="toneladas">toneladas</option>
                      <option value="metros">metros</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Precio Unitario <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="precioUnitario" placeholder="0.00" required>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Total:</strong> <span id="totalVenta">$0.00</span>
                    <br>
                    <small>El inventario se actualizará automáticamente restando las existencias vendidas</small>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Observaciones</label>
                    <textarea class="form-control" rows="2" placeholder="Notas adicionales sobre la venta"></textarea>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary">Registrar Venta</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ver Venta -->
    <div class="modal fade" id="modalVerVenta" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detalle de Venta #1</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <p><strong>Fecha:</strong> 2024-11-05</p>
                <p><strong>Sucursal:</strong> Sucursal Central</p>
                <p><strong>Categoría:</strong> PET</p>
                <p><strong>Cantidad:</strong> 50.00 kg</p>
              </div>
              <div class="col-md-6">
                <p><strong>Precio Unitario:</strong> $3.00</p>
                <p><strong>Total:</strong> $150.00</p>
                <p><strong>Cliente:</strong> Industrias ABC</p>
                <p><strong>Estado:</strong> <span class="badge badge-success">Completada</span></p>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-12">
                <div class="alert alert-success">
                  <i class="fas fa-check-circle"></i> 
                  <strong>Inventario actualizado:</strong> Se restaron 50.00 kg de PET del inventario
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
        $('#ventasTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          },
          "order": [[0, "desc"]]
        });

        // Calcular total automáticamente
        $('#cantidadVenta, #precioUnitario').on('input', function() {
          var cantidad = parseFloat($('#cantidadVenta').val()) || 0;
          var precio = parseFloat($('#precioUnitario').val()) || 0;
          var total = cantidad * precio;
          $('#totalVenta').text('$' + total.toFixed(2));
        });
      });
    </script>
  </body>
</html>

