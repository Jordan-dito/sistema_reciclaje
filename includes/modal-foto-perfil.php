<?php
// Modal para cambiar foto de perfil
// Variables esperadas:
// - $basePath: prefijo para las rutas ('' o '../')

require_once __DIR__ . '/../config/database.php';

$basePath = isset($basePath) ? rtrim($basePath, '/') : '';
$basePath = $basePath !== '' ? $basePath . '/' : '';

// Obtener foto actual del usuario
$fotoActual = null;
if (isset($_SESSION['usuario_id'])) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $usuario = $stmt->fetch();
        if ($usuario && !empty($usuario['foto_perfil'])) {
            $fotoActual = $usuario['foto_perfil'];
        }
    } catch (Exception $e) {
        error_log("Error al obtener foto actual: " . $e->getMessage());
    }
}

// Obtener iniciales para el preview
$iniciales = '';
if (isset($_SESSION['usuario_nombre'])) {
    $nombres = explode(' ', $_SESSION['usuario_nombre']);
    $iniciales = strtoupper(substr($nombres[0], 0, 1));
    if (isset($nombres[1])) {
        $iniciales .= strtoupper(substr($nombres[1], 0, 1));
    } else {
        $iniciales .= strtoupper(substr($nombres[0], 1, 1));
    }
}
?>
<!-- Modal Foto de Perfil -->
<div class="modal fade" id="modalFotoPerfil" tabindex="-1" aria-hidden="false" data-bs-backdrop="static" data-bs-keyboard="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title text-white mb-0">
          <i class="fas fa-camera me-2"></i> Cambiar Foto de Perfil
        </h5>
        <button type="button" class="btn-close btn-close-white" onclick="cerrarModalFotoPerfil(); return false;" aria-label="Close" style="pointer-events: auto; z-index: 10005; cursor: pointer;"></button>
      </div>
      <div class="modal-body">
        <form id="formFotoPerfil" enctype="multipart/form-data">
          <div class="text-center mb-4">
            <div class="avatar-lg mx-auto mb-3 position-relative" style="width: 120px; height: 120px;">
              <div id="previewFoto" class="avatar-img rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 120px; height: 120px; font-size: 48px; line-height: 120px; border: 3px solid #e0e0e0;">
                <?php if ($fotoActual): ?>
                  <img src="<?php echo htmlspecialchars($basePath . $fotoActual); ?>" 
                       alt="Foto actual" 
                       class="avatar-img rounded-circle" 
                       style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e0e0e0;"
                       onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-user\'></i>';">
                <?php else: ?>
                  <i class="fas fa-user"></i>
                <?php endif; ?>
              </div>
            </div>
            <p class="text-muted small mb-0">Formatos permitidos: JPG, PNG, GIF</p>
            <p class="text-muted small">Tamaño máximo: 2MB</p>
          </div>
          <div class="form-group mb-3">
            <label for="foto_perfil_input" class="form-label fw-bold">Seleccionar Imagen</label>
            <div class="input-group">
              <input type="file" 
                     class="form-control" 
                     id="foto_perfil_input" 
                     name="foto_perfil" 
                     accept="image/jpeg,image/jpg,image/png,image/gif"
                     style="display: none;"
                     required>
              <input type="text" 
                     class="form-control" 
                     id="foto_perfil_display" 
                     placeholder="Ningún archivo seleccionado" 
                     readonly
                     style="background-color: #f8f9fa; cursor: pointer;">
              <button class="btn btn-primary" 
                      type="button" 
                      id="btnSeleccionarArchivo"
                      style="min-width: 150px;">
                <i class="fas fa-folder-open me-2"></i> Buscar Archivo
              </button>
            </div>
            <div id="fileStatus" class="mt-2" style="font-size: 0.875rem;"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="cerrarModalFotoPerfil(); return false;" style="pointer-events: auto; z-index: 10005; cursor: pointer;">
          <i class="fas fa-times me-2"></i> Cancelar
        </button>
        <button type="button" class="btn btn-primary" id="btnSubirFoto">
          <i class="fas fa-upload me-2"></i> Subir Foto
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// Función global para cerrar el modal
window.cerrarModalFotoPerfil = function() {
  console.log('[Foto Perfil] Función cerrarModalFotoPerfil llamada');
  
  if (typeof jQuery === 'undefined') {
    console.error('[Foto Perfil] jQuery no disponible para cerrar modal');
    // Intentar cerrar sin jQuery
    var modalElement = document.getElementById('modalFotoPerfil');
    if (modalElement) {
      var bsModal = bootstrap.Modal.getInstance(modalElement);
      if (bsModal) {
        bsModal.hide();
      }
      modalElement.classList.remove('show');
      modalElement.style.display = 'none';
      modalElement.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('modal-open');
      var backdrops = document.querySelectorAll('.modal-backdrop');
      backdrops.forEach(function(backdrop) {
        backdrop.remove();
      });
    }
    return;
  }
  
  var $ = jQuery;
  var modal = $('#modalFotoPerfil');
  
  if (modal.length === 0) {
    console.warn('[Foto Perfil] Modal no encontrado');
    return;
  }
  
  // Método 1: Bootstrap 5 nativo
  var modalElement = document.getElementById('modalFotoPerfil');
  if (modalElement) {
    var bsModal = bootstrap.Modal.getInstance(modalElement);
    if (bsModal) {
      console.log('[Foto Perfil] Cerrando con Bootstrap Modal instance');
      bsModal.hide();
    } else {
      var newModal = new bootstrap.Modal(modalElement);
      newModal.hide();
    }
  }
  
  // Método 2: jQuery (respaldo)
  modal.modal('hide');
  
  // Método 3: Forzar cierre manual
  setTimeout(function() {
    // Remover backdrop
    $('.modal-backdrop').remove();
    // Remover clases del body
    $('body').removeClass('modal-open');
    $('body').css({
      'overflow': '',
      'padding-right': ''
    });
    // Ocultar modal manualmente
    modal.removeClass('show').css({
      'display': 'none',
      'z-index': '',
      'pointer-events': ''
    });
    modal.attr('aria-hidden', 'true');
    modal.removeAttr('aria-modal');
    
    // Limpiar formulario
    var form = $('#formFotoPerfil')[0];
    if (form) {
      form.reset();
    }
    $('#foto_perfil_display').val('');
    $('#foto_perfil_display').css('color', '');
    $('#fileStatus').html('');
    
    // Restaurar preview
    <?php if ($fotoActual): ?>
      $('#previewFoto').html('<img src="<?php echo htmlspecialchars($basePath . $fotoActual); ?>" class="avatar-img rounded-circle" style="width: 120px; height: 120px; object-fit: cover;" onerror="this.parentElement.innerHTML=\'<i class=\\\'fas fa-user\\\'></i>\';">');
    <?php else: ?>
      $('#previewFoto').html('<i class="fas fa-user"></i>');
    <?php endif; ?>
    
    console.log('[Foto Perfil] Modal cerrado completamente');
  }, 100);
};

