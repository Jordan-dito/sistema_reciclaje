<?php
/**
 * Funciones de Validación
 * Sistema de Gestión de Reciclaje
 * 
 * Este archivo contiene funciones de validación reutilizables
 * para todo el sistema
 */

/**
 * Valida una cédula ecuatoriana
 * 
 * @param string $cedula Cédula a validar (10 dígitos)
 * @return array ['valid' => bool, 'message' => string]
 */
function validarCedulaEcuatoriana($cedula) {
    // Limpiar la cédula (solo números)
    $cedula = preg_replace('/[^0-9]/', '', $cedula);
    
    // Verificar que tenga exactamente 10 dígitos
    if (strlen($cedula) !== 10) {
        return [
            'valid' => false,
            'message' => 'La cédula debe tener exactamente 10 dígitos'
        ];
    }
    
    // Verificar que no sean todos los dígitos iguales (ej: 1111111111)
    if (preg_match('/^(\d)\1{9}$/', $cedula)) {
        return [
            'valid' => false,
            'message' => 'La cédula no puede tener todos los dígitos iguales'
        ];
    }
    
    // Extraer los primeros 9 dígitos y el dígito verificador
    $digitos = str_split(substr($cedula, 0, 9));
    $verificador = intval(substr($cedula, 9, 1));
    
    // Algoritmo de validación de cédula ecuatoriana
    $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
    $suma = 0;
    
    for ($i = 0; $i < 9; $i++) {
        $producto = intval($digitos[$i]) * $coeficientes[$i];
        // Si el producto es mayor a 9, sumar los dígitos
        if ($producto > 9) {
            $producto = intval($producto / 10) + ($producto % 10);
        }
        $suma += $producto;
    }
    
    // Calcular el dígito verificador
    $residuo = $suma % 10;
    $digitoCalculado = ($residuo === 0) ? 0 : (10 - $residuo);
    
    // Validar
    if ($digitoCalculado === $verificador) {
        return [
            'valid' => true,
            'message' => 'Cédula válida'
        ];
    } else {
        return [
            'valid' => false,
            'message' => 'La cédula no es válida según el algoritmo de verificación'
        ];
    }
}

/**
 * Valida un RUC ecuatoriano
 * 
 * @param string $ruc RUC a validar (13 dígitos)
 * @return array ['valid' => bool, 'message' => string]
 */
function validarRucEcuatoriano($ruc) {
    // Limpiar el RUC (solo números)
    $ruc = preg_replace('/[^0-9]/', '', $ruc);
    
    // Verificar que tenga exactamente 13 dígitos
    if (strlen($ruc) !== 13) {
        return [
            'valid' => false,
            'message' => 'El RUC debe tener exactamente 13 dígitos'
        ];
    }
    
    // Los primeros 2 dígitos deben ser el código de provincia (01-24)
    $provincia = intval(substr($ruc, 0, 2));
    if ($provincia < 1 || $provincia > 24) {
        return [
            'valid' => false,
            'message' => 'El código de provincia del RUC no es válido (debe ser 01-24)'
        ];
    }
    
    // El tercer dígito debe ser 9 para personas jurídicas o 6 para públicas
    $tercerDigito = intval(substr($ruc, 2, 1));
    if ($tercerDigito !== 9 && $tercerDigito !== 6) {
        return [
            'valid' => false,
            'message' => 'El tercer dígito del RUC debe ser 9 (personas jurídicas) o 6 (públicas)'
        ];
    }
    
    // Extraer los primeros 9 dígitos y el dígito verificador
    $digitos = str_split(substr($ruc, 0, 9));
    $verificador = intval(substr($ruc, 9, 1));
    
    // Algoritmo de validación (similar a cédula pero con coeficientes diferentes)
    $coeficientes = [4, 3, 2, 7, 6, 5, 4, 3, 2];
    $suma = 0;
    
    for ($i = 0; $i < 9; $i++) {
        $suma += intval($digitos[$i]) * $coeficientes[$i];
    }
    
    // Calcular el dígito verificador
    $residuo = $suma % 11;
    $digitoCalculado = ($residuo < 2) ? $residuo : (11 - $residuo);
    
    // Validar
    if ($digitoCalculado === $verificador) {
        return [
            'valid' => true,
            'message' => 'RUC válido'
        ];
    } else {
        return [
            'valid' => false,
            'message' => 'El RUC no es válido según el algoritmo de verificación'
        ];
    }
}

