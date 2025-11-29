<?php
/**
 * Scripts comunes del footer
 * Incluir este archivo después de kaiadmin.min.js y setting-demo.js
 */
$basePath = $basePath ?? '';
// Asegurar que la ruta termine con / si no está vacía
if (!empty($basePath) && substr($basePath, -1) !== '/') {
    $basePath .= '/';
}
?>
<script src="<?php echo $basePath; ?>assets/js/sidebar-fix.js"></script>

