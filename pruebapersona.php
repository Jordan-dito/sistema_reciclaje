<?php
/**
 * Página de prueba para Asistente Personal
 */
// Verificar autenticación
require_once __DIR__ . '/config/auth.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    header('Location: index.php');
    exit;
}

$usuario = $auth->getCurrentUser();
$usuarioNombre = $usuario['nombre'] ?? 'Usuario';

?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Asistente Personal - <?php echo htmlspecialchars($usuarioNombre); ?></title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/logo.jpg" type="image/jpeg" />

    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: { families: ["Font Awesome 5 Solid","Font Awesome 5 Regular","Font Awesome 5 Brands","simple-line-icons"], urls: ["assets/css/fonts.min.css"] },
        active: function () { sessionStorage.fonts = true; }
      });
    </script>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
  </head>
  <body>
    <div class="wrapper">
      <div class="sidebar" data-background-color="dark">
        <?php
          $basePath = '';
          include __DIR__ . '/includes/sidebar-logo.php';
        ?>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <?php
              $basePath = '';
              $currentRoute = 'asistente_personal';
              include __DIR__ . '/includes/sidebar.php';
            ?>
          </div>
        </div>
      </div>

      <div class="main-panel">
        <div class="main-header">
          <?php
            $basePath = '';
            include __DIR__ . '/includes/main-header-logo.php';
          ?>
          <?php
            $basePath = '';
            include __DIR__ . '/includes/user-header.php';
            include __DIR__ . '/includes/modal-foto-perfil.php';
            include __DIR__ . '/includes/modal-cambiar-password.php';
          ?>
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Gestión de Personal</div>
                    <div class="text-muted">Usuario: <?php echo htmlspecialchars($usuarioNombre); ?></div>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="card card-round mb-3">
                          <div class="card-body">
                            <h5>Acciones Rápidas</h5>
                            <p class="text-muted small">Sugerencias para empezar:</p>
                            <div class="d-grid gap-2">
                              <button class="btn btn-outline-primary btn-sm quick-prompt">Generar informe del mes</button>
                              <button class="btn btn-outline-primary btn-sm quick-prompt">Buscar cliente por cédula</button>
                              <button class="btn btn-outline-primary btn-sm quick-prompt">Registrar nueva venta</button>
                              <button class="btn btn-outline-secondary btn-sm" id="limpiarChat">Limpiar chat</button>
                            </div>
                          </div>
                        </div>
                        <div class="card card-round">
                          <div class="card-body">
                            <h6 class="mb-2">Información</h6>
                            <p class="text-muted small">Esta pantalla es un ejemplo de interfaz para el módulo "Gestión de Personal". Aquí puedes integrar formularios y búsquedas relacionadas al personal.</p>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-8">
                        <div class="card card-round mb-3">
                          <div class="card-body p-0" style="height:520px; display:flex; flex-direction:column;">
                            <div id="chatWindow" style="flex:1; overflow:auto; padding:20px; background:#f7f9fc;">
                              <!-- Mensajes -->
                            </div>
                            <div style="border-top:1px solid #e9ecef; padding:12px;">
                              <div class="input-group">
                                <input id="chatInput" type="text" class="form-control" placeholder="Escribe tu mensaje... (ej. ¿Cuál es el total de ventas hoy?)">
                                <button id="sendBtn" class="btn btn-primary">Enviar</button>
                              </div>
                              <div class="mt-2 d-flex gap-2">
                                <small class="text-muted">Respuesta simulada localmente para demo.</small>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="assets/js/core/jquery.3.2.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/kaiadmin.min.js"></script>
    <style>
      .msg { margin-bottom:14px; display:flex; }
      .msg .bubble { max-width:78%; padding:10px 14px; border-radius:12px; }
      .msg.user { justify-content:flex-end; }
      .msg.user .bubble { background:#0d6efd; color:white; border-bottom-right-radius:4px; }
      .msg.assistant { justify-content:flex-start; }
      .msg.assistant .bubble { background:#e9ecef; color:#212529; border-bottom-left-radius:4px; }
      #chatWindow::-webkit-scrollbar { width:8px; }
      #chatWindow::-webkit-scrollbar-thumb { background:#d0d7de; border-radius:4px; }
    </style>
    <script>
      (function(){
        const chatWindow = document.getElementById('chatWindow');
        const chatInput = document.getElementById('chatInput');
        const sendBtn = document.getElementById('sendBtn');
        const quicks = document.querySelectorAll('.quick-prompt');
        const limpiar = document.getElementById('limpiarChat');

        function appendMessage(role, text){
          const li = document.createElement('div');
          li.className = 'msg ' + (role === 'user' ? 'user' : 'assistant');
          const bubble = document.createElement('div');
          bubble.className = 'bubble';
          bubble.textContent = text;
          li.appendChild(bubble);
          chatWindow.appendChild(li);
          chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        function simulateAssistantReply(userText){
          // Respuestas de ejemplo basadas en palabras clave
          let reply = 'Lo siento, no entiendo la consulta. Intenta con otra pregunta.';
          const text = userText.toLowerCase();
          if (text.includes('ventas')) reply = 'Resumen: hoy se registraron 12 ventas por un total de $3,450.00.';
          else if (text.includes('cliente') || text.includes('cédula')) reply = 'Ingrese la cédula del cliente para buscar: ejemplo 0102030405.';
          else if (text.includes('informe')) reply = 'Generando informe... (este es un demo). Puedes exportar a PDF desde la sección de reportes.';
          else if (text.includes('hola') || text.includes('buenos')) reply = '¡Hola! ¿En qué puedo ayudarte hoy?';

          // Simular tiempo de procesamiento
          appendMessage('assistant', 'Escribiendo...');
          setTimeout(function(){
            // quitar 'Escribiendo...'
            const last = chatWindow.querySelectorAll('.msg.assistant');
            if (last.length) last[last.length-1].remove();
            appendMessage('assistant', reply);
          }, 700);
        }

        sendBtn.addEventListener('click', function(){
          const text = chatInput.value.trim();
          if (!text) return;
          appendMessage('user', text);
          chatInput.value = '';
          simulateAssistantReply(text);
        });

        chatInput.addEventListener('keydown', function(e){ if (e.key === 'Enter'){ e.preventDefault(); sendBtn.click(); } });

        quicks.forEach(function(btn){ btn.addEventListener('click', function(){ const txt = this.textContent.trim(); chatInput.value = txt; sendBtn.click(); }); });

        limpiar.addEventListener('click', function(){ chatWindow.innerHTML = ''; appendMessage('assistant','Hola <?php echo addslashes($usuarioNombre); ?> — soy tu asistente. Prueba una sugerencia o escribe tu consulta.'); });

        // Mensaje inicial
        appendMessage('assistant','Hola <?php echo addslashes($usuarioNombre); ?> — soy tu asistente. Prueba una sugerencia o escribe tu consulta.');
      })();
    </script>
  </body>
</html>
