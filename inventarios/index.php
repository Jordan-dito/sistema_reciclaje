<?php
/**
 * Gestión de Materiales
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
    <title>Gestión de Materiales - Sistema de Reciclaje</title>
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
                <h3 class="fw-bold mb-3">Gestión de Materiales</h3>
                <h6 class="op-7 mb-2">Control de materiales por categoría de reciclaje</h6>
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
                      <div class="card-title">Materiales por Categoría</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div id="materialesPorCategoria">
                      <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                          <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando materiales...</p>
                      </div>
                    </div>
                  </div>
                </div>
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
                          <!-- Las opciones se cargarán dinámicamente -->
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="inventariosTable" class="display table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Sucursal</th>
                            <th>Categoría</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Precio Unitario</th>
                            <th>Valor Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td>Sucursal Central</td>
                            <td><strong>PET</strong></td>
                            <td>150.50</td>
                            <td>kg</td>
                            <td>$2.50</td>
                            <td>$376.25</td>
                            <td><span class="badge badge-success">Disponible</span></td>
                            <td>
                              <button class="btn btn-link btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarInventario">
                                <i class="fa fa-edit"></i>
                              </button>
                              <button class="btn btn-link btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td>Sucursal Central</td>
                            <td><strong>hogar</strong></td>
                            <td>85.00</td>
                            <td>kg</td>
                            <td>$3.00</td>
                            <td>$255.00</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>Sucursal Norte</td>
                            <td><strong>soplado</strong></td>
                            <td>120.75</td>
                            <td>kg</td>
                            <td>$1.50</td>
                            <td>$181.13</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>Sucursal Central</td>
                            <td><strong>CARTON 0,12</strong></td>
                            <td>95.25</td>
                            <td>kg</td>
                            <td>$2.50</td>
                            <td>$238.13</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>Sucursal Sur</td>
                            <td><strong>PAPEL</strong></td>
                            <td>200.00</td>
                            <td>kg</td>
                            <td>$1.80</td>
                            <td>$360.00</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>Sucursal Central</td>
                            <td><strong>COBRE 3,5</strong></td>
                            <td>45.50</td>
                            <td>kg</td>
                            <td>$4.00</td>
                            <td>$182.00</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>7</td>
                            <td>Sucursal Norte</td>
                            <td><strong>cobre 3,2</strong></td>
                            <td>60.00</td>
                            <td>kg</td>
                            <td>$3.80</td>
                            <td>$228.00</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>8</td>
                            <td>Sucursal Central</td>
                            <td><strong>BRONCE</strong></td>
                            <td>30.25</td>
                            <td>kg</td>
                            <td>$5.50</td>
                            <td>$166.38</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>9</td>
                            <td>Sucursal Sur</td>
                            <td><strong>ALUMINIO</strong></td>
                            <td>75.50</td>
                            <td>kg</td>
                            <td>$3.20</td>
                            <td>$241.60</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>10</td>
                            <td>Sucursal Norte</td>
                            <td><strong>PERFIL</strong></td>
                            <td>40.00</td>
                            <td>kg</td>
                            <td>$2.80</td>
                            <td>$112.00</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>11</td>
                            <td>Sucursal Central</td>
                            <td><strong>duplex</strong></td>
                            <td>55.75</td>
                            <td>kg</td>
                            <td>$2.20</td>
                            <td>$122.65</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>12</td>
                            <td>Sucursal Sur</td>
                            <td><strong>BATERIA</strong></td>
                            <td>25.50</td>
                            <td>unidades</td>
                            <td>$15.00</td>
                            <td>$382.50</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>13</td>
                            <td>Sucursal Central</td>
                            <td><strong>BATE/PEQ</strong></td>
                            <td>18.00</td>
                            <td>unidades</td>
                            <td>$12.00</td>
                            <td>$216.00</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>14</td>
                            <td>Sucursal Norte</td>
                            <td><strong>FILL</strong></td>
                            <td>90.25</td>
                            <td>kg</td>
                            <td>$1.90</td>
                            <td>$171.48</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>15</td>
                            <td>Sucursal Sur</td>
                            <td><strong>PVC</strong></td>
                            <td>35.75</td>
                            <td>kg</td>
                            <td>$2.60</td>
                            <td>$92.95</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>16</td>
                            <td>Sucursal Central</td>
                            <td><strong>VIDRIO</strong></td>
                            <td>120.00</td>
                            <td>kg</td>
                            <td>$1.50</td>
                            <td>$180.00</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>17</td>
                            <td>Sucursal Norte</td>
                            <td><strong>JABAS</strong></td>
                            <td>50.00</td>
                            <td>unidades</td>
                            <td>$8.00</td>
                            <td>$400.00</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>18</td>
                            <td>Sucursal Sur</td>
                            <td><strong>REVISTA</strong></td>
                            <td>150.50</td>
                            <td>kg</td>
                            <td>$1.20</td>
                            <td>$180.60</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>19</td>
                            <td>Sucursal Central</td>
                            <td><strong>RADIADOR</strong></td>
                            <td>12.00</td>
                            <td>unidades</td>
                            <td>$25.00</td>
                            <td>$300.00</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                            <td>20</td>
                            <td>Sucursal Norte</td>
                            <td><strong>carton 0,13</strong></td>
                            <td>80.25</td>
                            <td>kg</td>
                            <td>$2.60</td>
                            <td>$208.65</td>
                            <td><span class="badge badge-success">Disponible</span></td>
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
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Sucursal *</label>
                    <select id="sucursal_id" name="sucursal_id" class="form-control" required>
                      <option value="">Seleccione una sucursal</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nombre del Producto *</label>
                    <input type="text" id="nombre_producto" name="nombre_producto" class="form-control" placeholder="Ej: Papel Reciclado" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Categoría Principal *</label>
                    <select id="categoria_principal" name="categoria_principal" class="form-control" required>
                      <option value="">Seleccione una categoría</option>
                      <!-- Las opciones se cargarán dinámicamente -->
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Material *</label>
                    <select id="material_id" name="material_id" class="form-control" required disabled>
                      <option value="">Primero seleccione una categoría</option>
                      <!-- Las opciones se cargarán dinámicamente según la categoría seleccionada -->
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Cantidad *</label>
                    <input type="number" step="0.01" id="cantidad" name="cantidad" class="form-control" placeholder="0.00" required>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Unidad *</label>
                    <select id="unidad" name="unidad" class="form-control" required>
                      <option value="kg">kg</option>
                      <option value="litros">litros</option>
                      <option value="unidades">unidades</option>
                      <option value="toneladas">toneladas</option>
                      <option value="metros">metros</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Precio Unitario *</label>
                    <input type="number" step="0.01" id="precio_unitario" name="precio_unitario" class="form-control" placeholder="0.00" required>
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
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="2" placeholder="Descripción adicional"></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Estado</label>
                    <select id="estado" name="estado" class="form-control">
                      <option value="disponible">Disponible</option>
                      <option value="agotado">Agotado</option>
                      <option value="reservado">Reservado</option>
                      <option value="inactivo">Inactivo</option>
                    </select>
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

    <!-- Modal Editar Inventario -->
    <div class="modal fade" id="modalEditarInventario" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Registro de Inventario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formEditarInventario">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Sucursal</label>
                    <select class="form-control" required>
                      <option value="1" selected>Sucursal Central</option>
                      <option value="2">Sucursal Norte</option>
                      <option value="3">Sucursal Sur</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Categoría</label>
                    <select class="form-control" required>
                      <option selected>PET</option>
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
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Cantidad</label>
                    <input type="number" step="0.01" class="form-control" value="150.50" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Unidad</label>
                    <select class="form-control" required>
                      <option value="kg" selected>kg</option>
                      <option value="litros">litros</option>
                      <option value="unidades">unidades</option>
                      <option value="toneladas">toneladas</option>
                      <option value="metros">metros</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Precio Unitario</label>
                    <input type="number" step="0.01" class="form-control" value="2.50" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Stock Mínimo</label>
                    <input type="number" step="0.01" class="form-control" value="50.00">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Stock Máximo</label>
                    <input type="number" step="0.01" class="form-control" value="500.00">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Descripción</label>
                    <textarea class="form-control" rows="2">Papel reciclado procesado</textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Estado</label>
                    <select class="form-control">
                      <option value="disponible" selected>Disponible</option>
                      <option value="agotado">Agotado</option>
                      <option value="reservado">Reservado</option>
                      <option value="inactivo">Inactivo</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary">Actualizar Inventario</button>
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
        var table = $('#inventariosTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
          },
          "order": [[0, "desc"]]
        });
        
        // Cargar sucursales para el filtro y formularios
        function cargarSucursales() {
          $.ajax({
            url: '../sucursales/api.php?action=activas',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                // Llenar filtro
                var filtro = $('#filtroSucursal');
                filtro.empty().append('<option value="">Todas las Sucursales</option>');
                response.data.forEach(function(sucursal) {
                  filtro.append('<option value="' + sucursal.id + '">' + sucursal.nombre + '</option>');
                });
                
                // Llenar selects en formularios
                $('#sucursal_id, #editar_sucursal_id').each(function() {
                  var select = $(this);
                  select.empty().append('<option value="">Seleccione una sucursal</option>');
                  response.data.forEach(function(sucursal) {
                    select.append('<option value="' + sucursal.id + '">' + sucursal.nombre + '</option>');
                  });
                });
              }
            }
          });
        }
        
        // Cargar categorías principales
        function cargarCategorias() {
          $.ajax({
            url: 'api.php?action=categorias',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                var select = $('#categoria_principal');
                select.empty().append('<option value="">Seleccione una categoría</option>');
                response.data.forEach(function(categoria) {
                  select.append('<option value="' + categoria.id + '">' + categoria.nombre + '</option>');
                });
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar las categorías", "error");
            }
          });
        }
        
        // Cargar materiales según categoría seleccionada
        function cargarMateriales(categoriaId) {
          var selectMaterial = $('#material_id');
          
          if (!categoriaId || categoriaId === '') {
            selectMaterial.empty().append('<option value="">Primero seleccione una categoría</option>');
            selectMaterial.prop('disabled', true);
            return;
          }
          
          $.ajax({
            url: 'api.php?action=materiales&categoria_id=' + categoriaId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                selectMaterial.empty();
                if (response.data.length > 0) {
                  selectMaterial.append('<option value="">Seleccione un material</option>');
                  response.data.forEach(function(material) {
                    selectMaterial.append('<option value="' + material.id + '">' + material.nombre + '</option>');
                  });
                  selectMaterial.prop('disabled', false);
                } else {
                  // Si la categoría no tiene subcategorías, usar la categoría misma como material
                  selectMaterial.append('<option value="' + categoriaId + '">' + $('#categoria_principal option:selected').text() + '</option>');
                  selectMaterial.prop('disabled', false);
                }
              }
            },
            error: function() {
              swal("Error", "No se pudieron cargar los materiales", "error");
            }
          });
        }
        
        // Evento cuando se selecciona una categoría
        $(document).on('change', '#categoria_principal', function() {
          var categoriaId = $(this).val();
          cargarMateriales(categoriaId);
        });
        
        window.cargarInventarios = cargarInventarios;
        
        // Cargar inventarios
        function cargarInventarios(sucursal_id = null) {
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
                  var valorTotal = parseFloat(inventario.cantidad) * parseFloat(inventario.precio_unitario);
                  var badgeEstado = '';
                  if (inventario.estado === 'disponible') {
                    badgeEstado = '<span class="badge badge-success">Disponible</span>';
                  } else if (inventario.estado === 'agotado') {
                    badgeEstado = '<span class="badge badge-danger">Agotado</span>';
                  } else if (inventario.estado === 'reservado') {
                    badgeEstado = '<span class="badge badge-warning">Reservado</span>';
                  } else if (inventario.estado === 'inactivo') {
                    badgeEstado = '<span class="badge badge-secondary">Inactivo</span>';
                  } else {
                    badgeEstado = inventario.estado || '';
                  }
                  
                  var categoriaTexto = inventario.categoria_padre_nombre 
                    ? inventario.categoria_padre_nombre + ' - ' + inventario.material_nombre 
                    : inventario.material_nombre;
                  
                  table.row.add([
                    inventario.id,
                    inventario.sucursal_nombre,
                    '<span class="badge badge-info">' + categoriaTexto + '</span>',
                    inventario.cantidad,
                    inventario.unidad,
                    '$' + parseFloat(inventario.precio_unitario).toFixed(2),
                    '$' + valorTotal.toFixed(2),
                    badgeEstado,
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
        }
        
        // Filtro de sucursales
        $('#filtroSucursal').change(function() {
          var sucursal_id = $(this).val();
          cargarInventarios(sucursal_id || null);
        });
        
        // Guardar nuevo inventario
        $('#btnGuardarInventario').click(function() {
          var form = $('#formAgregarInventario')[0];
          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }
          
          var materialId = $('#material_id').val();
          if (!materialId) {
            swal("Error", "Debe seleccionar un material", "error");
            return;
          }
          
          var formData = {
            sucursal_id: $('#sucursal_id').val(),
            nombre_producto: $('#nombre_producto').val(),
            material_id: materialId,
            cantidad: $('#cantidad').val(),
            unidad: $('#unidad').val(),
            precio_unitario: $('#precio_unitario').val(),
            stock_minimo: $('#stock_minimo').val() || 0,
            stock_maximo: $('#stock_maximo').val() || 0,
            descripcion: $('#descripcion').val(),
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
                $('#modalAgregarInventario').modal('hide');
                $('#formAgregarInventario')[0].reset();
                cargarInventarios($('#filtroSucursal').val() || null);
              } else {
                swal("Error", response.message, "error");
              }
            },
            error: function(xhr) {
              var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar el inventario';
              swal("Error", error, "error");
            }
          });
        });
        
        // Cargar materiales por categoría
        function cargarMaterialesPorCategoria() {
          $.ajax({
            url: 'api.php?action=categorias',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.success && response.data.length > 0) {
                var html = '<div class="row">';
                
                response.data.forEach(function(categoria) {
                  // Cargar materiales de esta categoría
                  $.ajax({
                    url: 'api.php?action=materiales&categoria_id=' + categoria.id,
                    method: 'GET',
                    dataType: 'json',
                    async: false, // Sincrónico para mantener el orden
                    success: function(materialResponse) {
                      html += '<div class="col-md-6 mb-4">';
                      html += '<div class="card card-round" style="border-left: 4px solid #177dff;">';
                      html += '<div class="card-header">';
                      html += '<div class="card-head-row">';
                      html += '<div class="card-title">';
                      if (categoria.icono) {
                        html += '<i class="' + categoria.icono + ' me-2"></i>';
                      }
                      html += '<strong>' + categoria.nombre + '</strong>';
                      html += '</div>';
                      html += '</div>';
                      html += '</div>';
                      html += '<div class="card-body">';
                      
                      if (materialResponse.success && materialResponse.data.length > 0) {
                        html += '<ul class="list-group list-group-flush">';
                        materialResponse.data.forEach(function(material) {
                          html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                          html += '<span><i class="fa fa-circle me-2" style="font-size: 8px; color: #177dff;"></i>' + material.nombre + '</span>';
                          if (material.descripcion) {
                            html += '<small class="text-muted">' + material.descripcion + '</small>';
                          }
                          html += '</li>';
                        });
                        html += '</ul>';
                      } else {
                        // Si no tiene subcategorías, mostrar la categoría misma
                        html += '<ul class="list-group list-group-flush">';
                        html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                        html += '<span><i class="fa fa-circle me-2" style="font-size: 8px; color: #177dff;"></i>' + categoria.nombre + '</span>';
                        if (categoria.descripcion) {
                          html += '<small class="text-muted">' + categoria.descripcion + '</small>';
                        }
                        html += '</li>';
                        html += '</ul>';
                      }
                      
                      html += '</div>';
                      html += '</div>';
                      html += '</div>';
                    },
                    error: function() {
                      html += '<div class="col-md-6 mb-4">';
                      html += '<div class="alert alert-warning">Error al cargar materiales de ' + categoria.nombre + '</div>';
                      html += '</div>';
                    }
                  });
                });
                
                html += '</div>';
                $('#materialesPorCategoria').html(html);
              } else {
                $('#materialesPorCategoria').html('<div class="alert alert-info">No hay categorías registradas</div>');
              }
            },
            error: function() {
              $('#materialesPorCategoria').html('<div class="alert alert-danger">Error al cargar las categorías</div>');
            }
          });
        }
        
        // Cargar datos al iniciar
        cargarSucursales();
        cargarCategorias();
        cargarMaterialesPorCategoria();
        cargarInventarios();
        
        // Limpiar formulario al cerrar modal
        $('#modalAgregarInventario').on('hidden.bs.modal', function() {
          $('#formAgregarInventario')[0].reset();
          $('#material_id').empty().append('<option value="">Primero seleccione una categoría</option>').prop('disabled', true);
        });
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
                  var sucursalSeleccionada = $('#filtroSucursal').val() || null;
                  cargarInventarios(sucursalSeleccionada);
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

