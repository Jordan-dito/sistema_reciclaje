<?php
/**
 * Configuración del Sistema
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
    <title>Configuración del Sistema - Sistema de Reciclaje</title>
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
</head>
<body>
    <div class="wrapper">
        <div class="sidebar" data-background-color="dark">
            <?php $basePath = '..'; include __DIR__ . '/../includes/sidebar-logo.php'; ?>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <?php $basePath = '..'; $currentRoute = 'configuracion_sistema'; include __DIR__ . '/../includes/sidebar.php'; ?>
                </div>
            </div>
        </div>

        <div class="main-panel">
            <div class="main-header">
                <?php $basePath = '..'; include __DIR__ . '/../includes/main-header-logo.php'; ?>
                <?php $basePath = '..'; include __DIR__ . '/../includes/user-header.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="pt-2 pb-4">
                        <h3 class="fw-bold mb-1">Configuración del Sistema</h3>
                        <h6 class="op-7 mb-0">Parámetros generales del sistema</h6>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-round">
                                <div class="card-header">
                                    <h4 class="card-title">Parámetros</h4>
                                </div>
                                <div class="card-body">
                                    <div id="lista-params">
                                        <div class="text-center p-4">Cargando...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        $(document).ready(function() {
            cargarParams();
        });

        function cargarParams() {
            $.get('api.php', { action: 'get' }, function(resp) {
                if (!resp.success) return;
                let html = '';
                resp.datos.forEach(function(p) {
                    html += `
                        <div class="form-group row align-items-center mb-4 pb-3" style="border-bottom: 1px solid #f0f0f0;">
                            <div class="col-md-6">
                                <label class="fw-bold mb-0">${p.descripcion}</label>
                                <small class="text-muted d-block">Clave: <code>${p.clave}</code></small>
                            </div>
                            <div class="col-md-4">
                                <input type="${p.tipo}" class="form-control" id="param_${p.clave}"
                                       value="${p.valor}"
                                       ${p.tipo === 'number' ? 'min="0" max="23"' : ''}>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary btn-sm w-100" onclick="guardar('${p.clave}')">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </div>
                    `;
                });
                $('#lista-params').html(html);
            }, 'json');
        }

        function guardar(clave) {
            let valor = $('#param_' + clave).val();
            $.post('api.php', { action: 'update', clave: clave, valor: valor }, function(resp) {
                if (resp.success) {
                    swal({ title: '¡Guardado!', text: 'Parámetro actualizado correctamente.', icon: 'success', timer: 1500, buttons: false });
                } else {
                    swal('Error', resp.message, 'error');
                }
            }, 'json');
        }
    </script>
</body>
</html>
