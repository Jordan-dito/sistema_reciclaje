<?php
/**
 * Modal para cambiar contraseña del usuario
 * Sistema de Gestión de Reciclaje
 */
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
              <input type="password" class="form-control" id="password_actual" name="password_actual" placeholder="Ingresa tu contraseña actual" required autocomplete="current-password">
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
              <input type="password" class="form-control" id="password_nueva" name="password_nueva" placeholder="Ingresa tu nueva contraseña" minlength="8" required autocomplete="new-password">
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
              <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" placeholder="Confirma tu nueva contraseña" minlength="8" required autocomplete="new-password">
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
window.togglePasswordVisibility = function(inputId, button) {
  var input = document.getElementById(inputId);
  var icon = button.querySelector('i');
  if (input.type === 'password') { input.type = 'text'; icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); } else { input.type = 'password'; icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
};

window.cerrarModalCambiarPassword = function() {
  var modalElement = document.getElementById('modalCambiarPassword');
  if (modalElement) {
    var bsModal = bootstrap.Modal.getInstance(modalElement);
    if (bsModal) bsModal.hide();
  }
  if (typeof jQuery !== 'undefined') {
    jQuery('#modalCambiarPassword').modal('hide');
    setTimeout(function() { jQuery('.modal-backdrop').remove(); jQuery('body').removeClass('modal-open').css({'overflow': '', 'padding-right': ''}); }, 100);
  }
};

window.abrirModalCambiarPassword = function() {
  var modalElement = document.getElementById('modalCambiarPassword');
  if (!modalElement) return;
  if (typeof jQuery !== 'undefined') {
  // Eliminar force-fully todos los backdrops y clases relacionadas
  jQuery('.modal').modal('hide');
  jQuery('.modal-backdrop').remove();
  jQuery('body').removeClass('modal-open').css({'overflow': '', 'padding-right': ''});
}
setTimeout(function() {
  var modal = new bootstrap.Modal(modalElement, { backdrop: 'static', keyboard: true });
  modal.show();
  setTimeout(function() { var firstInput = document.getElementById('password_actual'); if (firstInput) firstInput.focus(); }, 300);
}, 100);
};

(function() {
  function initPasswordLogic() {
    if (typeof jQuery === 'undefined') { setTimeout(initPasswordLogic, 100); return; }
    var $ = jQuery;
    $(document).on('input', '#password_nueva', function() {
      var password = $(this).val();
      var strengthEl = $('#password_strength');
      var progressBar = strengthEl.find('.progress-bar');
      var textEl = $('#password_strength_text');
      if (password.length === 0) { strengthEl.hide(); return; }
      strengthEl.show();
      var strength = 0;
      if (password.length >= 8) strength += 25;
      if (password.length >= 12) strength += 15;
      if (/[A-Z]/.test(password)) strength += 20;
      if (/[a-z]/.test(password)) strength += 15;
      if (/[0-9]/.test(password)) strength += 15;
      if (/[^A-Za-z0-9]/.test(password)) strength += 10;
      var color = strength < 40 ? 'bg-danger' : (strength < 70 ? 'bg-warning' : 'bg-success');
      progressBar.css('width', strength + '%').removeClass('bg-danger bg-warning bg-success').addClass(color);
      textEl.text('Fortaleza: ' + (strength < 40 ? 'Débil' : (strength < 70 ? 'Media' : 'Fuerte')));
    });
    $(document).on('click', '#btnGuardarPassword', function(e) {
      e.preventDefault();
      var pA = $('#password_actual').val().trim();
      var pN = $('#password_nueva').val().trim();
      var pC = $('#password_confirmar').val().trim();
      if (!pA || !pN || !pC) { swal('Error', 'Todos los campos son obligatorios', 'error'); return; }
      if (pN !== pC) { swal('Error', 'Las contraseñas no coinciden', 'error'); return; }
      var btn = $(this);
      $.ajax({
        url: '<?php echo $basePath; ?>config/cambiar_password.php',
        method: 'POST',
        data: { password_actual: pA, password_nueva: pN, password_confirmar: pC },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            swal('Éxito', response.message, 'success').then(function() { cerrarModalCambiarPassword(); });
          } else { swal('Error', response.message, 'error'); }
        }
      });
    });
  }
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initPasswordLogic); } else { initPasswordLogic(); }
})();
</script>