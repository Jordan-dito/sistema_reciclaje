<?php
/**
 * Gestión de Empleados - Página de ejemplo
 */
// Verificar autenticación
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: ../../index.php');
    exit;
}

$usuario = $auth->getCurrentUser();
$usuarioNombre = $usuario['nombre'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Gestión de Empleados - <?php echo htmlspecialchars($usuarioNombre); ?></title>
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
              $currentRoute = 'gestion_empleados';
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
            include __DIR__ . '/../includes/modal-cambiar-password.php';
          ?>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Gestión de Empleados</div>
                    <div class="text-muted">Usuario: <?php echo htmlspecialchars($usuarioNombre); ?></div>
                  </div>
                  <div class="card-body">
                    <p class="mb-3">Página de ejemplo para administrar empleados. Aquí puedes listar, crear y editar registros del personal.</p>

                    <div class="mb-3">
                      <button class="btn btn-primary" id="btnNuevo">Nuevo Empleado</button>
                    </div>

                    <div class="table-responsive">
                      <table class="table table-striped table-hover" id="empleadosTable">
                        <thead>
                          <tr>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Cargo</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Fila de ejemplo -->
                          <tr>
                            <td>Juan Pérez</td>
                            <td>0102030405</td>
                            <td>Operario</td>
                            <td>0991234567</td>
                            <td>
                              <button class="btn btn-sm btn-info">Ver</button>
                              <button class="btn btn-sm btn-warning">Editar</button>
                              <button class="btn btn-sm btn-danger">Eliminar</button>
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
      </div>
    </div>

    <script src="../assets/js/core/jquery.3.2.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script>
      document.getElementById('btnNuevo').addEventListener('click', function(){
        alert('Aquí abrirías el formulario para crear un nuevo empleado (demo).');
      });
    </script>
  </body>
</html>
