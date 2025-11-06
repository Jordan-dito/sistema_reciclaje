<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Gestión de Roles - Sistema de Reciclaje</title>
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
              <li class="nav-item active">
                <a href="index.php">
                  <i class="fas fa-user-shield"></i>
                  <p>Roles</p>
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
                <h3 class="fw-bold mb-3">Gestión de Roles</h3>
                <h6 class="op-7 mb-2">Administra los roles y permisos del sistema</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalAgregarRol">
                  <i class="fa fa-plus"></i> Nuevo Rol
                </button>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Lista de Roles</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="rolesTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td><strong>Administrador</strong></td>
                            <td>Administrador del sistema con acceso completo</td>
                            <td><span class="badge badge-success">Activo</span></td>
                            <td>2024-01-15</td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarRol">
                                <i class="fa fa-edit"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td><strong>Gerente</strong></td>
                            <td>Gerente con permisos de gestión y reportes</td>
                            <td><span class="badge badge-success">Activo</span></td>
                            <td>2024-01-15</td>
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
                            <td><strong>usuario</strong></td>
                            <td>Usuario normal del sistema con acceso limitado</td>
                            <td><span class="badge badge-success">Activo</span></td>
                            <td>2024-01-15</td>
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

    <!-- Modal Agregar Rol -->
    <div class="modal fade" id="modalAgregarRol" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Rol</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formAgregarRol">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Nombre del Rol</label>
                    <input type="text" class="form-control" placeholder="Ej: Supervisor" required>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Descripción</label>
                    <textarea class="form-control" rows="3" placeholder="Descripción del rol"></textarea>
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
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary">Guardar Rol</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Editar Rol -->
    <div class="modal fade" id="modalEditarRol" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Rol</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formEditarRol">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Nombre del Rol</label>
                    <input type="text" class="form-control" value="Administrador" required>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Descripción</label>
                    <textarea class="form-control" rows="3">Administrador del sistema con acceso completo</textarea>
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
            <button type="button" class="btn btn-primary">Actualizar Rol</button>
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
        $('#rolesTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          }
        });
      });
    </script>
  </body>
</html>

