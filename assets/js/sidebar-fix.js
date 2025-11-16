/**
 * Fix para mantener el menú colapsable visible y funcionando correctamente
 * Sistema de Gestión de Reciclaje
 */
$(document).ready(function() {
    // Asegurar que todos los menús colapsables estén siempre visibles en el DOM
    // No permitir que se oculten completamente
    
    // Función para mantener el estado correcto de los menús
    function maintainMenuState() {
        // Asegurar que los menús que deben estar abiertos permanezcan abiertos
        $('.collapse.show').each(function() {
            var $navItem = $(this).closest('.nav-item');
            $navItem.addClass('submenu');
            
            // Asegurar que el enlace no tenga la clase 'collapsed'
            var $toggleLink = $navItem.find('> a[data-bs-toggle="collapse"]');
            $toggleLink.removeClass('collapsed').attr('aria-expanded', 'true');
        });
        
        // Asegurar que los menús colapsables siempre tengan la clase nav-item visible
        $('.nav-item.submenu').each(function() {
            var $navItem = $(this);
            // Asegurar que el elemento esté visible
            if ($navItem.is(':hidden')) {
                $navItem.show();
            }
        });
    }
    
    // Ejecutar múltiples veces para asegurar que se aplique
    maintainMenuState();
    setTimeout(maintainMenuState, 50);
    setTimeout(maintainMenuState, 100);
    setTimeout(maintainMenuState, 300);
    setTimeout(maintainMenuState, 500);
    
    // Prevenir que el menú se cierre al hacer click en enlaces del submenú
    $(document).on('click', '.nav-collapse a', function(e) {
        var $parentMenu = $(this).closest('.collapse');
        var $parentNavItem = $parentMenu.closest('.nav-item');
        
        // Asegurar que el nav-item siempre esté visible
        $parentNavItem.show();
        
        // Si el menú está abierto, mantenerlo abierto
        if ($parentMenu.hasClass('show')) {
            $parentNavItem.addClass('submenu');
            var $toggleLink = $parentNavItem.find('> a[data-bs-toggle="collapse"]');
            $toggleLink.removeClass('collapsed').attr('aria-expanded', 'true');
        }
    });
    
    // Mejorar el comportamiento del toggle del menú principal
    $(document).on('click', '.nav-item > a[data-bs-toggle="collapse"]', function(e) {
        var $navItem = $(this).closest('.nav-item');
        var $collapse = $navItem.find('.collapse');
        var isCurrentlyOpen = $collapse.hasClass('show');
        
        // Asegurar que el nav-item siempre esté visible
        $navItem.show();
        
        // Si el menú está abierto y haces click, permitir que se cierre normalmente
        // Si está cerrado, abrirlo
        if (isCurrentlyOpen) {
            // Permitir que Bootstrap maneje el cierre normalmente
            setTimeout(function() {
                if ($collapse.hasClass('show')) {
                    $navItem.addClass('submenu');
                } else {
                    // Menú cerrado, pero mantener visible
                    $navItem.removeClass('submenu');
                }
                // Asegurar visibilidad
                $navItem.show();
            }, 350);
        } else {
            // El menú se abrirá, asegurar la clase submenu
            $navItem.addClass('submenu');
            $navItem.show();
        }
    });
    
    // Observar cambios en el DOM para mantener el estado
    var observer = new MutationObserver(function(mutations) {
        maintainMenuState();
        
        // Asegurar que los nav-items siempre estén visibles
        $('.nav-item.submenu').each(function() {
            $(this).show();
        });
    });
    
    // Observar cambios en los menús colapsables y en los nav-items
    $('.collapse, .nav-item.submenu').each(function() {
        observer.observe(this, {
            attributes: true,
            attributeFilter: ['class', 'style'],
            childList: false,
            subtree: false
        });
    });
    
    // También observar el contenedor del sidebar
    var $sidebar = $('.sidebar-content, .sidebar-wrapper');
    if ($sidebar.length > 0) {
        observer.observe($sidebar[0], {
            childList: true,
            subtree: true,
            attributes: false
        });
    }
    
    // Forzar visibilidad de los menús al cargar
    $(window).on('load', function() {
        maintainMenuState();
        $('.nav-item.submenu').show();
    });
});

