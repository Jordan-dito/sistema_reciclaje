<?php
/**
 * Modal para cambiar contraseña del usuario
 * Sistema de Gestión de Reciclaje
 */

// Variables esperadas:
// - $basePath: prefijo para las rutas ('' o '../')

$basePath = isset($basePath) ? rtrim($basePath, '/') : '';
$basePath = $basePath !== '' ? $basePath . '/' : '';
?>
<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="modalCambiarPassword" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title mb-0">
          <i class="fas fa-key me-2"></i> Cambiar Contraseña
        </h5>
        <button type="button" class="btn-close" onclick="cerrarModalCambiarPassword(); return false;" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formCambiarPassword">
          <div class="alert alert-info py-2 mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <small>Tu nueva contraseña debe tener al menos 8 caracteres. Se enviará una notificación a tu correo electrónico.</small>
          </div>
          
          <!-- Contraseña Actual -->
          <div class="form-group mb-3">
            <label for="password_actual" class="form-label fw-bold">
              <i class="fas fa-lock me-1 text-muted"></i> Contraseña Actual
            </label>
            <div class="input-group">
              <input type="password" 
                     class="form-control" 
                     id="password_actual" 
                     name="password_actual" 
                     placeholder="Ingresa tu contraseña actual"
                     required
                     autocomplete="current-password">
              <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password_actual', this)">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>
          
          <hr class="my-3">
          
          <!-- Nueva Contraseña -->
          <div class="form-group mb-3">
            <label for="password_nueva" class="form-label fw-bold">
              <i class="fas fa-key me-1 text-muted"></i> Nueva Contraseña
            </label>
            <div class="input-group">
              <input type="password" 
                     class="form-control" 
                     id="password_nueva" 
                     name="password_nueva" 
                     placeholder="Ingresa tu nueva contraseña"
                     minlength="8"
                     required
                     autocomplete="new-password">
              <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password_nueva', this)">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <div id="password_strength" class="mt-2" style="display: none;">
              <div class="progress" style="height: 5px;">
                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
              </div>
              <small class="text-muted" id="password_strength_text"></small>
            </div>
          </div>
          
          <!-- Confirmar Nueva Contraseña -->
          <div class="form-group mb-3">
            <label for="password_confirmar" class="form-label fw-bold">
              <i class="fas fa-check-double me-1 text-muted"></i> Confirmar Nueva Contraseña
            </label>
            <div class="input-group">
              <input type="password" 
                     class="form-control" 
                     id="password_confirmar" 
                     name="password_confirmar" 
                     placeholder="Confirma tu nueva contraseña"
                     minlength="8"
                     required
                     autocomplete="new-password">
              <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password_confirmar', this)">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <div id="password_match" class="mt-1" style="display: none;"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="cerrarModalCambiarPassword(); return false;">
          <i class="fas fa-times me-2"></i> Cancelar
        </button>
        <button type="button" class="btn btn-warning" id="btnGuardarPassword">
          <i class="fas fa-save me-2"></i> Guardar Cambios
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// Función para mostrar/ocultar contraseña
function togglePasswordVisibility(inputId, button) {
  var input = document.getElementById(inputId);
  var icon = button.querySelector('i');
  
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}

// Función global para cerrar el modal
window.cerrarModalCambiarPassword = function() {
  console.log('[Cambiar Password] Cerrando modal...');
  
  var modalElement = document.getElementById('modalCambiarPassword');
  if (modalElement) {
    // Bootstrap 5 nativo
    var bsModal = bootstrap.Modal.getInstance(modalElement);
    if (bsModal) {
      bsModal.hide();
    }
  }
  
  // jQuery como respaldo
  if (typeof jQuery !== 'undefined') {
    jQuery('#modalCambiarPassword').modal('hide');
  }
  
  // Limpiar después de cerrar
  setTimeout(function() {
    if (typeof jQuery !== 'undefined') {
      jQuery('.modal-backdrop').remove();
      jQuery('body').removeClass('modal-open').css({'overflow': '', 'padding-right': ''});
    }
    
    // Limpiar formulario
    var form = document.getElementById('formCambiarPassword');
    if (form) form.reset();
    
    // Ocultar indicadores
    var strengthEl = document.getElementById('password_strength');
    if (strengthEl) strengthEl.style.display = 'none';
    var matchEl = document.getElementById('password_match');
    if (matchEl) matchEl.style.display = 'none';
    
    console.log('[Cambiar Password] Modal cerrado y formulario limpiado');
  }, 100);
};

// Función global para abrir el modal
window.abrirModalCambiarPassword = function() {
  console.log('[Cambiar Password] Abriendo modal...');
  
  var modalElement = document.getElementById('modalCambiarPassword');
  if (!modalElement) {
    console.error('[Cambiar Password] Modal no encontrado en el DOM');
    alert('Error: Modal no encontrado. Por favor recarga la página.');
    return;
  }
  
  // Cerrar otros modales primero
  if (typeof jQuery !== 'undefined') {
    jQuery('.modal').modal('hide');
    jQuery('.modal-backdrop').remove();
    jQuery('body').removeClass('modal-open');
  }
  
  // Abrir modal
  setTimeout(function() {
    var modal = new bootstrap.Modal(modalElement, {
      backdrop: 'static',
      keyboard: true
    });
    modal.show();
    
    // Focus en el primer campo
    setTimeout(function() {
      var firstInput = document.getElementById('password_actual');
      if (firstInput) firstInput.focus();
    }, 300);
    
    console.log('[Cambiar Password] Modal abierto');
  }, 100);
};

