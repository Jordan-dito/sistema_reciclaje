# Guía de integración: API de Reportes en Flutter

## Sistema de Gestión de Reciclaje

Documentación para el equipo de frontend Flutter sobre el uso del endpoint de reportes.

---

## 1. URL Base

```
https://hermanosyanez.alwaysdata.net
```

**Endpoint completo:**
```
https://hermanosyanez.alwaysdata.net/reportes/api.php
```

---

## 2. Autenticación

La API requiere identificación del usuario. **Es obligatorio enviar `usuario_id`** en cada petición (Flutter no usa sesiones de cookies).

### Cómo obtener `usuario_id`

Después del login (`config/login.php`), el backend devuelve:

```json
{
  "success": true,
  "message": "Inicio de sesión exitoso",
  "usuario": {
    "id": 5,
    "nombre": "Juan Pérez",
    "email": "juan@ejemplo.com",
    ...
  }
}
```

**Guarda `usuario.id`** en tu estado/localStorage y úsalo en todas las llamadas a reportes.

### Formas de enviar `usuario_id`

| Método | Ejemplo |
|--------|---------|
| GET (query) | `?usuario_id=5` |
| GET (alternativo) | `?id=5` |
| POST (form) | `usuario_id=5` |
| POST (JSON) | `{"usuario_id": 5}` |

---

## 3. Endpoint principal

### Action: `vista_previa`

**Método:** `GET` (recomendado) o `POST`

**Parámetros:**

| Parámetro | Tipo | Obligatorio | Descripción |
|-----------|------|-------------|-------------|
| `action` | string | Sí | Siempre: `vista_previa` |
| `tipo` | string | Sí | Tipo de reporte (ver tabla abajo) |
| `fecha_desde` | date | Depende* | Formato: `YYYY-MM-DD` |
| `fecha_hasta` | date | Depende* | Formato: `YYYY-MM-DD` |
| `usuario_id` | int | Sí | ID del usuario logueado |
| `sucursal_id` | int | No | Filtrar por sucursal |
| `rol_id` | int | No | Solo reporte `usuarios` |
| `material` | string | No | Filtrar por nombre de material |

\* **Fechas obligatorias** para: `inventarios`, `compras`, `ventas`, `sucursales`, `usuarios`  
\* **Fechas NO requeridas** para: `productos`, `materiales`

---

## 4. Tipos de reporte

| `tipo` | Nombre | Requiere fechas |
|--------|--------|-----------------|
| `inventarios` | Reporte de Inventarios | Sí |
| `compras` | Reporte de Compras | Sí |
| `ventas` | Reporte de Ventas | Sí |
| `productos` | Reporte de Productos | No |
| `materiales` | Reporte de Materiales por Categoría | No |
| `sucursales` | Reporte de Sucursales | Sí |
| `usuarios` | Reporte de Usuarios por Rol | Sí |

---

## 5. Respuesta de la API

### Estructura exitosa

```json
{
  "success": true,
  "html": "<div class=\"table-responsive\">...",
  "tieneDatos": true,
  "datos": [ ... ]
}
```

| Campo | Descripción |
|-------|-------------|
| `success` | `true` si la petición fue correcta |
| `html` | HTML pre-renderizado (opcional, para WebView) |
| `tieneDatos` | `true` si hay registros |
| `datos` | Array de objetos con los registros del reporte |

### Estructura de error

```json
{
  "success": false,
  "message": "Las fechas son obligatorias para este tipo de reporte",
  "type": "Validation Error"
}
```

```json
{
  "success": false,
  "message": "No autenticado. Inicia sesión o proporciona usuario_id.",
  "type": "Authentication Error"
}
```

---

## 6. URLs de ejemplo (cURL/Postman)

