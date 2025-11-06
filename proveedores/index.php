<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Gestión de Proveedores - Sistema de Reciclaje</title>
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
              <li class="nav-item active">
                <a href="index.php">
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
              <li class="nav-item">
                <a href="../ventas/index.php">
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
                <h3 class="fw-bold mb-3">Gestión de Proveedores</h3>
                <h6 class="op-7 mb-2">Administra los proveedores de materiales reciclables</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalAgregarProveedor">
                  <i class="fa fa-plus"></i> Nuevo Proveedor
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Lista de Proveedores</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="proveedoresTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Razón Social</th>
                            <th>RUC</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td><strong>Reciclajes S.A.</strong></td>
                            <td>0998765432001</td>
                            <td>compras@reciclajessa.com</td>
                            <td>02-2345678</td>
                            <td>Av. Principal 123, Quito</td>
                            <td><span class="badge badge-success">Activo</span></td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarProveedor">
                                <i class="fa fa-edit"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td><strong>Metales del Norte</strong></td>
                            <td>0987654321001</td>
                            <td>ventas@metalesnorte.com</td>
                            <td>04-3456789</td>
                            <td>Calle 10 de Agosto 456, Guayaquil</td>
                            <td><span class="badge badge-success">Activo</span></td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm">
                                <i class="fa fa-edit"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>3</td>
                            <td><strong>Papelería Central</strong></td>
                            <td>0976543210001</td>
                            <td>info@papeleriacentral.com</td>
                            <td>02-5678901</td>
                            <td>Av. 9 de Octubre 789, Quito</td>
                            <td><span class="badge badge-success">Activo</span></td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm">
                                <i class="fa fa-edit"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>4</td>
                            <td><strong>Metales Premium</strong></td>
                            <td>0965432109001</td>
                            <td>compras@metalespremium.com</td>
                            <td>04-4567890</td>
                            <td>Av. Colón 321, Guayaquil</td>
                            <td><span class="badge badge-success">Activo</span></td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm">
                                <i class="fa fa-edit"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>5</td>
                            <td><strong>Vidrios y Más</strong></td>
                            <td>0954321098001</td>
                            <td>ventas@vidriosymas.com</td>
                            <td>02-6789012</td>
                            <td>Calle Bolívar 654, Quito</td>
                            <td><span class="badge badge-success">Activo</span></td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm">
                                <i class="fa fa-edit"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>6</td>
                            <td><strong>Reciclaje Ecológico S.A.</strong></td>
                            <td>0943210987001</td>
                            <td>contacto@reciclajeecologico.com</td>
                            <td>04-7890123</td>
                            <td>Av. Amazonas 987, Guayaquil</td>
                            <td><span class="badge badge-success">Activo</span></td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm">
                                <i class="fa fa-edit"></i>
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

    <!-- Modal Agregar Proveedor -->
    <div class="modal fade" id="modalAgregarProveedor" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Proveedor</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formAgregarProveedor">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Razón Social <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="Ej: Reciclajes S.A." required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>RUC Ecuatoriano <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="rucProveedor" placeholder="0998765432001" 
                           pattern="[0-9]{13}" maxlength="13" required>
                    <small class="form-text text-muted">RUC debe tener 13 dígitos (formato: 0998765432001)</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nombre Comercial</label>
                    <input type="text" class="form-control" placeholder="Nombre comercial (opcional)">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" placeholder="proveedor@email.com">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" class="form-control" placeholder="02-2345678">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Dirección</label>
                    <textarea class="form-control" rows="2" placeholder="Dirección completa"></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Ciudad</label>
                    <input type="text" class="form-control" placeholder="Ej: Quito">
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
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Observaciones</label>
                    <textarea class="form-control" rows="2" placeholder="Información adicional sobre el proveedor"></textarea>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary">Guardar Proveedor</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Editar Proveedor -->
    <div class="modal fade" id="modalEditarProveedor" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Proveedor</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formEditarProveedor">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Razón Social <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="Reciclajes S.A." required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>RUC Ecuatoriano <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="rucProveedorEdit" value="0998765432001" 
                           pattern="[0-9]{13}" maxlength="13" required>
                    <small class="form-text text-muted">RUC debe tener 13 dígitos (formato: 0998765432001)</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nombre Comercial</label>
                    <input type="text" class="form-control" value="Reciclajes">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" value="compras@reciclajessa.com">
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
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Observaciones</label>
                    <textarea class="form-control" rows="2">Proveedor principal de materiales PET</textarea>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary">Actualizar Proveedor</button>
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
        $('#proveedoresTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          }
        });

        // Validar RUC - solo números y máximo 13 dígitos
        $('#rucProveedor, #rucProveedorEdit').on('input', function() {
          this.value = this.value.replace(/[^0-9]/g, '');
          if (this.value.length > 13) {
            this.value = this.value.slice(0, 13);
          }
        });
      });
    </script>
  </body>
</html>