// Función global para abrir el modal
window.abrirModalFotoPerfil = function() {
  console.log('[Foto Perfil] Función abrirModalFotoPerfil llamada');
  
  // Función interna que usa jQuery
  function abrirModal() {
    if (typeof jQuery === 'undefined') {
      console.error('[Foto Perfil] jQuery no disponible, reintentando...');
      setTimeout(abrirModal, 100);
      return;
    }
    
    var $ = jQuery;
      console.log('[Foto Perfil] jQuery disponible, abriendo modal...');
      
      // Cerrar cualquier modal abierto primero
      $('.modal').modal('hide');
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open');
      $('body').css('padding-right', '');
      
      // Esperar un momento y abrir el modal SIN backdrop
      setTimeout(function() {
        console.log('[Foto Perfil] Intentando abrir modal...');
        var modal = $('#modalFotoPerfil');
        
        if (modal.length === 0) {
          console.error('[Foto Perfil] Modal no encontrado en el DOM');
          alert('Error: El modal no se encontró. Por favor recarga la página.');
          return;
        }
        
        console.log('[Foto Perfil] Modal encontrado, abriendo SIN backdrop...');
        // Abrir modal sin backdrop para evitar bloqueos
        modal.modal({
          backdrop: false,
          keyboard: true,
          show: true
        });
        
        // Verificar que se abrió
        setTimeout(function() {
          if (modal.hasClass('show')) {
            console.log('[Foto Perfil] Modal abierto correctamente');
          } else {
            console.warn('[Foto Perfil] Modal no se abrió, forzando...');
            modal.addClass('show');
            modal.css({
              'display': 'block',
              'z-index': '9999',
              'pointer-events': 'auto'
            });
            $('body').addClass('modal-open');
          }
          
          // Asegurar que todos los elementos del modal sean interactivos
          modal.find('*').css('pointer-events', 'auto');
          modal.find('input, button, select, textarea').css({
            'pointer-events': 'auto',
            'z-index': '10000'
          });
          
          console.log('[Foto Perfil] Modal configurado para ser interactivo');
        }, 300);
      }, 100);
  }
  
  // Intentar abrir
  abrirModal();
};

