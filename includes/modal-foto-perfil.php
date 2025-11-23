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
<div class="modal fade" id="modalFotoPerfil" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar Foto de Perfil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formFotoPerfil" enctype="multipart/form-data">
          <div class="text-center mb-3">
            <div class="avatar-lg mx-auto mb-3 position-relative" style="width: 120px; height: 120px;">
              <div id="previewFoto" class="avatar-img rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 120px; height: 120px; font-size: 48px; line-height: 120px;">
                <?php if ($fotoActual): ?>
                  <img src="<?php echo htmlspecialchars($basePath . $fotoActual); ?>" 
                       alt="Foto actual" 
                       class="avatar-img rounded-circle" 
                       style="width: 120px; height: 120px; object-fit: cover;"
                       onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-user\'></i>';">
                <?php else: ?>
                  <i class="fas fa-user"></i>
                <?php endif; ?>
              </div>
            </div>
            <p class="text-muted small">Formatos permitidos: JPG, PNG, GIF (máx. 2MB)</p>
          </div>
          <div class="form-group">
            <label for="foto_perfil_input" class="form-label">Seleccionar Imagen</label>
            <input type="file" 
                   class="form-control" 
                   id="foto_perfil_input" 
                   name="foto_perfil" 
                   accept="image/jpeg,image/jpg,image/png,image/gif"
                   required>
            <div class="form-text">Tamaño máximo: 2MB</div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSubirFoto">Subir Foto</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Preview de la imagen antes de subir
  $('#foto_perfil_input').on('change', function(e) {
    var file = e.target.files[0];
    if (file) {
      var reader = new FileReader();
      reader.onload = function(e) {
        var preview = $('#previewFoto');
        preview.html('<img src="' + e.target.result + '" class="avatar-img rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">');
      };
      reader.readAsDataURL(file);
    }
  });
  
  // Subir foto de perfil
  $('#btnSubirFoto').on('click', function() {
    var form = $('#formFotoPerfil')[0];
    var formData = new FormData(form);
    
    if (!formData.get('foto_perfil')) {
      swal("Error", "Por favor selecciona una imagen", "error");
      return;
    }
    
    // Validar tamaño
    var file = $('#foto_perfil_input')[0].files[0];
    if (file.size > 2 * 1024 * 1024) {
      swal("Error", "El archivo es demasiado grande. Tamaño máximo: 2MB", "error");
      return;
    }
    
    $.ajax({
      url: '<?php echo $basePath; ?>config/upload_foto_perfil.php',
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
        $('#btnSubirFoto').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
      },
      success: function(response) {
        $('#btnSubirFoto').prop('disabled', false).html('Subir Foto');
        if (response.success) {
          swal("¡Éxito!", response.message, "success").then(function() {
            $('#modalFotoPerfil').modal('hide');
            // Recargar la página para actualizar la foto en el header
            location.reload();
          });
        } else {
          swal("Error", response.message, "error");
        }
      },
      error: function(xhr) {
        $('#btnSubirFoto').prop('disabled', false).html('Subir Foto');
        var error = xhr.responseJSON ? xhr.responseJSON.message : 'Error al subir la foto';
        swal("Error", error, "error");
      }
    });
  });
  
  // Limpiar preview al cerrar el modal (restaurar foto actual)
  $('#modalFotoPerfil').on('hidden.bs.modal', function() {
    $('#formFotoPerfil')[0].reset();
    <?php if ($fotoActual): ?>
      $('#previewFoto').html('<img src="<?php echo htmlspecialchars($basePath . $fotoActual); ?>" class="avatar-img rounded-circle" style="width: 120px; height: 120px; object-fit: cover;" onerror="this.parentElement.innerHTML=\'<i class=\\\'fas fa-user\\\'></i>\';">');
    <?php else: ?>
      $('#previewFoto').html('<i class="fas fa-user"></i>');
    <?php endif; ?>
  });
});
</script>

