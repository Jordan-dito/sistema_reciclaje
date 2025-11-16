# Manual de Usuario
## Sistema de Gestión de Reciclaje

**Versión:** 1.0  
**Fecha:** 2024  
**Sistema:** Gestión de Reciclaje - Hermanos Yánez S.A.

---

## 📋 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Acceso al Sistema](#acceso-al-sistema)
3. [Navegación del Sistema](#navegación-del-sistema)
4. [Gestión de Categorías y Materiales](#gestión-de-categorías-y-materiales)
5. [Gestión de Unidades](#gestión-de-unidades)
6. [Gestión de Productos](#gestión-de-productos)
7. [Gestión de Inventarios](#gestión-de-inventarios)
8. [Gestión de Proveedores](#gestión-de-proveedores)
9. [Gestión de Compras](#gestión-de-compras)
10. [Gestión de Ventas](#gestión-de-ventas)
11. [Reportes](#reportes)
12. [Gestión de Usuarios](#gestión-de-usuarios)
13. [Preguntas Frecuentes](#preguntas-frecuentes)

---

## 1. Introducción

### 1.1 ¿Qué es el Sistema de Gestión de Reciclaje?

El Sistema de Gestión de Reciclaje es una plataforma web diseñada para administrar de manera eficiente todas las operaciones relacionadas con el reciclaje de materiales, incluyendo:

- Gestión de productos y materiales reciclables
- Control de inventarios por sucursal
- Registro de compras a proveedores
- Registro de ventas a clientes
- Generación de reportes y estadísticas

### 1.2 Roles del Sistema

El sistema cuenta con diferentes roles de usuario:

- **Administrador**: Acceso completo al sistema, puede gestionar usuarios, roles, sucursales y todas las operaciones.
- **Gerente**: Puede gestionar inventarios, compras, ventas y generar reportes.
- **Operador**: Acceso limitado para registrar operaciones básicas.

---

## 2. Acceso al Sistema

### 2.1 Inicio de Sesión

1. Abre tu navegador web (Chrome, Firefox, Edge, etc.)
2. Ingresa la URL del sistema proporcionada por el administrador
3. En la pantalla de inicio de sesión, ingresa:
   - **Email**: Tu correo electrónico registrado
   - **Contraseña**: Tu contraseña
4. Haz clic en el botón **"Iniciar Sesión"**

### 2.2 Registro de Nuevo Usuario

Si eres un nuevo usuario:

1. En la pantalla de inicio de sesión, haz clic en **"Regístrate aquí"**
2. Completa el formulario de registro:
   - **Nombre completo**: Tu nombre y apellidos
   - **Cédula**: Tu número de cédula ecuatoriana (10 dígitos)
   - **Email**: Tu correo electrónico válido
   - **Teléfono**: Tu número de teléfono (opcional)
   - **Contraseña**: Mínimo 8 caracteres
   - **Confirmar contraseña**: Repite tu contraseña
3. Haz clic en **"Registrarse"**
4. **Nota**: Los nuevos usuarios se registran automáticamente con el rol de **Gerente**

### 2.3 Recuperar Contraseña

Si olvidaste tu contraseña, contacta al administrador del sistema para que te proporcione una nueva.

---

## 3. Navegación del Sistema

### 3.1 Panel Principal (Dashboard)

Al iniciar sesión, verás el **Dashboard** que muestra:
- Resumen de inventarios
- Estadísticas de compras y ventas
- Alertas de stock mínimo
- Información general del sistema

### 3.2 Menú Lateral

El menú lateral contiene las siguientes secciones:

#### **Dashboard**
- Vista principal con resumen del sistema

#### **Administrador** (Solo para Administradores y Gerentes)
- **Gestión**
  - Usuarios
  - Roles
  - Sucursales
  - Categorías
  - Materiales
  - Productos
  - Unidades

#### **Inventario**
- Inventario
- Proveedores
- Compras
- Ventas

#### **Reportes**
- Generación de reportes y estadísticas

---

## 4. Gestión de Categorías y Materiales

### 4.1 Crear una Categoría con Materiales

**Ruta:** Gestión → Categorías

1. Haz clic en el botón **"Nueva Categoría"**
2. Completa el formulario:
   - **Nombre de la Categoría**: Ej. "Plástico", "Metal", "Papel"
   - **Descripción**: Descripción opcional de la categoría
   - **Icono**: Clase de Font Awesome (opcional), ej. "fa-recycle"
   - **Estado**: Activo/Inactivo
3. **Agregar Materiales**:
   - En la sección "Materiales de esta Categoría", completa:
     - **Nombre del Material**: Ej. "PET", "HDPE", "Aluminio"
     - **Descripción**: Descripción opcional del material
   - Para agregar más materiales, haz clic en **"Agregar Otro Material"**
4. Haz clic en **"Guardar Categoría y Materiales"**

**Nota:** Los materiales creados desde aquí también aparecerán en la vista de Materiales.

### 4.2 Editar una Categoría

1. En la lista de categorías, haz clic en el icono de **editar** (lápiz)
2. Modifica los campos necesarios
3. Haz clic en **"Actualizar Categoría"**

### 4.3 Desactivar una Categoría

1. En la lista de categorías, haz clic en el icono de **eliminar** (X)
2. Confirma la acción
3. La categoría cambiará su estado a "Inactivo" (no se elimina físicamente)

### 4.4 Crear Materiales por Separado

**Ruta:** Gestión → Materiales

1. Haz clic en **"Nuevo Material"**
2. Completa el formulario:
   - **Nombre**: Nombre del material
   - **Categoría**: Selecciona la categoría a la que pertenece
   - **Descripción**: Descripción opcional
   - **Icono**: Clase de Font Awesome (opcional)
   - **Estado**: Activo/Inactivo
3. Haz clic en **"Guardar Material"**

---

## 5. Gestión de Unidades

### 5.1 Crear una Unidad

**Ruta:** Gestión → Unidades

1. Haz clic en **"Nueva Unidad"**
2. Completa el formulario:
   - **Nombre**: Ej. "Kilogramo", "Litro", "Tonelada", "Unidad"
   - **Símbolo**: Ej. "kg", "L", "t", "u"
   - **Tipo**: Selecciona el tipo:
     - Peso
     - Volumen
     - Longitud
     - Cantidad
   - **Estado**: Activo/Inactivo
3. Haz clic en **"Guardar Unidad"**

### 5.2 Editar o Desactivar una Unidad

- **Editar**: Haz clic en el icono de editar (lápiz)
- **Desactivar**: Haz clic en el icono de eliminar (X)

---

## 6. Gestión de Productos

### 6.1 Crear un Producto

**Ruta:** Gestión → Productos

**IMPORTANTE:** Antes de crear un producto, asegúrate de tener:
- ✅ Materiales creados
- ✅ Unidades creadas

1. Haz clic en **"Nuevo Producto"**
2. Completa el formulario:
   - **Nombre**: Ej. "Botellas PET", "Latas de Aluminio"
   - **Material**: Selecciona el material (debe existir previamente)
   - **Unidad**: Selecciona la unidad (debe existir previamente)
   - **Precio de Venta**: Precio al que se vende el producto (opcional)
   - **Precio de Compra**: Precio al que se compra el producto (opcional)
   - **Descripción**: Descripción opcional del producto
   - **Estado**: Activo/Inactivo
3. Haz clic en **"Guardar Producto"**

**Nota:** Los precios se crean automáticamente al guardar el producto.

### 6.2 Editar un Producto

1. En la lista de productos, haz clic en el icono de **editar** (lápiz)
2. Modifica los campos necesarios
3. Los precios se pueden actualizar desde aquí
4. Haz clic en **"Actualizar Producto"**

### 6.3 Desactivar un Producto

1. Haz clic en el icono de **eliminar** (X)
2. Confirma la acción
3. El producto cambiará su estado a "Inactivo"

---

## 7. Gestión de Inventarios

### 7.1 Ver Inventario

**Ruta:** Inventario → Inventario

La vista de inventario muestra:
- **Producto**: Nombre del producto
- **Material**: Material del producto
- **Categoría**: Categoría del material
- **Sucursal**: Sucursal donde está el inventario
- **Cantidad**: Cantidad disponible
- **Stock Mínimo**: Cantidad mínima establecida
- **Stock Máximo**: Cantidad máxima establecida
- **Precio Venta**: Precio de venta del producto
- **Estado**: Activo/Inactivo

### 7.2 Crear o Actualizar Inventario

1. Haz clic en **"Nuevo Registro"**
2. Completa el formulario:
   - **Producto**: Selecciona el producto
   - **Sucursal**: Selecciona la sucursal
   - **Cantidad**: Cantidad inicial
   - **Stock Mínimo**: Cantidad mínima de alerta
   - **Stock Máximo**: Cantidad máxima permitida
   - **Estado**: Activo/Inactivo
3. Haz clic en **"Guardar"**

**Nota:** El inventario se actualiza automáticamente cuando:
- Se completa una compra (suma cantidad)
- Se completa una venta (resta cantidad)

---

## 8. Gestión de Proveedores

### 8.1 Crear un Proveedor

**Ruta:** Inventario → Proveedores

1. Haz clic en **"Nuevo Proveedor"**
2. Completa el formulario:
   - **Nombre**: Nombre o razón social del proveedor
   - **Cédula/RUC**: Número de cédula o RUC (se valida automáticamente)
   - **Tipo de Documento**: Cédula, RUC, Pasaporte, Consumidor Final
   - **Dirección**: Dirección completa
   - **Teléfono**: Número de teléfono (se valida formato ecuatoriano)
   - **Email**: Correo electrónico (opcional)
   - **Contacto**: Nombre de la persona de contacto (opcional)
   - **Tipo de Proveedor**: Recolector, Intermediario, Empresa, Otro
   - **Materiales que Suministra**: Descripción de los materiales
   - **Estado**: Activo/Inactivo
   - **Notas**: Notas adicionales (opcional)
3. Haz clic en **"Guardar Proveedor"**

### 8.2 Editar o Desactivar un Proveedor

- **Editar**: Haz clic en el icono de editar (lápiz)
- **Desactivar**: Haz clic en el icono de eliminar (X)

---

## 9. Gestión de Compras

### 9.1 Crear una Compra

**Ruta:** Inventario → Compras

1. Haz clic en **"Nueva Compra"**
2. Completa el formulario:
   - **Sucursal**: Selecciona la sucursal donde se recibirá la compra
   - **Proveedor**: Selecciona el proveedor
   - **Fecha de Compra**: Fecha de la compra
   - **Estado**: Pendiente/Completada
3. **Agregar Productos**:
   - Haz clic en **"Agregar Producto"**
   - Selecciona el **Producto**
   - El sistema mostrará automáticamente el **Precio de Compra**
   - Ingresa la **Cantidad**
   - El **Subtotal** se calcula automáticamente
   - Repite para agregar más productos
4. Haz clic en **"Guardar Compra"**

### 9.2 Completar una Compra

**IMPORTANTE:** Cuando una compra se marca como "Completada":
- ✅ El inventario se actualiza automáticamente (suma la cantidad)
- ✅ Si no existe inventario para ese producto en esa sucursal, se crea automáticamente

1. En la lista de compras, haz clic en el icono de **editar** (lápiz)
2. Cambia el **Estado** a "Completada"
3. Haz clic en **"Actualizar Compra"**

### 9.3 Ver Detalles de una Compra

En la lista de compras puedes ver:
- Número de compra
- Fecha
- Proveedor
- Sucursal
- Total
- Estado
- Productos incluidos

---

## 10. Gestión de Ventas

### 10.1 Crear una Venta

**Ruta:** Inventario → Ventas

1. Haz clic en **"Nueva Venta"**
2. Completa el formulario:
   - **Sucursal**: Selecciona la sucursal desde donde se vende
   - **Cliente**: Nombre del cliente (opcional, si no hay tabla de clientes)
   - **Fecha de Venta**: Fecha de la venta
   - **Estado**: Pendiente/Completada
3. **Agregar Productos**:
   - Haz clic en **"Agregar Producto"**
   - Selecciona el **Inventario** (producto disponible en esa sucursal)
   - El sistema mostrará:
     - **Precio de Venta** automáticamente
     - **Stock Disponible** del producto
   - Ingresa la **Cantidad** (no puede exceder el stock disponible)
   - El **Subtotal** se calcula automáticamente
   - Repite para agregar más productos
4. Haz clic en **"Guardar Venta"**

### 10.2 Completar una Venta

**IMPORTANTE:** Cuando una venta se marca como "Completada":
- ✅ El inventario se actualiza automáticamente (resta la cantidad)
- ✅ Se valida que haya stock suficiente antes de completar

1. En la lista de ventas, haz clic en el icono de **editar** (lápiz)
2. Cambia el **Estado** a "Completada"
3. Haz clic en **"Actualizar Venta"**

### 10.3 Ver Detalles de una Venta

En la lista de ventas puedes ver:
- Número de venta
- Fecha
- Cliente
- Sucursal
- Total
- Estado
- Productos vendidos

---

## 11. Reportes

### 11.1 Generar un Reporte

**Ruta:** Reportes

1. Selecciona el **Tipo de Reporte**:
   - Inventarios
   - Compras
   - Ventas
   - Productos
   - Materiales por Categoría
   - Sucursales
   - Usuarios por Rol
2. **Filtros** (según el tipo de reporte):
   - **Fecha Desde**: Fecha inicial (requerido para algunos reportes)
   - **Fecha Hasta**: Fecha final (requerido para algunos reportes)
   - **Rol**: Para reporte de usuarios (opcional)
3. Haz clic en **"Generar Vista Previa"**
4. Revisa el reporte generado
5. Opcional: Haz clic en **"Exportar a PDF"** o **"Exportar a Excel"**

### 11.2 Tipos de Reportes Disponibles

- **Inventarios**: Muestra el estado actual de inventarios por sucursal
- **Compras**: Lista de compras en un rango de fechas
- **Ventas**: Lista de ventas en un rango de fechas
- **Productos**: Lista completa de productos activos
- **Materiales por Categoría**: Materiales agrupados por categoría
- **Sucursales**: Información de sucursales
- **Usuarios por Rol**: Usuarios agrupados por rol

---

## 12. Gestión de Usuarios

**Ruta:** Gestión → Usuarios (Solo Administradores)

### 12.1 Crear un Usuario

1. Haz clic en **"Nuevo Usuario"**
2. Completa el formulario:
   - **Nombre**: Nombre completo
   - **Cédula**: Cédula ecuatoriana (10 dígitos, se valida automáticamente)
   - **Email**: Correo electrónico válido
   - **Teléfono**: Teléfono ecuatoriano (opcional)
   - **Contraseña**: Mínimo 8 caracteres
   - **Rol**: Selecciona el rol (Administrador, Gerente, Operador)
   - **Estado**: Activo/Inactivo
3. Haz clic en **"Guardar Usuario"**

### 12.2 Editar un Usuario

1. Haz clic en el icono de **editar** (lápiz)
2. Modifica los campos necesarios
3. Si cambias la contraseña, ingresa una nueva
4. Haz clic en **"Actualizar Usuario"**

### 12.3 Desactivar un Usuario

1. Haz clic en el icono de **eliminar** (X)
2. Confirma la acción
3. El usuario cambiará su estado a "Inactivo" (no podrá iniciar sesión)

---

## 13. Preguntas Frecuentes

### ¿Cómo se actualiza el inventario?

El inventario se actualiza automáticamente cuando:
- Se completa una **compra** → Suma cantidad al inventario
- Se completa una **venta** → Resta cantidad del inventario

### ¿Puedo eliminar un producto que ya tiene compras o ventas?

No se eliminan físicamente. Los productos, materiales, categorías, etc., solo cambian su estado a "Inactivo" para mantener el historial.

### ¿Qué pasa si intento vender más de lo que hay en inventario?

El sistema valida el stock disponible antes de permitir completar la venta. Si no hay suficiente stock, mostrará un error.

### ¿Cómo creo un producto nuevo?

1. Primero asegúrate de tener:
   - Materiales creados
   - Unidades creadas
2. Ve a Gestión → Productos
3. Haz clic en "Nuevo Producto"
4. Completa el formulario con material, unidad y precios

### ¿Los materiales se duplican si los creo desde Categorías y desde Materiales?

No. Los materiales se crean una sola vez en la base de datos. Puedes crearlos desde cualquier lugar, pero todos aparecen en la misma lista.

### ¿Qué validaciones tiene el sistema?

El sistema valida:
- **Cédulas ecuatorianas**: Algoritmo de validación completo
- **RUC ecuatoriano**: Validación de formato y dígito verificador
- **Teléfonos**: Formato ecuatoriano (9 dígitos celular, 7 fijo)
- **Emails**: Formato válido
- **Números**: Solo números en campos numéricos
- **Letras**: Solo letras y espacios en nombres
- **Espacios en blanco**: No permite campos con solo espacios

### ¿Cómo cambio mi contraseña?

Contacta al administrador del sistema para que te proporcione una nueva contraseña.

### ¿Puedo exportar reportes?

Sí, los reportes se pueden exportar a PDF o Excel desde la sección de Reportes.

---

## 📞 Soporte

Para más información o soporte técnico, contacta al administrador del sistema.

---

**Fin del Manual de Usuario**

*Última actualización: 2024*