```bash
# Compras (con fechas)
https://hermanosyanez.alwaysdata.net/reportes/api.php?action=vista_previa&tipo=compras&fecha_desde=2025-01-01&fecha_hasta=2025-12-31&usuario_id=1

# Ventas
https://hermanosyanez.alwaysdata.net/reportes/api.php?action=vista_previa&tipo=ventas&fecha_desde=2025-01-01&fecha_hasta=2025-12-31&usuario_id=1

# Inventarios
https://hermanosyanez.alwaysdata.net/reportes/api.php?action=vista_previa&tipo=inventarios&fecha_desde=2025-01-01&fecha_hasta=2025-12-31&usuario_id=1

# Productos (sin fechas)
https://hermanosyanez.alwaysdata.net/reportes/api.php?action=vista_previa&tipo=productos&usuario_id=1

# Materiales (sin fechas)
https://hermanosyanez.alwaysdata.net/reportes/api.php?action=vista_previa&tipo=materiales&usuario_id=1

# Con filtro de sucursal
https://hermanosyanez.alwaysdata.net/reportes/api.php?action=vista_previa&tipo=compras&fecha_desde=2025-01-01&fecha_hasta=2025-12-31&usuario_id=1&sucursal_id=2
```

---

## 7. Diseño de la pantalla en Flutter

### 7.1 Estructura recomendada

```
┌─────────────────────────────────────────────┐
│  ← Reportes                                 │
├─────────────────────────────────────────────┤
│                                             │
│  Tipo de reporte *                          │
│  ┌─────────────────────────────────────┐   │
│  │ Seleccione un reporte            ▼ │   │
│  └─────────────────────────────────────┘   │
│                                             │
│  Fecha desde *     Fecha hasta *            │
│  ┌──────────┐      ┌──────────┐             │
│  │ 01/01/25 │      │ 31/12/25 │             │
│  └──────────┘      └──────────┘             │
│                                             │
│  Sucursal (opcional)                        │
│  ┌─────────────────────────────────────┐   │
│  │ Todas las sucursales             ▼ │   │
│  └─────────────────────────────────────┘   │
│                                             │
│  [ Si tipo=usuarios: Rol (opcional) ]       │
│  [ Si aplica: Material (opcional) ]        │
│                                             │
│  ┌─────────────────────────────────────┐   │
│  │         Generar reporte             │   │
│  └─────────────────────────────────────┘   │
│                                             │
├─────────────────────────────────────────────┤
│                                             │
│  RESULTADO                                  │
│  ┌─────────────────────────────────────┐   │
│  │ Tabla/Lista con datos               │   │
│  │ o "No hay datos"                    │   │
│  └─────────────────────────────────────┘   │
│                                             │
│  [ Exportar PDF ]  (si lo implementas)     │
│                                             │
└─────────────────────────────────────────────┘
```

### 7.2 Comportamiento de filtros

- **Tipo de reporte:** Dropdown con las 7 opciones.
- **Fechas:** Mostrar siempre. Si el tipo es `productos` o `materiales`, deshabilitarlas o ocultarlas.
- **Sucursal:** Cargar desde `sucursales/api.php?action=activas`. Mostrar "Todas" por defecto.
- **Rol:** Solo visible cuando `tipo == usuarios`. Cargar desde `roles/api.php`.
- **Material:** Opcional para reportes que lo soporten. Puede ser un dropdown o buscador.

### 7.3 Visualización de datos

Usa el array `datos` de la respuesta:

- **Lista/tabla:** `DataTable`, `ListView`, o `PaginatedDataTable` según cantidad de registros.
- **Sin datos:** Mensaje amigable cuando `tieneDatos == false`.
- **Cargando:** Indicador mientras se hace la petición.
- **Errores:** SnackBar o diálogo con `message` del JSON de error.

---

