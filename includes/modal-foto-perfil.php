<?php
/**
 * Modal para cambiar foto de perfil
 * Sistema de Gestión de Reciclaje
 */
$basePath = isset($basePath) ? rtrim($basePath, '/') : '';
$basePath = $basePath !== '' ? $basePath . '/' : '';
?>
<div class="modal fade" id="modalFotoPerfil" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title mb-0"><i class="fas fa-camera me-2"></i> Cambiar Foto de Perfil</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formFotoPerfil" enctype="multipart/form-data">
          <div class="form-group text-center mb-3">
            <label class="form-label fw-bold">Foto Actual</label>
            <div class="avatar avatar-xl mb-3">
              <img src="<?php echo htmlspecialchars($basePath); ?>assets/img/default-avatar.png" 
                   alt="Foto de Perfil" 
                   class="avatar-img rounded-circle" 
                   id="currentFotoPerfil" 
                   style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e0e0e0;"
                   onerror="this.src='<?php echo htmlspecialchars($basePath); ?>assets/img/default-avatar.png';">
            </div>
            <input type="file" id="nueva_foto_perfil" name="nueva_foto_perfil" class="form-control" accept="image/*">
          </div>
          <div class="alert alert-info py-2 mb-0">
            <i class="fas fa-info-circle me-2"></i>
            <small>La imagen debe ser cuadrada (ej. 200x200px) para una mejor visualización. Tamaño máximo 2MB.</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarFoto">
          <i class="fas fa-save me-2"></i> Guardar Foto
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// Lógica para foto de perfil
(function() {
  function initFotoPerfilLogic() {
    if (typeof jQuery === 'undefined') {
      setTimeout(initFotoPerfilLogic, 100); return;
    }
    var $ = jQuery;
    
    window.abrirModalFotoPerfil = function() {
      var modalElement = document.getElementById('modalFotoPerfil');
      if (!modalElement) return;
      if (typeof jQuery !== 'undefined') {
        jQuery('.modal').modal('hide');
        jQuery('.modal-backdrop').remove();
        jQuery('body').removeClass('modal-open');
      }
      setTimeout(function() {
        var modal = new bootstrap.Modal(modalElement, { backdrop: 'static', keyboard: false });
        modal.show();
        var currentFotoUrl = $('.profile-pic .avatar-img').attr('src');
        $('#currentFotoPerfil').attr('src', currentFotoUrl);
      }, 100);
    };

    $(document).on('click', '#btnGuardarFoto', function(e) {
      e.preventDefault();
      var fileInput = $('#nueva_foto_perfil')[0];
      if (fileInput.files.length === 0) {
        swal('Advertencia', 'Por favor, selecciona una nueva foto de perfil', 'warning');
        return;
      }
      var file = fileInput.files[0];
      var formData = new FormData();
      formData.append('foto_perfil', file);
      formData.append('action', 'upload_foto_perfil');
      
      var btn = $(this); var originalText = btn.html();
      $.ajax({
        url: '<?php echo $basePath; ?>config/upload_foto_perfil.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function() { btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Subiendo...'); },
        success: function(response) {
          btn.prop('disabled', false).html(originalText);
          if (response.success) {
            swal({ title: '¡Éxito!', text: response.message, icon: 'success', button: 'Aceptar' }).then(function() {
              location.reload();
            });
          } else {
            swal('Error', response.message || 'Error al subir la foto de perfil', 'error');
          }
        },
        error: function() {
          btn.prop('disabled', false).html(originalText);
          swal('Error', 'Error al procesar la solicitud', 'error');
        }
      });
    });
  }
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initFotoPerfilLogic); } else { initFotoPerfilLogic(); }
})();
</script>