# Validación de RUC Ecuatoriano - Documentación

## Implementación Completa

Se ha implementado una validación exhaustiva del RUC ecuatoriano según la normativa del SRI (Servicio de Rentas Internas).

## Reglas de Validación

### 1. Longitud
- **Exactamente 13 dígitos**
- Solo caracteres numéricos (0-9)

### 2. Código de Provincia (Primeros 2 dígitos)
- Rango válido: `01` a `24` (provincias de Ecuador)
- Excepción: `30` para casos especiales (extranjeros)

### 3. Tercer Dígito (Tipo de Contribuyente)

| Dígito | Tipo | Validación |
|--------|------|------------|
| 0-5 | Persona Natural | Primeros 10 dígitos = Cédula válida (Módulo 10) |
| 6 | Entidad Pública | Primeros 8 dígitos + verificador en posición 9 (Módulo 11) |
| 9 | Sociedad Privada | Primeros 9 dígitos + verificador en posición 10 (Módulo 11) |
| 7, 8 | Inválido | No permitido |

### 4. Número de Establecimiento (Últimos 3 dígitos)
- Debe ser mayor o igual a `001`
- No puede ser `000`

## Algoritmos de Validación

### Persona Natural (Tercer dígito 0-5)
Los primeros 10 dígitos deben ser una cédula ecuatoriana válida:

```
Coeficientes: [2, 1, 2, 1, 2, 1, 2, 1, 2]
- Multiplicar cada dígito por su coeficiente
- Si el resultado > 9, sumar sus dígitos
- Sumar todos los productos
- Dígito verificador = (10 - (suma % 10)) % 10
```

### Sociedad Privada (Tercer dígito 9)
```
Coeficientes: [4, 3, 2, 7, 6, 5, 4, 3, 2]
- Aplicar a los primeros 9 dígitos
- suma % 11
- Dígito verificador = (residuo == 0) ? 0 : (11 - residuo)
- Comparar con el 10º dígito
```

### Entidad Pública (Tercer dígito 6)
```
Coeficientes: [3, 2, 7, 6, 5, 4, 3, 2]
- Aplicar a los primeros 8 dígitos
- suma % 11
- Dígito verificador = (residuo == 0) ? 0 : (11 - residuo)
- Comparar con el 9º dígito
```

## Ejemplos de RUCs Válidos

### Persona Natural
```
0930792767001 - Provincia 09 (Guayas), Persona Natural
1710034065001 - Provincia 17 (Pichincha), Persona Natural
0918234567001 - Provincia 09 (Guayas), Persona Natural
```

### Sociedad Privada
```
1790016919001 - Provincia 17 (Pichincha), Sociedad Privada
0992398032001 - Provincia 09 (Guayas), Sociedad Privada
```

### Entidad Pública
```
1768013940001 - Provincia 17 (Pichincha), Entidad Pública
```

### Casos Especiales
```
3050012345001 - Código 30 (Extranjeros)
```

## Uso en el Sistema

### Backend (PHP)
```php
// En api.php
$cedula_ruc = preg_replace('/[^0-9]/', '', $_POST['cedula_ruc']);
$tipo_documento = $_POST['tipo_documento'];

$validacion = validarDocumentoEcuatoriano($cedula_ruc, $tipo_documento);
if (!$validacion['valid']) {
    throw new Exception($validacion['message']);
}
```

### Frontend (JavaScript)
```javascript
// Validación en tiempo real
$('#cedula_ruc').on('input', function() {
    // Solo números
    this.value = this.value.replace(/[^0-9]/g, '');
    
    // Limitar longitud según tipo
    var tipoDoc = $('#tipo_documento').val();
    if (tipoDoc === 'cedula' && this.value.length > 10) {
        this.value = this.value.substring(0, 10);
    } else if (tipoDoc === 'ruc' && this.value.length > 13) {
        this.value = this.value.substring(0, 13);
    }
});
```

## Archivos Modificados

1. **config/validaciones.php**
   - Función `validarRucEcuatoriano()` completamente reescrita
   - Implementa todas las reglas de validación
   - Manejo seguro de errores

2. **proveedores/index.php**
   - Validación JavaScript mejorada
   - Límite de longitud dinámico según tipo de documento
   - Prevención de caracteres no numéricos

3. **proveedores/api.php**
   - Ya estaba usando `validarDocumentoEcuatoriano()`
   - No requiere cambios adicionales

## Archivos de Prueba

- `test_ruc.html` - Interfaz web para probar validación
- `test_ruc_ajax.php` - Endpoint AJAX para pruebas
- `test_ruc_especifico.php` - Prueba del RUC específico de la imagen
- `test_validacion_ruc.php` - Suite completa de pruebas

## Casos de Error Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "El RUC debe tener exactamente 13 dígitos" | Longitud incorrecta | Verificar que sean 13 dígitos |
| "El código de provincia del RUC no es válido" | Provincia fuera de rango | Usar 01-24 o 30 |
| "El tercer dígito del RUC no es válido" | Tipo de contribuyente inválido | Usar 0-5, 6 o 9 |
| "El número de establecimiento debe ser mayor o igual a 001" | Últimos 3 dígitos = 000 | Usar mínimo 001 |
| "El dígito verificador... no es válido" | Error en algoritmo | Verificar cálculo del dígito |

## Compatibilidad

- ✅ PHP 7.0+
- ✅ Compatible con MySQL/MariaDB
- ✅ Validación lado servidor y cliente
- ✅ Mensajes de error descriptivos en español
- ✅ Soporte para todos los tipos de RUC ecuatorianos

## Testing

Para probar la validación, abrir en el navegador:
```
http://localhost/sistema_reciclaje/test_ruc.html
```

Ingresar RUCs de prueba y verificar que la validación funcione correctamente.
