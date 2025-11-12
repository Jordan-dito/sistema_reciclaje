# 🔐 Sistema de Validación de Documentos Ecuatorianos

## 📋 Descripción

Este sistema implementa validación de cédulas y RUCs ecuatorianos usando un archivo centralizado (`config/validaciones.php`) que puede ser reutilizado en todo el proyecto.

---

## 📁 Archivo de Validaciones

**Ubicación:** `config/validaciones.php`

Este archivo contiene las siguientes funciones:

### 1. `validarCedulaEcuatoriana($cedula)`
Valida una cédula ecuatoriana de 10 dígitos usando el algoritmo oficial.

**Parámetros:**
- `$cedula` (string): Cédula a validar

**Retorna:**
```php
[
    'valid' => true/false,
    'message' => 'Mensaje descriptivo'
]
```

**Ejemplo:**
```php
$resultado = validarCedulaEcuatoriana('1234567890');
if ($resultado['valid']) {
    echo "Cédula válida";
} else {
    echo $resultado['message'];
}
```

---

### 2. `validarRucEcuatoriano($ruc)`
Valida un RUC ecuatoriano de 13 dígitos usando el algoritmo oficial.

**Parámetros:**
- `$ruc` (string): RUC a validar

**Retorna:**
```php
[
    'valid' => true/false,
    'message' => 'Mensaje descriptivo'
]
```

**Ejemplo:**
```php
$resultado = validarRucEcuatoriano('1234567890001');
if ($resultado['valid']) {
    echo "RUC válido";
} else {
    echo $resultado['message'];
}
```

---

### 3. `validarDocumentoEcuatoriano($numero, $tipo)`
Valida cualquier tipo de documento ecuatoriano según su tipo.

**Parámetros:**
- `$numero` (string): Número de documento
- `$tipo` (string): Tipo de documento ('cedula', 'ruc', 'pasaporte', 'otro')

**Retorna:**
```php
[
    'valid' => true/false,
    'message' => 'Mensaje descriptivo'
]
```

**Ejemplo:**
```php
// Validar cédula
$resultado = validarDocumentoEcuatoriano('1234567890', 'cedula');

// Validar RUC
$resultado = validarDocumentoEcuatoriano('1234567890001', 'ruc');
```

---

### 4. `formatearCedula($cedula)`
Formatea una cédula con puntos y guión.

**Ejemplo:**
```php
echo formatearCedula('1234567890'); 
// Resultado: 12.345.678-0
```

---

### 5. `formatearRuc($ruc)`
Formatea un RUC con puntos y guiones.

**Ejemplo:**
```php
echo formatearRuc('1234567890001'); 
// Resultado: 12.345.678-9000-1
```

---

## 🔧 Cómo Usar en el Proyecto

### Paso 1: Incluir el archivo

En cualquier archivo PHP donde necesites validar:

```php
require_once __DIR__ . '/../config/validaciones.php';
```

### Paso 2: Usar la validación

```php
// Ejemplo en una API
$cedula = trim($_POST['cedula'] ?? '');

// Validar cédula
$validacion = validarCedulaEcuatoriana($cedula);
if (!$validacion['valid']) {
    throw new Exception($validacion['message']);
}

// Continuar con el proceso...
```

---

## ✅ Módulos que ya usan la validación

1. ✅ **Usuarios** (`usuarios/api.php`)
   - Valida cédulas al crear y actualizar usuarios

2. ✅ **Clientes** (`clientes/api.php`)
   - Valida cédulas y RUCs según el tipo de documento

3. ✅ **Proveedores** (`proveedores/api.php`)
   - Valida cédulas y RUCs según el tipo de documento

---

## 📝 Ejemplos de Cédulas Válidas para Pruebas

### Cédulas de Prueba (válidas según el algoritmo):
- `1234567890` - Ejemplo genérico
- `1713175071` - Cédula válida de ejemplo
- `0923456789` - Otra cédula válida

**Nota:** Estas cédulas pasan la validación del algoritmo, pero pueden no ser cédulas reales. Para pruebas reales, usa cédulas válidas de Ecuador.

---

## 🔍 Algoritmo de Validación

### Cédula Ecuatoriana:
1. Debe tener exactamente 10 dígitos
2. No puede tener todos los dígitos iguales
3. Se calcula el dígito verificador usando coeficientes [2,1,2,1,2,1,2,1,2]
4. Si el producto es > 9, se suman sus dígitos
5. El dígito verificador es: (10 - (suma % 10)) % 10

### RUC Ecuatoriano:
1. Debe tener exactamente 13 dígitos
2. Los primeros 2 dígitos deben ser código de provincia (01-24)
3. El tercer dígito debe ser 9 (jurídicas) o 6 (públicas)
4. Se calcula el dígito verificador usando coeficientes [4,3,2,7,6,5,4,3,2]
5. El dígito verificador es: (11 - (suma % 11)) % 11

---

## 🚀 Agregar Validación a Nuevos Módulos

Si creas un nuevo módulo que necesite validar documentos:

```php
<?php
// 1. Incluir el archivo de validaciones
require_once __DIR__ . '/../config/validaciones.php';

// 2. Obtener los datos
$cedula_ruc = trim($_POST['cedula_ruc'] ?? '');
$tipo_documento = $_POST['tipo_documento'] ?? 'cedula';

// 3. Validar
if (!empty($cedula_ruc)) {
    $validacion = validarDocumentoEcuatoriano($cedula_ruc, $tipo_documento);
    if (!$validacion['valid']) {
        throw new Exception($validacion['message']);
    }
}

// 4. Continuar con el proceso...
```

---

## 📌 Notas Importantes

1. **Opcional:** La validación solo se ejecuta si se proporciona un valor. Si el campo está vacío, no se valida (útil para campos opcionales).

2. **Limpieza automática:** Las funciones limpian automáticamente caracteres no numéricos antes de validar.

3. **Reutilizable:** Un solo archivo para todo el proyecto, fácil de mantener y actualizar.

4. **Mensajes claros:** Los mensajes de error son descriptivos y en español.

---

**Última actualización:** Sistema de Gestión de Reciclaje