// Inicialización cuando el DOM esté listo
(function() {
  function initCambiarPassword() {
    console.log('[Cambiar Password] Inicializando...');
    
    if (typeof jQuery === 'undefined') {
      console.warn('[Cambiar Password] jQuery no disponible, reintentando...');
      setTimeout(initCambiarPassword, 100);
      return;
    }
    
    var $ = jQuery;
    
    // Validación de fortaleza de contraseña
    $(document).on('input', '#password_nueva', function() {
      var password = $(this).val();
      var strengthEl = $('#password_strength');
      var progressBar = strengthEl.find('.progress-bar');
      var textEl = $('#password_strength_text');
      
      if (password.length === 0) {
        strengthEl.hide();
        return;
      }
      
      strengthEl.show();
      
      var strength = 0;
      var text = '';
      var colorClass = '';
      
      // Longitud
      if (password.length >= 8) strength += 25;
      if (password.length >= 12) strength += 15;
      
      // Mayúsculas
      if (/[A-Z]/.test(password)) strength += 20;
      
      // Minúsculas
      if (/[a-z]/.test(password)) strength += 15;
      
      // Números
      if (/[0-9]/.test(password)) strength += 15;
      
      // Caracteres especiales
      if (/[^A-Za-z0-9]/.test(password)) strength += 10;
      
      if (strength < 40) {
        text = 'Débil';
        colorClass = 'bg-danger';
      } else if (strength < 70) {
        text = 'Media';
        colorClass = 'bg-warning';
      } else {
        text = 'Fuerte';
        colorClass = 'bg-success';
      }
      
      progressBar.css('width', strength + '%')
                 .removeClass('bg-danger bg-warning bg-success')
                 .addClass(colorClass);
      textEl.text('Fortaleza: ' + text);
      
      // Verificar coincidencia si ya hay confirmación
      var confirmPass = $('#password_confirmar').val();
      if (confirmPass.length > 0) {
        checkPasswordMatch();
      }
    });
    
    // Verificar coincidencia de contraseñas
    function checkPasswordMatch() {
      var password = $('#password_nueva').val();
      var confirm = $('#password_confirmar').val();
      var matchEl = $('#password_match');
      
      if (confirm.length === 0) {
        matchEl.hide();
        return;
      }
      
      matchEl.show();
      
      if (password === confirm) {
        matchEl.html('<small class="text-success"><i class="fas fa-check-circle me-1"></i> Las contraseñas coinciden</small>');
      } else {
        matchEl.html('<small class="text-danger"><i class="fas fa-times-circle me-1"></i> Las contraseñas no coinciden</small>');
      }
    }
    
    $(document).on('input', '#password_confirmar', checkPasswordMatch);
    
    // Enviar formulario
    $(document).on('click', '#btnGuardarPassword', function(e) {
      e.preventDefault();
      console.log('[Cambiar Password] Enviando formulario...');
      
      var passwordActual = $('#password_actual').val().trim();
      var passwordNueva = $('#password_nueva').val().trim();
      var passwordConfirmar = $('#password_confirmar').val().trim();
      
      // Validaciones del lado del cliente
      if (!passwordActual || !passwordNueva || !passwordConfirmar) {
        swal('Error', 'Todos los campos son obligatorios', 'error');
        return;
      }
      
      if (passwordNueva.length < 8) {
        swal('Error', 'La nueva contraseña debe tener al menos 8 caracteres', 'error');
        return;
      }
      
      if (passwordNueva !== passwordConfirmar) {
        swal('Error', 'La nueva contraseña y su confirmación no coinciden', 'error');
        return;
      }
      
      if (passwordActual === passwordNueva) {
        swal('Error', 'La nueva contraseña debe ser diferente a la actual', 'error');
        return;
      }
      
      var btn = $(this);
      var originalText = btn.html();
      
      $.ajax({
        url: '<?php echo $basePath; ?>config/cambiar_password.php',
        method: 'POST',
        data: {
          password_actual: passwordActual,
          password_nueva: passwordNueva,
          password_confirmar: passwordConfirmar
        },
        dataType: 'json',
        beforeSend: function() {
          btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
        },
        success: function(response) {
          btn.prop('disabled', false).html(originalText);
          
          if (response.success) {
            swal({
              title: '¡Éxito!',
              text: response.message,
              icon: 'success',
              button: 'Aceptar'
            }).then(function() {
              cerrarModalCambiarPassword();
            });
          } else {
            swal('Error', response.message || 'Error al cambiar la contraseña', 'error');
          }
        },
        error: function(xhr, status, error) {
          btn.prop('disabled', false).html(originalText);
          console.error('[Cambiar Password] Error:', {status: status, error: error, response: xhr.responseText});
          
          var errorMsg = 'Error al procesar la solicitud';
          try {
            var response = JSON.parse(xhr.responseText);
            errorMsg = response.message || errorMsg;
          } catch(e) {}
          
          swal('Error', errorMsg, 'error');
        }
      });
    });
    
    // Enviar con Enter
    $(document).on('keypress', '#formCambiarPassword input', function(e) {
      if (e.which === 13) {
        e.preventDefault();
        $('#btnGuardarPassword').click();
      }
    });
    
    // Limpiar al cerrar el modal
    $('#modalCambiarPassword').on('hidden.bs.modal', function() {
      var form = document.getElementById('formCambiarPassword');
      if (form) form.reset();
      $('#password_strength').hide();
      $('#password_match').hide();
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open').css({'overflow': '', 'padding-right': ''});
    });
    
    console.log('[Cambiar Password] Inicialización completa');
  }
  
  // Inicializar cuando el DOM esté listo
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCambiarPassword);
  } else {
    initCambiarPassword();
  }
})();
</script>
