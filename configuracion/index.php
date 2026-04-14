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
    <style>
        .config-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .config-card .card-header {
            background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
            border: none;
            padding: 20px 28px;
        }
        .config-card .card-header h4 {
            color: #fff;
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.3px;
        }
        .config-card .card-header p {
            color: rgba(255,255,255,0.75);
            margin: 4px 0 0 0;
            font-size: 0.85rem;
        }
        .param-item {
            padding: 20px 28px;
            border-bottom: 1px solid #f1f3f5;
            transition: background 0.15s ease;
        }
        .param-item:last-child {
            border-bottom: none;
        }
        .param-item:hover {
            background: #f8f9ff;
        }
        .param-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .param-icon.time-icon {
            background: #e8f0fe;
            color: #1a73e8;
        }
        .param-icon.text-icon {
            background: #e6f4ea;
            color: #188038;
        }
        .param-icon.number-icon {
            background: #fce8e6;
            color: #c5221f;
        }
        .param-label {
            font-weight: 600;
            color: #202124;
            font-size: 0.9rem;
            margin-bottom: 2px;
        }
        .param-key {
            font-size: 0.75rem;
            color: #80868b;
            font-family: monospace;
        }
        .param-input-wrap {
            position: relative;
        }
        .param-input-wrap .form-control {
            border-radius: 10px;
            border: 1.5px solid #e0e0e0;
            padding: 10px 16px;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fafafa;
            color: #202124;
        }
        .param-input-wrap .form-control:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 3px rgba(26,115,232,0.12);
            background: #fff;
        }
        .btn-guardar {
            background: #1a73e8;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            white-space: nowrap;
        }
        .btn-guardar:hover {
            background: #1558b0;
            color: #fff;
            box-shadow: 0 4px 12px rgba(26,115,232,0.3);
        }
        .btn-guardar:active {
            transform: scale(0.97);
        }
        .btn-guardar.saving {
            background: #80868b;
            pointer-events: none;
        }
        .btn-guardar.saved {
            background: #188038;
        }
        .page-title-section {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }
        .page-title-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.4rem;
            box-shadow: 0 4px 12px rgba(26,115,232,0.35);
        }
        .page-title-section h3 {
            margin: 0;
            font-weight: 700;
            color: #202124;
            font-size: 1.4rem;
        }
        .page-title-section p {
            margin: 2px 0 0 0;
            color: #80868b;
            font-size: 0.85rem;
        }
        .loading-skeleton {
            padding: 20px 28px;
        }
        .skeleton-line {
            height: 14px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .info-banner {
            background: linear-gradient(135deg, #e8f0fe, #f0f4ff);
            border-left: 4px solid #1a73e8;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .info-banner i {
            color: #1a73e8;
            font-size: 1.1rem;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .info-banner p {
            margin: 0;
            font-size: 0.85rem;
            color: #3c4043;
            line-height: 1.5;
        }
    </style>
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

                    <div class="page-title-section">
                        <div class="page-title-icon">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div>
                            <h3>Configuración del Sistema</h3>
                            <p>Administra los parámetros generales del sistema</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">

                            <div class="info-banner">
                                <i class="fas fa-info-circle"></i>
                                <p>Los cambios en los parámetros se aplican de forma inmediata. Asegúrate de ingresar valores válidos antes de guardar.</p>
                            </div>

                            <div class="config-card card mb-0">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <div>
                                        <h4><i class="fas fa-cog me-2"></i> Parámetros del Sistema</h4>
                                        <p id="params-count">Cargando parámetros...</p>
                                    </div>
                                </div>
                                <div id="lista-params">
                                    <div class="loading-skeleton">
                                        <div class="skeleton-line" style="width:60%"></div>
                                        <div class="skeleton-line" style="width:40%"></div>
                                        <div class="skeleton-line" style="width:80%"></div>
                                    </div>
                                    <div class="loading-skeleton" style="border-top:1px solid #f1f3f5">
                                        <div class="skeleton-line" style="width:50%"></div>
                                        <div class="skeleton-line" style="width:35%"></div>
                                        <div class="skeleton-line" style="width:70%"></div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-4">
                            <div class="card card-round border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-3" style="color:#202124;">
                                        <i class="fas fa-question-circle text-primary me-2"></i>Ayuda
                                    </h6>
                                    <ul class="list-unstyled mb-0" style="font-size:0.85rem; color:#5f6368; line-height:1.8;">
                                        <li><i class="fas fa-clock text-primary me-2"></i>La <strong>hora de corte</strong> determina a partir de qué hora del sábado se procesan los pagos automáticos.</li>
                                        <li class="mt-2"><i class="fas fa-save text-success me-2"></i>Cada parámetro se guarda de forma individual al presionar <strong>Guardar</strong>.</li>
                                        <li class="mt-2"><i class="fas fa-shield-alt text-warning me-2"></i>Solo administradores pueden modificar estos valores.</li>
                                    </ul>
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

        function getIconClass(tipo) {
            if (tipo === 'time') return 'fas fa-clock';
            if (tipo === 'number') return 'fas fa-hashtag';
            return 'fas fa-font';
        }

        function getIconWrapClass(tipo) {
            if (tipo === 'time') return 'time-icon';
            if (tipo === 'number') return 'number-icon';
            return 'text-icon';
        }

        function cargarParams() {
            $.get('api.php', { action: 'get' }, function(resp) {
                if (!resp.success) return;

                let count = resp.datos.length;
                $('#params-count').text(count + ' parámetro' + (count !== 1 ? 's' : '') + ' configurado' + (count !== 1 ? 's' : ''));

                let html = '';
                resp.datos.forEach(function(p) {
                    let iconClass = getIconClass(p.tipo);
                    let wrapClass = getIconWrapClass(p.tipo);
                    let inputAttrs = '';
                    if (p.tipo === 'number') inputAttrs = 'min="0" max="23"';
                    if (p.tipo === 'time') inputAttrs = '';

                    html += `
                        <div class="param-item d-flex align-items-center gap-3 flex-wrap">
                            <div class="param-icon ${wrapClass}">
                                <i class="${iconClass}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="param-label">${p.descripcion}</div>
                                <div class="param-key">${p.clave}</div>
                            </div>
                            <div class="d-flex align-items-center gap-2" style="min-width:200px;">
                                <div class="param-input-wrap flex-grow-1">
                                    <input type="${p.tipo}" class="form-control" id="param_${p.clave}"
                                           value="${p.valor}" ${inputAttrs}>
                                </div>
                                <button class="btn btn-guardar" id="btn_${p.clave}" onclick="guardar('${p.clave}')">
                                    <i class="fas fa-save me-1"></i> Guardar
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
            let $btn = $('#btn_' + clave);

            $btn.addClass('saving').html('<i class="fas fa-spinner fa-spin me-1"></i> Guardando...');

            $.post('api.php', { action: 'update', clave: clave, valor: valor }, function(resp) {
                if (resp.success) {
                    $btn.removeClass('saving').addClass('saved').html('<i class="fas fa-check me-1"></i> Guardado');
                    setTimeout(function() {
                        $btn.removeClass('saved').html('<i class="fas fa-save me-1"></i> Guardar');
                    }, 2000);
                } else {
                    $btn.removeClass('saving').html('<i class="fas fa-save me-1"></i> Guardar');
                    swal('Error', resp.message, 'error');
                }
            }, 'json').fail(function() {
                $btn.removeClass('saving').html('<i class="fas fa-save me-1"></i> Guardar');
                swal('Error', 'No se pudo conectar con el servidor.', 'error');
            });
        }
    </script>
</body>
</html>
