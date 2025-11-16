/**
 * Validaciones del lado del cliente
 * Sistema de Gestión de Reciclaje
 * 
 * Este archivo contiene funciones de validación JavaScript
 * para validar formularios antes de enviarlos al servidor
 */

/**
 * Valida una cédula ecuatoriana
 * @param {string} cedula - Cédula a validar (10 dígitos)
 * @returns {object} {valid: boolean, message: string}
 */
function validarCedulaEcuatoriana(cedula) {
    // Limpiar la cédula (solo números)
    cedula = cedula.replace(/[^0-9]/g, '');
    
    // Verificar que tenga exactamente 10 dígitos
    if (cedula.length !== 10) {
        return {
            valid: false,
            message: 'La cédula debe tener exactamente 10 dígitos'
        };
    }
    
    // Verificar que no sean todos los dígitos iguales
    if (/^(\d)\1{9}$/.test(cedula)) {
        return {
            valid: false,
            message: 'La cédula no puede tener todos los dígitos iguales'
        };
    }
    
    // Extraer los primeros 9 dígitos y el dígito verificador
    var digitos = cedula.substring(0, 9).split('').map(Number);
    var verificador = parseInt(cedula.charAt(9));
    
    // Algoritmo de validación de cédula ecuatoriana
    var coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
    var suma = 0;
    
    for (var i = 0; i < 9; i++) {
        var producto = digitos[i] * coeficientes[i];
        // Si el producto es mayor a 9, sumar los dígitos
        if (producto > 9) {
            producto = Math.floor(producto / 10) + (producto % 10);
        }
        suma += producto;
    }
    
    // Calcular el dígito verificador
    var residuo = suma % 10;
    var digitoCalculado = (residuo === 0) ? 0 : (10 - residuo);
    
    // Validar
    if (digitoCalculado === verificador) {
        return {
            valid: true,
            message: 'Cédula válida'
        };
    } else {
        return {
            valid: false,
            message: 'La cédula no es válida según el algoritmo de verificación'
        };
    }
}

/**
 * Valida un RUC ecuatoriano
 * @param {string} ruc - RUC a validar (13 dígitos)
 * @returns {object} {valid: boolean, message: string}
 */
function validarRucEcuatoriano(ruc) {
    // Limpiar el RUC (solo números)
    ruc = ruc.replace(/[^0-9]/g, '');
    
    // Verificar que tenga exactamente 13 dígitos
    if (ruc.length !== 13) {
        return {
            valid: false,
            message: 'El RUC debe tener exactamente 13 dígitos'
        };
    }
    
    // Los primeros 2 dígitos deben ser el código de provincia (01-24)
    var provincia = parseInt(ruc.substring(0, 2));
    if (provincia < 1 || provincia > 24) {
        return {
            valid: false,
            message: 'El código de provincia del RUC no es válido (debe ser 01-24)'
        };
    }
    
    // El tercer dígito debe ser 9 para personas jurídicas o 6 para públicas
    var tercerDigito = parseInt(ruc.charAt(2));
    if (tercerDigito !== 9 && tercerDigito !== 6) {
        return {
            valid: false,
            message: 'El tercer dígito del RUC debe ser 9 (personas jurídicas) o 6 (públicas)'
        };
    }
    
    // Extraer los primeros 9 dígitos y el dígito verificador
    var digitos = ruc.substring(0, 9).split('').map(Number);
    var verificador = parseInt(ruc.charAt(9));
    
    // Algoritmo de validación
    var coeficientes = [4, 3, 2, 7, 6, 5, 4, 3, 2];
    var suma = 0;
    
    for (var i = 0; i < 9; i++) {
        suma += digitos[i] * coeficientes[i];
    }
    
    // Calcular el dígito verificador
    var residuo = suma % 11;
    var digitoCalculado = (residuo < 2) ? residuo : (11 - residuo);
    
    // Validar
    if (digitoCalculado === verificador) {
        return {
            valid: true,
            message: 'RUC válido'
        };
    } else {
        return {
            valid: false,
            message: 'El RUC no es válido según el algoritmo de verificación'
        };
    }
}

/**
 * Valida que un campo no contenga solo espacios en blanco
 * @param {string} valor - Valor a validar
 * @param {string} nombreCampo - Nombre del campo para el mensaje
 * @returns {object} {valid: boolean, message: string}
 */