## 8. Ejemplo de código Dart

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class ReportesService {
  static const String baseUrl = 'https://hermanosyanez.alwaysdata.net';
  final int usuarioId;

  ReportesService({required this.usuarioId});

  Future<Map<String, dynamic>> obtenerReporte({
    required String tipo,
    String? fechaDesde,
    String? fechaHasta,
    int? sucursalId,
    int? rolId,
    String? material,
  }) async {
    var uri = Uri.parse('$baseUrl/reportes/api.php').replace(
      queryParameters: {
        'action': 'vista_previa',
        'tipo': tipo,
        'usuario_id': usuarioId.toString(),
        if (fechaDesde != null) 'fecha_desde': fechaDesde,
        if (fechaHasta != null) 'fecha_hasta': fechaHasta,
        if (sucursalId != null) 'sucursal_id': sucursalId.toString(),
        if (rolId != null) 'rol_id': rolId.toString(),
        if (material != null && material.isNotEmpty) 'material': material,
      },
    );

    final response = await http.get(uri);

    if (response.statusCode == 200) {
      return jsonDecode(response.body) as Map<String, dynamic>;
    } else {
      throw Exception('Error ${response.statusCode}: ${response.body}');
    }
  }
}

// Uso en un StatefulWidget o Provider
void _generarReporte() async {
  setState(() => _cargando = true);
  try {
    final service = ReportesService(usuarioId: widget.usuarioId);
    final resultado = await service.obtenerReporte(
      tipo: _tipoSeleccionado,
      fechaDesde: _fechaDesde?.toIso8601String().split('T')[0],
      fechaHasta: _fechaHasta?.toIso8601String().split('T')[0],
      sucursalId: _sucursalId,
    );

    if (resultado['success'] == true) {
      final datos = resultado['datos'] as List<dynamic>? ?? [];
      final tieneDatos = resultado['tieneDatos'] == true;
      // Actualizar UI con datos o mostrar "No hay datos"
      setState(() {
        _datosReporte = datos;
        _tieneDatos = tieneDatos;
      });
    } else {
      // Mostrar error
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(resultado['message'] ?? 'Error desconocido')),
      );
    }
  } catch (e) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('Error: $e')),
    );
  } finally {
    setState(() => _cargando = false);
  }
}
```

---

## 9. Estructura aproximada de `datos` por tipo

> Los campos exactos pueden variar según la versión del backend. Usa la respuesta real para definir los modelos.

| Tipo | Campos principales (ejemplos) |
|------|------------------------------|
| **compras** | id, numero_factura, fecha_compra, total, sucursal_nombre, proveedor_nombre, estado |
| **ventas** | id, fecha_venta, total, sucursal_nombre, cliente_nombre, estado |
| **inventarios** | producto_nombre, material_nombre, sucursal_nombre, cantidad, precio_venta, estado |
| **productos** | nombre, material_nombre, categoria_nombre, unidad, estado |
| **materiales** | nombre, categoria_nombre, cantidad, unidad |
| **sucursales** | nombre, dirección, estado, total_compras, total_ventas |
| **usuarios** | nombre, email, cedula, rol_nombre, estado, total_ventas, total_compras |

---

## 10. Checklist para el desarrollador Flutter

- [ ] Guardar `usuario.id` tras el login
- [ ] Enviar `usuario_id` en todas las peticiones a reportes
- [ ] Validar fechas cuando el tipo las requiera
- [ ] Manejar `tieneDatos == false` con un mensaje claro
- [ ] Manejar errores de autenticación (redirigir al login si aplica)
- [ ] Mostrar indicador de carga durante la petición
- [ ] Considerar paginación si `datos` es muy grande
- [ ] Probar con tipos `productos` y `materiales` sin fechas

---

## 11. Endpoint de PDF (opcional)

Para generar PDF directamente:

```
https://hermanosyanez.alwaysdata.net/reportes/pdf.php?tipo=compras&fecha_desde=2025-01-01&fecha_hasta=2025-12-31&usuario_id=1
```

Devuelve un archivo PDF descargable. Requiere los mismos parámetros que la API de vista previa.