/**
 * Valida cédula o RUC según el tipo de documento
 * 
 * @param string $numero Número de documento
 * @param string $tipo Tipo de documento: 'cedula', 'ruc', 'pasaporte', 'otro'
 * @return array ['valid' => bool, 'message' => string]
 */
function validarDocumentoEcuatoriano($numero, $tipo = 'cedula') {
    // Limpiar el número
    $numero = preg_replace('/[^0-9]/', '', $numero);
    
    switch ($tipo) {
        case 'cedula':
            if (strlen($numero) !== 10) {
                return [
                    'valid' => false,
                    'message' => 'La cédula debe tener 10 dígitos'
                ];
            }
            return validarCedulaEcuatoriana($numero);
            
        case 'ruc':
            if (strlen($numero) !== 13) {
                return [
                    'valid' => false,
                    'message' => 'El RUC debe tener 13 dígitos'
                ];
            }
            return validarRucEcuatoriano($numero);
            
        case 'pasaporte':
        case 'otro':
            // Para pasaporte y otros, solo validar que no esté vacío
            if (empty($numero)) {
                return [
                    'valid' => false,
                    'message' => 'El número de documento no puede estar vacío'
                ];
            }
            return [
                'valid' => true,
                'message' => 'Documento válido'
            ];
            
        default:
            return [
                'valid' => false,
                'message' => 'Tipo de documento no válido'
            ];
    }
}

/**
 * Formatea una cédula ecuatoriana con guiones
 * Ejemplo: 1234567890 -> 12.345.678-0
 * 
 * @param string $cedula Cédula sin formato
 * @return string Cédula formateada
 */
function formatearCedula($cedula) {
    $cedula = preg_replace('/[^0-9]/', '', $cedula);
    if (strlen($cedula) === 10) {
        return substr($cedula, 0, 2) . '.' . 
               substr($cedula, 2, 3) . '.' . 
               substr($cedula, 5, 3) . '-' . 
               substr($cedula, 9, 1);
    }
    return $cedula;
}

/**
 * Formatea un RUC ecuatoriano con guiones
 * Ejemplo: 1234567890001 -> 12.345.678-9000-1
 * 
 * @param string $ruc RUC sin formato
 * @return string RUC formateado
 */
function formatearRuc($ruc) {
    $ruc = preg_replace('/[^0-9]/', '', $ruc);
    if (strlen($ruc) === 13) {
        return substr($ruc, 0, 2) . '.' . 
               substr($ruc, 2, 3) . '.' . 
               substr($ruc, 5, 3) . '-' . 
               substr($ruc, 8, 4) . '-' . 
               substr($ruc, 12, 1);
    }
    return $ruc;
}

/**
 * Valida que un campo no contenga solo espacios en blanco
 * 
 * @param string $valor Valor a validar
 * @param string $nombreCampo Nombre del campo para el mensaje de error
 * @return array ['valid' => bool, 'message' => string]
 */
function validarNoSoloEspacios($valor, $nombreCampo = 'Campo') {
    $trimmed = trim($valor);
    if (empty($trimmed)) {
        return [
            'valid' => false,
            'message' => $nombreCampo . ' no puede contener solo espacios en blanco'
        ];
    }
    return [
        'valid' => true,
        'message' => 'Válido'
    ];
}

/**
 * Valida que un campo contenga solo números
 * 
 * @param string $valor Valor a validar
 * @param string $nombreCampo Nombre del campo para el mensaje de error
 * @param bool $permitirDecimales Si permite decimales (default: false)
 * @return array ['valid' => bool, 'message' => string]
 */
function validarSoloNumeros($valor, $nombreCampo = 'Campo', $permitirDecimales = false) {
    $valor = trim($valor);
    if (empty($valor)) {
        return [
            'valid' => false,
            'message' => $nombreCampo . ' no puede estar vacío'
        ];
    }
    
    if ($permitirDecimales) {
        // Permitir números enteros y decimales (con punto o coma)
        if (!preg_match('/^[0-9]+([.,][0-9]+)?$/', $valor)) {
            return [
                'valid' => false,
                'message' => $nombreCampo . ' debe contener solo números' . ($permitirDecimales ? ' (puede incluir decimales)' : '')
            ];
        }
    } else {
        // Solo números enteros
        if (!preg_match('/^[0-9]+$/', $valor)) {
            return [
                'valid' => false,
                'message' => $nombreCampo . ' debe contener solo números'
            ];
        }
    }
    
    return [
        'valid' => true,
        'message' => 'Válido'
    ];
}