function validarNoSoloEspacios(valor, nombreCampo) {
    nombreCampo = nombreCampo || 'Campo';
    var trimmed = valor.trim();
    if (!trimmed) {
        return {
            valid: false,
            message: nombreCampo + ' no puede contener solo espacios en blanco'
        };
    }
    return {
        valid: true,
        message: 'Válido'
    };
}

/**
 * Valida que un campo contenga solo números
 * @param {string} valor - Valor a validar
 * @param {string} nombreCampo - Nombre del campo para el mensaje
 * @param {boolean} permitirDecimales - Si permite decimales (default: false)
 * @returns {object} {valid: boolean, message: string}
 */
function validarSoloNumeros(valor, nombreCampo, permitirDecimales) {
    nombreCampo = nombreCampo || 'Campo';
    permitirDecimales = permitirDecimales || false;
    var trimmed = valor.trim();
    
    if (!trimmed) {
        return {
            valid: false,
            message: nombreCampo + ' no puede estar vacío'
        };
    }
    
    if (permitirDecimales) {
        // Permitir números enteros y decimales (con punto o coma)
        if (!/^[0-9]+([.,][0-9]+)?$/.test(trimmed)) {
            return {
                valid: false,
                message: nombreCampo + ' debe contener solo números' + (permitirDecimales ? ' (puede incluir decimales)' : '')
            };
        }
    } else {
        // Solo números enteros
        if (!/^[0-9]+$/.test(trimmed)) {
            return {
                valid: false,
                message: nombreCampo + ' debe contener solo números'
            };
        }
    }
    
    return {
        valid: true,
        message: 'Válido'
    };
}

/**
 * Valida que un campo contenga solo letras (y espacios)
 * @param {string} valor - Valor a validar
 * @param {string} nombreCampo - Nombre del campo para el mensaje
 * @param {boolean} permitirEspacios - Si permite espacios (default: true)
 * @returns {object} {valid: boolean, message: string}
 */
function validarSoloLetras(valor, nombreCampo, permitirEspacios) {
    nombreCampo = nombreCampo || 'Campo';
    permitirEspacios = permitirEspacios !== false; // default: true
    var trimmed = valor.trim();
    
    if (!trimmed) {
        return {
            valid: false,
            message: nombreCampo + ' no puede estar vacío'
        };
    }
    
    // Patrón: solo letras, acentos y espacios (si se permiten)
    var patron = permitirEspacios 
        ? /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u
        : /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]+$/u;
    
    if (!patron.test(trimmed)) {
        var mensaje = nombreCampo + ' debe contener solo letras';
        if (permitirEspacios) {
            mensaje += ' y espacios';
        }
        return {
            valid: false,
            message: mensaje
        };
    }
    
    return {
        valid: true,
        message: 'Válido'
    };
}

/**
 * Valida un email
 * @param {string} email - Email a validar
 * @returns {object} {valid: boolean, message: string}
 */
function validarEmail(email) {
    email = email.trim();
    if (!email) {
        return {
            valid: false,
            message: 'El email no puede estar vacío'
        };
    }
    
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        return {
            valid: false,
            message: 'El formato del email no es válido'
        };
    }
    
    return {
        valid: true,
        message: 'Email válido'
    };
}

/**
 * Valida un teléfono ecuatoriano
 * @param {string} telefono - Teléfono a validar
 * @returns {object} {valid: boolean, message: string}
 */
function validarTelefonoEcuatoriano(telefono) {
    telefono = telefono.replace(/[^0-9]/g, '');
    
    // Teléfonos ecuatorianos: 9 dígitos (celular) o 7 dígitos (fijo)
    if (telefono.length === 9) {
        // Celular: debe empezar con 09
        if (telefono.substring(0, 2) === '09') {
            return {
                valid: true,
                message: 'Teléfono válido'
            };
        }
    } else if (telefono.length === 7) {
        // Fijo: 7 dígitos
        return {
            valid: true,
            message: 'Teléfono válido'
        };
    }
    
    return {
        valid: false,
        message: 'El teléfono debe tener 9 dígitos (celular: 09XXXXXXXX) o 7 dígitos (fijo)'
    };
}

/**
 * Limpia espacios en blanco de un valor
 * @param {string} valor - Valor a limpiar
 * @returns {string} Valor limpio
 */
function limpiarEspacios(valor) {
    return valor.trim();
}

