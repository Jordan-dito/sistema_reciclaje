/**
 * Fix para mantener el menú colapsable abierto al navegar
 * Sistema de Gestión de Reciclaje
 */
$(document).ready(function() {
    // Función para mantener el menú abierto basado en el estado inicial
    function maintainMenuState() {
        // Asegurar que los menús que deben estar abiertos permanezcan abiertos
        $('.collapse.show').each(function() {
            var $navItem = $(this).closest('.nav-item');
            $navItem.addClass('submenu');
            
            // Asegurar que el enlace no tenga la clase 'collapsed'
            $navItem.find('> a[data-bs-toggle="collapse"]').removeClass('collapsed').attr('aria-expanded', 'true');
        });
    }
    
    // Ejecutar inmediatamente y después de un pequeño delay
    maintainMenuState();
    setTimeout(maintainMenuState, 100);
    setTimeout(maintainMenuState, 500);
    
    // Prevenir que el menú se cierre al hacer click en enlaces del submenú
    $('.nav-collapse a').on('click', function(e) {
        var $parentMenu = $(this).closest('.collapse');
        var $parentNavItem = $parentMenu.closest('.nav-item');
        
        // Si el menú está abierto, mantenerlo abierto
        if ($parentMenu.hasClass('show')) {
            $parentNavItem.addClass('submenu');
            $parentNavItem.find('> a[data-bs-toggle="collapse"]').removeClass('collapsed').attr('aria-expanded', 'true');
        }
    });
    
    // Sobrescribir el comportamiento del toggle para mantener el estado
    $('.nav-item > a[data-bs-toggle="collapse"]').off('click').on('click', function(e) {
        var $navItem = $(this).closest('.nav-item');
        var $collapse = $navItem.find('.collapse');
        var isCurrentlyOpen = $collapse.hasClass('show');
        
        // Si el menú está abierto y haces click, permitir que se cierre
        // Si está cerrado, abrirlo
        if (isCurrentlyOpen) {
            // Permitir que Bootstrap cierre el menú
            // Pero restaurar el estado después de la animación si es necesario
            setTimeout(function() {
                // Si el menú debería estar abierto (basado en la ruta actual), restaurarlo
                if ($collapse.hasClass('show')) {
                    $navItem.addClass('submenu');
                } else {
                    $navItem.removeClass('submenu');
                }
            }, 350);
        } else {
            // El menú se abrirá, asegurar la clase submenu
            $navItem.addClass('submenu');
        }
    });
    
    // Observar cambios en el DOM para mantener el estado
    var observer = new MutationObserver(function(mutations) {
        maintainMenuState();
    });
    
    // Observar cambios en los menús colapsables
    $('.collapse').each(function() {
        observer.observe(this, {
            attributes: true,
            attributeFilter: ['class']
        });
    });
});