/**
 * Valida que un campo contenga solo letras (y espacios)
 * 
 * @param string $valor Valor a validar
 * @param string $nombreCampo Nombre del campo para el mensaje de error
 * @param bool $permitirEspacios Si permite espacios (default: true)
 * @param bool $permitirAcentos Si permite acentos y caracteres especiales del español (default: true)
 * @return array ['valid' => bool, 'message' => string]
 */
function validarSoloLetras($valor, $nombreCampo = 'Campo', $permitirEspacios = true, $permitirAcentos = true) {
    $valor = trim($valor);
    if (empty($valor)) {
        return [
            'valid' => false,
            'message' => $nombreCampo . ' no puede estar vacío'
        ];
    }
    
    // Patrón base: solo letras
    $patron = '/^[a-zA-Z';
    
    if ($permitirAcentos) {
        $patron .= 'áéíóúÁÉÍÓÚñÑüÜ';
    }
    
    if ($permitirEspacios) {
        $patron .= ' ';
    }
    
    $patron .= ']+$/u';
    
    if (!preg_match($patron, $valor)) {
        $mensaje = $nombreCampo . ' debe contener solo letras';
        if ($permitirEspacios) {
            $mensaje .= ' y espacios';
        }
        return [
            'valid' => false,
            'message' => $mensaje
        ];
    }
    
    return [
        'valid' => true,
        'message' => 'Válido'
    ];
}

/**
 * Limpia espacios en blanco al inicio y final de un string
 * 
 * @param string $valor Valor a limpiar
 * @return string Valor limpio
 */
function limpiarEspacios($valor) {
    return trim($valor);
}

/**
 * Limpia y valida un campo de texto
 * 
 * @param string $valor Valor a limpiar y validar
 * @param bool $requerido Si el campo es requerido (default: false)
 * @return array ['valid' => bool, 'value' => string, 'message' => string]
 */
function limpiarYValidarTexto($valor, $requerido = false) {
    $limpio = trim($valor);
    
    if ($requerido && empty($limpio)) {
        return [
            'valid' => false,
            'value' => '',
            'message' => 'Este campo es obligatorio'
        ];
    }
    
    if (!empty($limpio) && preg_match('/^\s+$/', $limpio)) {
        return [
            'valid' => false,
            'value' => '',
            'message' => 'El campo no puede contener solo espacios en blanco'
        ];
    }
    
    return [
        'valid' => true,
        'value' => $limpio,
        'message' => 'Válido'
    ];
}

/**
 * Valida un email
 * 
 * @param string $email Email a validar
 * @return array ['valid' => bool, 'message' => string]
 */
function validarEmail($email) {
    $email = trim($email);
    if (empty($email)) {
        return [
            'valid' => false,
            'message' => 'El email no puede estar vacío'
        ];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'valid' => false,
            'message' => 'El formato del email no es válido'
        ];
    }
    
    return [
        'valid' => true,
        'message' => 'Email válido'
    ];
}

/**
 * Valida un teléfono ecuatoriano
 * 
 * @param string $telefono Teléfono a validar
 * @return array ['valid' => bool, 'message' => string]
 */
function validarTelefonoEcuatoriano($telefono) {
    $telefono = preg_replace('/[^0-9]/', '', $telefono);
    
    // Teléfonos ecuatorianos: 9 dígitos (celular) o 7 dígitos (fijo)
    if (strlen($telefono) === 9) {
        // Celular: debe empezar con 09
        if (substr($telefono, 0, 2) === '09') {
            return [
                'valid' => true,
                'message' => 'Teléfono válido'
            ];
        }
    } elseif (strlen($telefono) === 7) {
        // Fijo: 7 dígitos
        return [
            'valid' => true,
            'message' => 'Teléfono válido'
        ];
    }
    
    return [
        'valid' => false,
        'message' => 'El teléfono debe tener 9 dígitos (celular: 09XXXXXXXX) o 7 dígitos (fijo)'
    ];
}