/**
 * Aplica validaciones en tiempo real a un campo de formulario
 * @param {jQuery} $campo - Campo jQuery a validar
 * @param {string} tipoValidacion - Tipo de validación: 'texto', 'numero', 'soloNumeros', 'soloLetras', 'email', 'telefono', 'cedula', 'ruc'
 * @param {object} opciones - Opciones adicionales de validación
 */
function aplicarValidacionEnTiempoReal($campo, tipoValidacion, opciones) {
    opciones = opciones || {};
    var nombreCampo = opciones.nombreCampo || $campo.attr('name') || 'Campo';
    var mostrarMensaje = opciones.mostrarMensaje !== false; // default: true
    
    $campo.on('blur', function() {
        var valor = $(this).val();
        var resultado = null;
        
        switch (tipoValidacion) {
            case 'texto':
                resultado = validarNoSoloEspacios(valor, nombreCampo);
                break;
            case 'numero':
                resultado = validarSoloNumeros(valor, nombreCampo, opciones.permitirDecimales);
                break;
            case 'soloNumeros':
                resultado = validarSoloNumeros(valor, nombreCampo, false);
                break;
            case 'soloLetras':
                resultado = validarSoloLetras(valor, nombreCampo, opciones.permitirEspacios);
                break;
            case 'email':
                resultado = validarEmail(valor);
                break;
            case 'telefono':
                resultado = validarTelefonoEcuatoriano(valor);
                break;
            case 'cedula':
                resultado = validarCedulaEcuatoriana(valor);
                break;
            case 'ruc':
                resultado = validarRucEcuatoriano(valor);
                break;
        }
        
        if (resultado) {
            // Remover clases anteriores
            $campo.removeClass('is-valid is-invalid');
            
            if (resultado.valid) {
                $campo.addClass('is-valid');
                if (mostrarMensaje) {
                    $campo.siblings('.invalid-feedback').remove();
                }
            } else {
                $campo.addClass('is-invalid');
                if (mostrarMensaje) {
                    $campo.siblings('.invalid-feedback').remove();
                    $campo.after('<div class="invalid-feedback">' + resultado.message + '</div>');
                }
            }
        }
    });
    
    // Limpiar espacios en blanco al perder el foco
    if (tipoValidacion === 'texto' || tipoValidacion === 'soloLetras') {
        $campo.on('blur', function() {
            $(this).val($(this).val().trim());
        });
    }
}

/**
 * Valida un formulario completo antes de enviarlo
 * @param {jQuery} $form - Formulario jQuery a validar
 * @param {object} reglas - Objeto con reglas de validación por campo
 * @returns {object} {valid: boolean, errors: array}
 */
function validarFormulario($form, reglas) {
    var valido = true;
    var errores = [];
    
    $form.find('input, textarea, select').each(function() {
        var $campo = $(this);
        var nombre = $campo.attr('name');
        var valor = $campo.val();
        
        if (reglas[nombre]) {
            var regla = reglas[nombre];
            var resultado = null;
            
            switch (regla.tipo) {
                case 'texto':
                    resultado = validarNoSoloEspacios(valor, regla.nombreCampo || nombre);
                    break;
                case 'numero':
                    resultado = validarSoloNumeros(valor, regla.nombreCampo || nombre, regla.permitirDecimales);
                    break;
                case 'soloNumeros':
                    resultado = validarSoloNumeros(valor, regla.nombreCampo || nombre, false);
                    break;
                case 'soloLetras':
                    resultado = validarSoloLetras(valor, regla.nombreCampo || nombre, regla.permitirEspacios);
                    break;
                case 'email':
                    resultado = validarEmail(valor);
                    break;
                case 'telefono':
                    resultado = validarTelefonoEcuatoriano(valor);
                    break;
                case 'cedula':
                    resultado = validarCedulaEcuatoriana(valor);
                    break;
                case 'ruc':
                    resultado = validarRucEcuatoriano(valor);
                    break;
            }
            
            if (resultado && !resultado.valid) {
                valido = false;
                errores.push({
                    campo: nombre,
                    mensaje: resultado.message
                });
                $campo.addClass('is-invalid');
                $campo.siblings('.invalid-feedback').remove();
                $campo.after('<div class="invalid-feedback">' + resultado.message + '</div>');
            } else if (resultado && resultado.valid) {
                $campo.removeClass('is-invalid').addClass('is-valid');
            }
        }
    });
    
    return {
        valid: valido,
        errors: errores
    };
}