console.log('[Foto Perfil] Script iniciado');
// Esperar a que jQuery y todos los scripts estén cargados
(function() {
  var intentos = 0;
  var maxIntentos = 50; // 5 segundos máximo
  
  function initFotoPerfil() {
    console.log('[Foto Perfil] Intentando inicializar... Intento:', intentos + 1);
    
    // Verificar que jQuery y swal estén disponibles
    if (typeof jQuery === 'undefined') {
      console.warn('[Foto Perfil] jQuery no disponible, reintentando...');
      intentos++;
      if (intentos < maxIntentos) {
        setTimeout(initFotoPerfil, 100);
      } else {
        console.error('[Foto Perfil] jQuery no se cargó después de', maxIntentos, 'intentos');
      }
      return;
    }
    
    if (typeof swal === 'undefined') {
      console.warn('[Foto Perfil] SweetAlert no disponible, reintentando...');
      intentos++;
      if (intentos < maxIntentos) {
        setTimeout(initFotoPerfil, 100);
      } else {
        console.error('[Foto Perfil] SweetAlert no se cargó después de', maxIntentos, 'intentos');
      }
      return;
    }
    
    console.log('[Foto Perfil] jQuery y SweetAlert disponibles, configurando eventos...');
    var $ = jQuery;
    
    // Verificar que los elementos existan
    var fileInput = $('#foto_perfil_input');
    var btnSubir = $('#btnSubirFoto');
    var modal = $('#modalFotoPerfil');
    
    console.log('[Foto Perfil] Elementos encontrados:', {
      fileInput: fileInput.length,
      btnSubir: btnSubir.length,
      modal: modal.length
    });
    
    // Botón para abrir el selector de archivos - usar delegación de eventos
    $(document).on('click', '#btnSeleccionarArchivo', function(e) {
      console.log('[Foto Perfil] Botón seleccionar archivo clickeado');
      e.preventDefault();
      e.stopPropagation();
      
      var fileInput = $('#foto_perfil_input')[0];
      if (fileInput) {
        console.log('[Foto Perfil] Abriendo selector de archivos...');
        fileInput.click();
      } else {
        console.error('[Foto Perfil] Input file no encontrado');
        alert('Error: No se encontró el input de archivo');
      }
    });
    
    // También permitir click en el input display
    $(document).on('click', '#foto_perfil_display', function(e) {
      console.log('[Foto Perfil] Display clickeado, abriendo selector...');
      e.preventDefault();
      $('#btnSeleccionarArchivo').click();
    });
    
    // Preview de la imagen antes de subir
    $(document).on('change', '#foto_perfil_input', function(e) {
      console.log('[Foto Perfil] ===== CAMBIO DETECTADO EN INPUT FILE =====');
      console.log('[Foto Perfil] Evento:', e);
      console.log('[Foto Perfil] Target:', e.target);
      console.log('[Foto Perfil] Files:', e.target.files);
      console.log('[Foto Perfil] Files length:', e.target.files ? e.target.files.length : 0);
      
      var file = e.target.files[0];
      console.log('[Foto Perfil] Archivo seleccionado:', file ? {
        name: file.name,
        size: file.size,
        type: file.type
      } : 'ninguno');
      
      // Actualizar status con mejor diseño
      if (file) {
        var fileSize = file.size < 1024 ? file.size + ' B' : (file.size / 1024).toFixed(2) + ' KB';
        var fileName = file.name.length > 40 ? file.name.substring(0, 40) + '...' : file.name;
        $('#fileStatus').html(
          '<div class="alert alert-success alert-dismissible fade show py-2" role="alert">' +
          '<i class="fas fa-check-circle me-2"></i>' +
          '<strong>Archivo seleccionado:</strong> ' + fileName + ' (' + fileSize + ')' +
          '<button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>' +
          '</div>'
        );
      } else {
        $('#fileStatus').html('');
      }
      
      if (file) {
        // Actualizar el display con mejor formato
        var fileName = file.name.length > 30 ? file.name.substring(0, 30) + '...' : file.name;
        $('#foto_perfil_display').val(fileName);
        $('#foto_perfil_display').css('color', '#28a745');
        console.log('[Foto Perfil] Nombre del archivo actualizado en display');
        
        console.log('[Foto Perfil] Iniciando lectura del archivo...');
        var reader = new FileReader();
        reader.onload = function(e) {
          console.log('[Foto Perfil] Archivo leído exitosamente, actualizando preview');
          var preview = $('#previewFoto');
          if (preview.length) {
            preview.html('<img src="' + e.target.result + '" class="avatar-img rounded-circle" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e0e0e0;">');
            console.log('[Foto Perfil] Preview actualizado correctamente');
          } else {
            console.error('[Foto Perfil] Elemento previewFoto no encontrado');
          }
        };
        reader.onerror = function(e) {
          console.error('[Foto Perfil] Error al leer archivo:', e);
          $('#fileStatus').html(
            '<div class="alert alert-danger alert-dismissible fade show py-2" role="alert">' +
            '<i class="fas fa-exclamation-circle me-2"></i> Error al leer el archivo' +
            '<button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>'
          );
        };
        reader.onprogress = function(e) {
          if (e.lengthComputable) {
            var percentLoaded = Math.round((e.loaded / e.total) * 100);
            console.log('[Foto Perfil] Progreso de lectura:', percentLoaded + '%');
          }
        };
        reader.readAsDataURL(file);
      } else {
        console.warn('[Foto Perfil] No se seleccionó ningún archivo');
        $('#foto_perfil_display').val('');
        $('#foto_perfil_display').css('color', '');
      }
    });
    
    // Subir foto de perfil
    $(document).on('click', '#btnSubirFoto', function(e) {
      console.log('[Foto Perfil] Botón Subir Foto clickeado');
      e.preventDefault();
      e.stopPropagation();
      
      var form = $('#formFotoPerfil')[0];
      if (!form) {
        console.error('[Foto Perfil] Formulario no encontrado');
        alert('Error: Formulario no encontrado');
        return;
      }
      
      console.log('[Foto Perfil] Formulario encontrado, creando FormData...');
      var formData = new FormData(form);
      var fileInput = $('#foto_perfil_input')[0];
      
      console.log('[Foto Perfil] Verificando archivo...', {
        fileInput: fileInput ? 'existe' : 'no existe',
        files: fileInput && fileInput.files ? fileInput.files.length : 0
      });
      
      if (!fileInput || !fileInput.files || !fileInput.files[0]) {
        console.warn('[Foto Perfil] No se seleccionó ningún archivo');
        swal("Error", "Por favor selecciona una imagen", "error");
        return;
      }
      
      var file = fileInput.files[0];
      console.log('[Foto Perfil] Archivo validado:', {
        name: file.name,
        size: file.size,
        type: file.type
      });
      
      // Validar tamaño
      if (file.size > 2 * 1024 * 1024) {
        console.warn('[Foto Perfil] Archivo demasiado grande:', file.size);
        swal("Error", "El archivo es demasiado grande. Tamaño máximo: 2MB", "error");
        return;
      }
      
      // Validar tipo
      var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
      if (!allowedTypes.includes(file.type)) {
        console.warn('[Foto Perfil] Tipo de archivo no permitido:', file.type);
        swal("Error", "Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG o GIF", "error");
        return;
      }
      
      var uploadUrl = '<?php echo $basePath; ?>config/upload_foto_perfil.php';
      console.log('[Foto Perfil] Iniciando upload a:', uploadUrl);
      
      $.ajax({
        url: uploadUrl,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        xhrFields: {
          withCredentials: true
        },
        crossDomain: false,
        beforeSend: function() {
          console.log('[Foto Perfil] Enviando petición...');
          $('#btnSubirFoto').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
        },
        success: function(response) {
          console.log('[Foto Perfil] Respuesta recibida:', response);
          $('#btnSubirFoto').prop('disabled', false).html('Subir Foto');
          if (response.success) {
            swal("¡Éxito!", response.message, "success").then(function() {
              console.log('[Foto Perfil] Cerrando modal...');
              // Cerrar el modal de forma forzada - múltiples métodos
              var modalElement = document.getElementById('modalFotoPerfil');
              
              // Método 1: Bootstrap 5 nativo
              if (modalElement) {
                var modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                  modal.hide();
                } else {
                  var newModal = new bootstrap.Modal(modalElement);
                  newModal.hide();
                }
              }
              
              // Método 2: jQuery (respaldo)
              $('#modalFotoPerfil').modal('hide');
              
              // Método 3: Forzar cierre manual
              setTimeout(function() {
                // Remover backdrop
                $('.modal-backdrop').remove();
                // Remover clases del body
                $('body').removeClass('modal-open');
                $('body').css({'overflow': '', 'padding-right': ''});
                // Ocultar modal manualmente
                $('#modalFotoPerfil').removeClass('show').css('display', 'none');
                $('#modalFotoPerfil').attr('aria-hidden', 'true');
                $('#modalFotoPerfil').removeAttr('aria-modal');
                
                console.log('[Foto Perfil] Modal cerrado, recargando página...');
                // Recargar la página para actualizar la foto en el header
                location.reload();
              }, 300);
            });
          } else {
            console.error('[Foto Perfil] Error en respuesta:', response.message);
            swal("Error", response.message || "Error al subir la foto", "error");
          }
        },
        error: function(xhr, status, error) {
          console.error('[Foto Perfil] Error en AJAX:', {
            status: xhr.status,
            statusText: xhr.statusText,
            error: error,
            responseText: xhr.responseText
          });
          $('#btnSubirFoto').prop('disabled', false).html('Subir Foto');
          var errorMsg = 'Error al subir la foto';
          try {
            var response = JSON.parse(xhr.responseText);
            errorMsg = response.message || errorMsg;
          } catch(e) {
            errorMsg = xhr.statusText || errorMsg;
          }
          swal("Error", errorMsg, "error");
        }
      });
    });
    
    // Limpiar preview al cerrar el modal (restaurar foto actual)
    $(document).on('hidden.bs.modal', '#modalFotoPerfil', function() {
      console.log('[Foto Perfil] Modal cerrado, limpiando formulario');
      var form = $('#formFotoPerfil')[0];
      if (form) {
        form.reset();
      }
      $('#foto_perfil_display').val('');
      $('#foto_perfil_display').css('color', '');
      $('#fileStatus').html('');
      <?php if ($fotoActual): ?>
        $('#previewFoto').html('<img src="<?php echo htmlspecialchars($basePath . $fotoActual); ?>" class="avatar-img rounded-circle" style="width: 120px; height: 120px; object-fit: cover;" onerror="this.parentElement.innerHTML=\'<i class=\\\'fas fa-user\\\'></i>\';">');
      <?php else: ?>
        $('#previewFoto').html('<i class="fas fa-user"></i>');
      <?php endif; ?>
      // Asegurar que el backdrop se elimine
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open');
      $('body').css({'overflow': '', 'padding-right': ''});
    });
    
    // También manejar el evento cuando se intenta cerrar
    $('#modalFotoPerfil').on('hide.bs.modal', function(e) {
      console.log('[Foto Perfil] Intentando cerrar modal...');
    });
    
    // Verificar cuando se abre el modal
    $('#modalFotoPerfil').on('show.bs.modal', function(e) {
      console.log('[Foto Perfil] Modal se está abriendo...');
    });
    
    $('#modalFotoPerfil').on('shown.bs.modal', function() {
      console.log('[Foto Perfil] Modal abierto y listo');
      
      var modal = $(this);
      
      // ELIMINAR cualquier backdrop que pueda estar bloqueando
      $('.modal-backdrop').remove();
      
      // Asegurar que el modal sea completamente interactivo
      modal.css({
        'z-index': '9999',
        'pointer-events': 'auto',
        'display': 'block'
      });
      
      modal.find('.modal-dialog').css({
        'z-index': '10000',
        'pointer-events': 'auto'
      });
      
      modal.find('.modal-content').css({
        'z-index': '10001',
        'pointer-events': 'auto'
      });
      
      // Asegurar que TODOS los elementos sean interactivos
      modal.find('*').each(function() {
        $(this).css({
          'pointer-events': 'auto',
          'z-index': '10002'
        });
      });
      
      // Configurar botones específicamente
      var btnSeleccionar = $('#btnSeleccionarArchivo');
      var btnSubir = $('#btnSubirFoto');
      var fileInput = $('#foto_perfil_input');
      var fileDisplay = $('#foto_perfil_display');
      
      // Forzar que los botones sean clickeables
      btnSeleccionar.css({
        'pointer-events': 'auto !important',
        'z-index': '10003',
        'cursor': 'pointer',
        'position': 'relative',
        'display': 'inline-block'
      });
      
      btnSubir.css({
        'pointer-events': 'auto !important',
        'z-index': '10003',
        'cursor': 'pointer'
      });
      
      fileInput.css({
        'pointer-events': 'auto !important',
        'z-index': '10004'
      });
      
      fileDisplay.css({
        'pointer-events': 'auto !important',
        'cursor': 'pointer',
        'z-index': '10003'
      });
      
      // Re-asignar eventos directamente con eventos nativos también
      btnSeleccionar.off('click').on('click', function(e) {
        console.log('[Foto Perfil] Click directo en botón seleccionar');
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        fileInput[0].click();
        return false;
      });
      
      // También agregar evento nativo
      btnSeleccionar[0].addEventListener('click', function(e) {
        console.log('[Foto Perfil] Click NATIVO en botón seleccionar');
        e.preventDefault();
        e.stopPropagation();
        fileInput[0].click();
      }, true);
      
      fileDisplay.off('click').on('click', function(e) {
        console.log('[Foto Perfil] Click en display');
        e.preventDefault();
        btnSeleccionar.click();
      });
      
      console.log('[Foto Perfil] Modal completamente interactivo - sin backdrop');
    });
    
    // Limpiar cuando se cierra el modal
    $('#modalFotoPerfil').on('hidden.bs.modal', function() {
      console.log('[Foto Perfil] Modal cerrado');
    });
    
    // AGREGAR EVENTOS PARA LOS BOTONES DE CERRAR Y CANCELAR
    // Botón de cerrar (X) en el header
    $(document).on('click', '#modalFotoPerfil .btn-close', function(e) {
      console.log('[Foto Perfil] Botón cerrar (X) clickeado');
      e.preventDefault();
      e.stopPropagation();
      cerrarModalFotoPerfil();
      return false;
    });
    
    // Botón Cancelar en el footer
    $(document).on('click', '#modalFotoPerfil .btn-secondary[data-bs-dismiss="modal"]', function(e) {
      console.log('[Foto Perfil] Botón Cancelar clickeado');
      e.preventDefault();
      e.stopPropagation();
      cerrarModalFotoPerfil();
      return false;
    });
    
    console.log('[Foto Perfil] Inicialización completa');
  }
  
  // Inicializar cuando el DOM esté listo
  if (document.readyState === 'loading') {
    console.log('[Foto Perfil] Esperando DOMContentLoaded...');
    document.addEventListener('DOMContentLoaded', initFotoPerfil);
  } else {
    console.log('[Foto Perfil] DOM ya listo, inicializando inmediatamente...');
    initFotoPerfil();
  }
})();
</script>

