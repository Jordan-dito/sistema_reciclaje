# Manual Técnico del Sistema de Gestión de Reciclaje

## 1. Introducción

Este manual técnico proporciona una visión detallada de la arquitectura, diseño y funcionamiento interno del sistema de gestión de reciclaje. Está dirigido a desarrolladores, administradores de sistemas y personal técnico involucrado en el mantenimiento, extensión y soporte de la aplicación.

El sistema está diseñado para optimizar el proceso de gestión de materiales reciclables, desde su recolección y clasificación hasta su venta y reporte. Incluye módulos para la gestión de usuarios, sucursales, clientes, proveedores, productos, inventarios, compras, ventas, gastos y reportes.

## 2. Visión General del Sistema

El sistema es una aplicación web basada en PHP con una base de datos MySQL, complementada con una API RESTful que permite la integración con aplicaciones externas, incluyendo una futura aplicación móvil desarrollada en Flutter.

### Componentes Principales:

*   **Frontend Web (Panel de Administración):** Interfaz de usuario para la gestión diaria del sistema por parte del personal y administradores. Construido con HTML, CSS (Bootstrap), JavaScript (jQuery) y PHP para la renderización dinámica.
*   **Backend (Lógica de Negocio y API):** Desarrollado en PHP, maneja la lógica de negocio, la interacción con la base de datos y expone endpoints para el frontend web y la aplicación móvil.
*   **Base de Datos (MySQL):** Almacena toda la información del sistema, incluyendo datos de sucursales, usuarios, clientes, productos, transacciones, etc.
*   **API RESTful:** Conjunto de endpoints PHP que permiten a las aplicaciones cliente (web y móvil) interactuar con la lógica del negocio y la base de datos de manera estandarizada.
*   **Aplicación Móvil (Flutter - planeada/en desarrollo):** Una aplicación complementaria que consumirá la API para ofrecer funcionalidades específicas en dispositivos móviles, como la visualización de reportes gráficos por sucursal.

### Flujo Básico de Interacción:

1.  **Usuario (Web/Móvil):** Interactúa con la interfaz de usuario.
2.  **Frontend (Web/Móvil):** Envía solicitudes HTTP (GET, POST) a los endpoints de la API PHP.
3.  **Backend (PHP):**
    *   Valida la autenticación y autorización del usuario.
    *   Procesa la solicitud, aplicando la lógica de negocio.
    *   Interactúa con la base de datos (MySQL) para leer o escribir datos.
    *   Devuelve una respuesta en formato JSON al frontend.
4.  **Frontend (Web/Móvil):** Recibe la respuesta JSON, la procesa y actualiza la interfaz de usuario.

## 3. Arquitectura y Tecnologías Utilizadas

### 3.1. Arquitectura General

El sistema sigue una arquitectura N-capas, separando claramente la presentación, la lógica de negocio y la capa de acceso a datos.

*   **Capa de Presentación:** Compuesta por el frontend web (HTML, CSS, JS) y la futura aplicación móvil (Flutter).
*   **Capa de Lógica de Negocio:** Implementada en PHP, gestiona las reglas de negocio, validaciones y coordinación entre la capa de presentación y la de datos.
*   **Capa de Acceso a Datos:** Utiliza PDO (PHP Data Objects) para interactuar con la base de datos MySQL, asegurando una comunicación segura y eficiente.

### 3.2. Tecnologías del Backend

*   **Lenguaje de Programación:** PHP 7.x / 8.x
*   **Gestión de Dependencias:** Composer (utilizado para PHPMailer y otras librerías)
*   **Acceso a Base de Datos:** PDO (PHP Data Objects)
*   **Manejo de Sesiones:** Sesiones nativas de PHP para la autenticación en la parte web.
*   **Manejo de Errores:** Clase `ErrorHandler` personalizada para un manejo consistente de excepciones y errores de base de datos, con opciones de depuración (`APP_DEBUG`).

### 3.3. Tecnologías del Frontend Web

*   **HTML5:** Estructura de las páginas web.
*   **CSS3:** Estilos y presentación.
    *   **Bootstrap 5:** Framework CSS para diseño responsivo y componentes de UI.
    *   **Font Awesome:** Librería de iconos.
    *   **JavaScript:** Interactividad en el lado del cliente.
    *   **jQuery:** Librería JS para manipulación del DOM y eventos.
    *   **DataTables:** Plugin de jQuery para tablas interactivas.
    *   **SweetAlert2:** Librería para alertas y cuadros de diálogo atractivos.
    *   **Chart.js:** Librería para la creación de gráficos (potencialmente utilizado para reportes visuales).

### 3.4. Base de Datos

*   **Sistema Gestor de Base de Datos:** MySQL
*   **Diseño:** Relacional, con tablas para usuarios, roles, sucursales, clientes, proveedores, productos, categorías, unidades, inventarios, compras, ventas, gastos, etc.
*   **Conexión:** `config/database.php` centraliza la configuración y la función `getDB()` para obtener la conexión PDO.

### 3.5. Aplicación Móvil (Flutter)

*   **Framework:** Flutter (para iOS y Android).
*   **Lenguaje:** Dart.
*   **Comunicación con API:** Se utilizará el paquete `http` de Dart para realizar solicitudes a la API RESTful de PHP.
*   **Visualización de Gráficos:** Se integrarán librerías de gráficos específicas de Flutter (ej. `fl_chart`, `charts_flutter`) para la representación de los datos obtenidos del endpoint de reportes.

## 4. Estructura de Carpetas Principal

La siguiente es una descripción de las carpetas y archivos clave en la raíz del proyecto:

```
tesis reciclaje/
├── assets/                 # Archivos estáticos: JS, CSS, imágenes, fuentes (Bootstrap, FontAwesome, etc.)
├── categorias/             # Módulo de gestión de categorías (API y frontend)
├── clientes/               # Módulo de gestión de clientes (API y frontend)
├── components/             # Componentes HTML reusables o módulos pequeños
├── compras/                # Módulo de gestión de compras (API y frontend)
├── config/                 # Archivos de configuración global (DB, Auth, Email, Errores, etc.)
├── database/               # Scripts SQL para la creación de tablas o migraciones
├── deploy_tools/           # Herramientas y scripts para el despliegue del sistema
├── empleados/              # Módulo de gestión de empleados y gastos (API y frontend)
├── includes/               # Archivos PHP reusables (cabeceras, pies de página, barras laterales, modales)
├── inventarios/            # Módulo de gestión de inventarios (API y frontend)
├── materiales/             # Módulo de gestión de materiales (API y frontend)
├── productos/              # Módulo de gestión de productos (API y frontend)
├── proveedores/            # Módulo de gestión de proveedores (API y frontend)
├── reportes/               # Módulo de generación de reportes (API y frontend, incluye el nuevo endpoint para gráficos)
├── roles/                  # Módulo de gestión de roles y permisos (API y frontend)
├── scripts/                # Scripts PHP o JS auxiliares
├── sucursales/             # Módulo de gestión de sucursales (API y frontend)
├── unidades/               # Módulo de gestión de unidades de medida (API y frontend)
├── usuarios/               # Módulo de gestión de usuarios (API y frontend)
├── vendor/                 # Dependencias PHP instaladas via Composer (PHPMailer, etc.) y librerías de frontend (jQuery, Bootstrap, Chart.js, DataTables, etc.)
├── ventas/                 # Módulo de gestión de ventas (API y frontend)
├── .env.example            # Ejemplo de archivo de variables de entorno (para configuración sensible)
├── .gitignore              # Reglas para ignorar archivos en Git
├── Dashboard.php           # Página principal del panel de administración (después del login)
├── index.html              # Página de inicio / landing page estática (o redirige al login)
├── index.php               # Punto de entrada principal para la aplicación web (posiblemente maneja enrutamiento o login)
├── login.html              # Interfaz de usuario para el inicio de sesión
├── register.php            # Lógica para el registro de nuevos usuarios
├── MANUAL_TECNICO.md       # Este manual técnico
└── GUIA_DEPLOY.md          # Guía para el despliegue del sistema
```

## 5. Esquema de la Base de Datos

El sistema utiliza una base de datos MySQL con un diseño relacional. A continuación, se describen algunas de las tablas más importantes y sus relaciones. Los scripts de creación de tablas se encuentran en la carpeta `database/`.

### Tablas Principales:

*   **`usuarios`**: Almacena la información de los usuarios del sistema, incluyendo credenciales, roles y sucursal asignada.
    *   Relación: `1:N` con `roles` (un usuario tiene un rol), `1:1` con `sucursales` (un usuario puede ser responsable de una sucursal).
*   **`roles`**: Define los diferentes roles de usuario y sus permisos asociados.
    *   Relación: `N:1` con `usuarios`.
*   **`sucursales`**: Contiene los detalles de cada sucursal de la empresa.
    *   Relación: `1:N` con `usuarios` (varios usuarios pueden estar asignados a una sucursal), `1:N` con `compras`, `1:N` con `ventas`, `1:N` con `inventarios`, `1:N` con `gastos_varios`.
*   **`clientes`**: Guarda la información de los clientes.
    *   `id`: Clave primaria, auto-incrementable.
    *   `nombre`: Nombre o razón social del cliente.
    *   `cedula_ruc`: Cédula o RUC (único).
    *   `tipo_documento`: Enum ('cedula', 'ruc').
    *   `direccion`, `telefono`, `email`, `contacto`.
    *   `tipo_cliente`: Enum ('minorista', 'mayorista', 'empresa').
    *   `estado`: Enum ('activo', 'inactivo').
    *   `creado_por`: ID del usuario que creó el registro (Foreign Key a `usuarios.id`).
    *   `fecha_creacion`, `fecha_actualizacion`.
    *   Relación: `1:N` con `ventas` (un cliente puede tener muchas ventas).
*   **`proveedores`**: Información de los proveedores.
    *   Relación: `1:N` con `compras`.
*   **`categorias`**: Para clasificar los productos.
    *   Relación: `1:N` con `productos`.
*   **`unidades`**: Unidades de medida para los productos (ej. kg, unidad, litro).
    *   Relación: `1:N` con `productos`.
*   **`productos`**: Detalles de los productos reciclables o a la venta.
    *   Relación: `N:1` con `categorias`, `N:1` con `unidades`, `1:N` con `inventarios`.
*   **`inventarios`**: Registra el stock de productos en cada sucursal.
    *   Relación: `N:1` con `sucursales`, `N:1` con `productos`.
*   **`compras`**: Registros de las compras de materiales a proveedores.
    *   Relación: `N:1` con `sucursales`, `N:1` con `proveedores`, `1:N` con `detalle_compras`.
*   **`detalle_compras`**: Detalles de los productos incluidos en cada compra.
    *   Relación: `N:1` con `compras`, `N:1` con `productos`.
*   **`ventas`**: Registros de las ventas de productos a clientes.
    *   Relación: `N:1` con `sucursales`, `N:1` con `clientes`, `1:N` con `detalle_ventas`.
*   **`detalle_ventas`**: Detalles de los productos incluidos en cada venta.
    *   Relación: `N:1` con `ventas`, `N:1` con `productos`.
*   **`gastos_varios`**: Registros de los gastos operativos de las sucursales.
    *   Relación: `N:1` con `sucursales`.

### Relaciones Clave:

*   Las tablas de transacciones (`compras`, `ventas`, `gastos_varios`, `inventarios`) suelen estar relacionadas con `sucursales` para permitir el seguimiento por ubicación.
*   Las tablas de `detalle_compras` y `detalle_ventas` actúan como tablas pivote para las relaciones `N:M` entre `compras/ventas` y `productos`.
*   `creado_por` en varias tablas se relaciona con `usuarios.id` para auditar quién creó el registro.

## 6. Endpoints de la API

El sistema expone una serie de endpoints RESTful para la interacción tanto del frontend web como de la aplicación móvil (Flutter). La mayoría de los endpoints requieren autenticación. Los que son para la app móvil suelen tener habilitado `Access-Control-Allow-Origin: *`.

### 6.1. Endpoints de Autenticación y Usuarios

*   **`config/login.php`**
    *   **Método:** `POST` (soporta `application/json` y `form-data`)
    *   **Acción:** No se especifica un parámetro `action` explícito; el endpoint maneja el login directamente.
    *   **Descripción:** Permite a un usuario iniciar sesión proporcionando `email` y `password`. Retorna información del usuario (incluyendo URL de foto de perfil completa) y un indicador de éxito.
    *   **Para App Móvil:** Sí (`Access-Control-Allow-Origin: *`).

*   **`config/get_user.php`**
    *   **Método:** `GET` o `POST` (soporta `application/json` y `form-data`)
    *   **Acción:** No se especifica un parámetro `action` explícito; el endpoint obtiene los datos del usuario logueado.
    *   **Descripción:** Obtiene los datos actualizados del usuario actualmente autenticado. Requiere `usuario_id` o `id` en la solicitud (si no está autenticado por sesión). Retorna información detallada del usuario, incluyendo el rol y la sucursal asignada.
    *   **Para App Móvil:** Sí (`Access-Control-Allow-Origin: *`).

*   **`config/password-reset.php`**
    *   **Método:** `GET` o `POST`
    *   **Acciones:**
        *   `solicitar` (POST): Inicia el proceso de recuperación de contraseña enviando un correo al `email` proporcionado.
        *   `verificar` (GET): Verifica la validez de un `token` de recuperación de contraseña.
        *   `restablecer` (POST): Permite al usuario establecer una nueva `password` usando un `token` válido.
    *   **Descripción:** Gestiona el flujo de recuperación y restablecimiento de contraseñas.
    *   **Para App Móvil:** Sí (`Access-Control-Allow-Origin: *`).

*   **`usuarios/api.php`**
    *   **Método:** `GET`, `POST`
    *   **Acciones (GET):**
        *   `listar`: Obtiene una lista de todos los usuarios registrados con su rol.
        *   `obtener` (`id`): Recupera los detalles de un usuario específico por su ID.
    *   **Acciones (POST):**
        *   `crear`: Registra un nuevo usuario con `nombre`, `cedula`, `email`, `telefono`, `password`, `rol_id`, `estado`. Realiza validaciones de formato y unicidad.
        *   `actualizar` (`id`): Modifica la información de un usuario existente. Permite actualizar la `password` opcionalmente.
        *   `eliminar` / `desactivar` (`id`): Cambia el estado de un usuario a 'inactivo' (soft delete). Incluye lógica para evitar desactivar el último administrador activo.
        *   `activar` (`id`): Cambia el estado de un usuario a 'activo'.
    *   **Descripción:** API completa para la gestión CRUD de usuarios del sistema. Requiere autenticación.

### 6.2. Endpoints de Configuración y Datos Maestros

*   **`config/porcentajes_categorias.php`**
    *   **Método:** `GET` o `POST`
    *   **Acción:** No se especifica un parámetro `action` explícito; el endpoint obtiene los porcentajes.
    *   **Descripción:** Calcula y retorna el porcentaje de cada categoría basado en la cantidad de productos/materiales en inventario. Permite filtrar por `anio`, `mes` y `sucursal_id`. Es útil para gráficos de distribución de materiales.
    *   **Para App Móvil:** Sí (`Access-Control-Allow-Origin: *`).

*   **`sucursales/api.php`**
    *   **Método:** `GET`, `POST`
    *   **Acciones (GET):**
        *   `listar`: Obtiene una lista completa de todas las sucursales con el nombre de su responsable.
        *   `obtener` (`id`): Recupera los detalles de una sucursal específica.
        *   `activas`: Lista solo las sucursales con estado 'activa', aplicando un filtro si el usuario logueado tiene una sucursal asignada.
        *   `disponibles`: **(Para App Móvil)** Obtiene una lista de sucursales activas (id, nombre, dirección, teléfono, email, estado) sin requerir autenticación. Ideal para selección de sucursal inicial en la app.
    *   **Acciones (POST):**
        *   `crear`: Registra una nueva sucursal con `nombre`, `direccion`, `telefono`, `email`, `responsable_id`, `estado`, `saldo`. Incluye validaciones y actualización del `sucursal_id` en el usuario responsable.
        *   `actualizar` (`id`): Modifica los datos de una sucursal existente. Gestiona la vinculación/desvinculación de usuarios responsables.
        *   `eliminar` / `desactivar` (`id`): Cambia el estado de una sucursal a 'inactiva' (soft delete).
        *   `activar` (`id`): Cambia el estado de una sucursal a 'activa'.
    *   **Descripción:** API para la gestión completa de sucursales. Requiere autenticación para la mayoría de acciones.

*   **`categorias/api.php`**
    *   **Método:** `GET`, `POST`
    *   **Acciones (GET):**
        *   `listar`: Obtiene una lista de categorías (filtrables por `estado`: 'activos' o 'inactivos').
        *   `obtener` (`id`): Recupera los detalles de una categoría específica.
    *   **Acciones (POST):**
        *   `crear`: Registra una nueva categoría con `nombre`, `descripcion`, `estado`. Valida la unicidad del nombre.
        *   `actualizar` (`id`): Modifica los datos de una categoría existente.
        *   `eliminar` (`id`): Cambia el estado de una categoría a 'inactivo' (soft delete).
        *   `activar` (`id`): Cambia el estado de una categoría a 'activo'.
    *   **Descripción:** Gestión CRUD de categorías. Requiere autenticación.

*   **`unidades/api.php`**
    *   **Método:** `GET`, `POST`
    *   **Acciones (GET):**
        *   `listar`: Obtiene una lista de unidades de medida (filtrables por `estado`).
        *   `obtener` (`id`): Recupera los detalles de una unidad específica.
    *   **Acciones (POST):**
        *   `crear`: Registra una nueva unidad con `nombre`, `simbolo`, `tipo`, `estado`. Valida la unicidad del nombre.
        *   `actualizar` (`id`): Modifica los datos de una unidad existente.
        *   `eliminar` (`id`): Cambia el estado de una unidad a 'inactivo' (soft delete).
        *   `activar` (`id`): Cambia el estado de una unidad a 'activo'.
    *   **Descripción:** Gestión CRUD de unidades de medida. Requiere autenticación.

*   **`roles/api.php`**
    *   **Método:** `GET`, `POST`
    *   **Acciones (GET):**
        *   `listar`: Obtiene una lista de todos los roles.
        *   `obtener` (`id`): Recupera los detalles de un rol específico.
        *   `listar_modulos`: Lista todos los módulos disponibles en el sistema.
        *   `modulos_por_rol` (`rol_id`): Obtiene los módulos asignados a un rol específico.
    *   **Acciones (POST):**
        *   `crear`: Crea un nuevo rol con `nombre`, `descripcion`, `permisos` (JSON), `estado`.
        *   `actualizar` (`id`): Modifica los datos de un rol existente.
        *   `eliminar` / `desactivar` (`id`): Cambia el estado de un rol a 'inactivo'. Impide la desactivación si tiene usuarios activos asociados.
        *   `activar` (`id`): Cambia el estado de un rol a 'activo'.
        *   `asignar_modulo` (`rol_id`, `modulo_id`): Asigna un módulo a un rol.
        *   `quitar_modulo` (`rol_id`, `modulo_id`): Quita un módulo de un rol.
    *   **Descripción:** Gestión CRUD de roles y sus permisos (módulos). Requiere autenticación.

### 6.3. Endpoints de Clientes y Proveedores

*   **`clientes/api.php`**
    *   **Método:** `GET`, `POST`
    *   **Acciones (GET):**
        *   `listar`: Obtiene una lista de clientes (filtrables por `estado`).
        *   `obtener` (`id`): Recupera los detalles de un cliente específico.
    *   **Acciones (POST):**
        *   `crear`: Registra un nuevo cliente con `nombre`, `cedula_ruc`, `tipo_documento`, `direccion`, `telefono`, `email`, `contacto`, `tipo_cliente`, `notas`. Realiza validaciones exhaustivas (cédula/RUC, email, teléfono, unicidad).
        *   `actualizar` (`id`): Modifica los datos de un cliente existente. Mantiene las validaciones de unicidad y formato.
        *   `eliminar` (`id`): Cambia el estado de un cliente a 'inactivo' (soft delete).
        *   `activar` (`id`): Cambia el estado de un cliente a 'activo'.
    *   **Descripción:** Gestión CRUD de clientes. Requiere autenticación.

*   **`proveedores/api.php`**
    *   **Método:** `GET`, `POST`
    *   **Acciones (GET):**
        *   `listar`: Obtiene una lista de proveedores (filtrables por `estado`).
        *   `obtener` (`id`): Recupera los detalles de un proveedor específico.
    *   **Acciones (POST):**
        *   `crear`: Registra un nuevo proveedor con `nombre`, `cedula_ruc`, `tipo_documento`, `direccion`, `telefono`, `email`, `contacto`, `tipo_proveedor`, `materiales_suministra`, `notas`. Realiza validaciones similares a las de clientes.
        *   `actualizar` (`id`): Modifica los datos de un proveedor existente.
        *   `eliminar` (`id`): Cambia el estado de un proveedor a 'inactivo' (soft delete).
        *   `activar` (`id`): Cambia el estado de un proveedor a 'activo'.
    *   **Descripción:** Gestión CRUD de proveedores. Requiere autenticación.

### 6.4. Endpoints de Productos e Inventarios

*   **`productos/api.php`**
    *   **Método:** `GET`, `POST`
    *   **Acciones (GET):**
        *   `listar`: Obtiene una lista de productos con detalles de material, categoría, unidad y precios de venta/compra activos (filtrables por `estado`).
        *   `obtener` (`id`): Recupera los detalles de un producto específico, incluyendo sus precios asociados.
        *   `materiales`: Lista los materiales activos disponibles.
        *   `unidades`: Lista las unidades de medida activas disponibles.
    *   **Acciones (POST):**
        *   `crear`: Registra un nuevo producto, genera automáticamente un código numérico secuencial, y crea sus precios de venta y compra. Requiere `material_id`, `unidad_id`, `descripcion`, `precio_venta`, `precio_compra`. Valida la unicidad de la combinación material+unidad.
        *   `actualizar` (`id`): Modifica los datos de un producto existente y sus precios asociados.
        *   `eliminar` (`id`): Cambia el estado de un producto a 'inactivo' (soft delete).
        *   `activar` (`id`): Cambia el estado de un producto a 'activo'.
    *   **Descripción:** Gestión CRUD de productos. Requiere autenticación.

*   **`inventarios/api.php`**
    *   **Método:** `GET`, `POST`
    *   **Acciones (GET):**
        *   `listar`: Obtiene el inventario de productos por sucursal, incluyendo detalles de producto, material, categoría, unidad y precio de venta (filtrable por `sucursal_id`).
        *   `productos`: Lista productos activos con categoría.
        *   `sucursales`: Lista sucursales activas.
        *   `obtener` (`id`): Recupera los detalles de un registro de inventario específico.
    *   **Acciones (POST):**
        *   `crear`: Crea o actualiza un registro de inventario para un `producto_id` y `sucursal_id` específicos. Si existe, suma la cantidad; si no, lo crea. Permite ajustar `stock_minimo`, `stock_maximo` y `estado`.
        *   `actualizar` (`id`): Modifica un registro de inventario existente.
        *   `eliminar` (`id`): Cambia el estado de un registro de inventario a 'inactivo'.
    *   **Descripción:** Gestión del stock de productos por sucursal. Requiere autenticación.

### 6.5. Endpoints de Transacciones y Reportes

*   **`compras/api.php`**
    *   **Método:** `GET`, `POST`
    *   **Acciones (GET):**
        *   `listar`: Obtiene una lista de compras, incluyendo el proveedor y la sucursal, y sus detalles de productos. Filtrable por `sucursal_id` y `estado`.
        *   `productos`: Lista productos activos con precios de compra, filtrados por la sucursal del usuario.
        *   `obtener` (`id`): Recupera los detalles de una compra específica con todos sus productos.
        *   `siguiente_numero_factura`: Genera el siguiente número de factura de compra secuencial.
    *   **Acciones (POST):**
        *   `crear`: Registra una nueva compra y sus detalles de productos. Opcionalmente, si la compra está 'completada', actualiza el inventario y descuenta el `total` del `saldo` de la sucursal. Incluye validación de saldo de sucursal.
        *   `actualizar` (`id`): Modifica los datos principales de una compra existente (sin modificar los detalles de los productos).
        *   `eliminar` (`id`): Cambia el estado de una compra a 'cancelada'. Si la compra estaba 'completada', devuelve el monto al saldo de la sucursal y restaura el stock de los productos.
    *   **Descripción:** Gestión completa de compras de materiales. Requiere autenticación.

*   **`ventas/api.php`**
    *   **Método:** `GET`, `POST`
    *   **Acciones (GET):**
        *   `listar`: Obtiene una lista de ventas, incluyendo el nombre del cliente y la sucursal, con sus detalles de productos. Filtrable por `sucursal_id` y `estado`.
        *   `inventarios`: Lista los productos disponibles en inventario por sucursal con sus precios de venta.
        *   `obtener` (`id`): Recupera los detalles de una venta específica con todos sus productos.
        *   `siguiente_numero_factura`: Genera el siguiente número de factura de venta secuencial.
    *   **Acciones (POST):**
        *   `crear`: Registra una nueva venta y sus detalles de productos. Si la venta está 'completada', actualiza el inventario (resta stock) y añade el `total` al `saldo` de la sucursal. Incluye validación de stock.
        *   `actualizar` (`id`): Modifica los datos principales de una venta existente (sin modificar los detalles de los productos).
        *   `eliminar` (`id`): Cambia el estado de una venta a 'cancelada'. Si la venta estaba 'completada', resta el monto del saldo de la sucursal y devuelve el stock a los inventarios.
    *   **Descripción:** Gestión completa de ventas de productos. Requiere autenticación.

*   **`empleados/api_gastos.php`**
    *   **Método:** `GET`, `POST`
    *   **Acciones (GET):**
        *   `list`: Lista los gastos operativos, filtrables por `mes`, `anio` y `sucursal_id`. Calcula el `saldo_sucursal` actual de la sucursal filtrada. Si el usuario no es administrador, fuerza el filtro por su sucursal asignada.
    *   **Acciones (POST):**
        *   `create`: Registra un nuevo gasto (`concepto`, `descripcion`, `monto`, `fecha`, `sucursal_id`) y lo descuenta del `saldo` de la sucursal. Realiza validación de saldo suficiente. (Nota: en tu última actualización, eliminaste la validación de duplicados en la creación).
        *   `delete` (`id`): Cambia el estado de un gasto a 'cancelado' y devuelve el `monto` al `saldo` de la sucursal.
    *   **Descripción:** Gestión de gastos operativos por sucursal. Requiere autenticación.

*   **`reportes/api_graficos.php`**
    *   **Método:** `GET` (soporta preflight OPTIONS)
    *   **Acción:** `gastos_compras_por_sucursal`
    *   **Parámetros GET:** `mes` (opcional), `anio` (opcional).
    *   **Descripción:** **(Para App Móvil)** Proporciona datos consolidados del total de gastos y total de compras por cada sucursal activa. Los datos se pueden filtrar por mes y año. Ideal para la visualización de gráficos comparativos en la aplicación móvil.
    *   **Para App Móvil:** Sí (`Access-Control-Allow-Origin: *`).

## 7. Módulos/Funcionalidades Clave

El sistema se organiza en módulos que agrupan funcionalidades relacionadas. A continuación, se describen los módulos principales:

### 7.1. Módulo de Autenticación y Autorización

*   **Descripción:** Permite a los usuarios registrarse, iniciar sesión, recuperar contraseñas y gestiona los permisos de acceso a las diferentes partes del sistema. Cada usuario tiene un `rol` que determina qué acciones puede realizar y qué datos puede ver o modificar.
*   **Ubicación:** Principalmente en la carpeta `config/` (archivos como `auth.php`, `login.php`, `get_user.php`, `password-reset.php`) y el módulo `roles/`.
*   **Características:** Login seguro (hash de contraseñas), restablecimiento de contraseña vía email, gestión de roles con permisos detallados (módulos por rol), control de acceso basado en sesión (web) o posiblemente token (móvil).

### 7.2. Módulo de Gestión de Usuarios y Roles

*   **Descripción:** Permite a los administradores crear, ver, actualizar y desactivar cuentas de usuario, así como definir y asignar roles con conjuntos específicos de permisos.
*   **Ubicación:** Carpetas `usuarios/` y `roles/`.
*   **Características:** CRUD de usuarios, asignación de roles a usuarios, gestión de roles y sus módulos asociados, validaciones de datos de usuario (cédula/RUC, email, etc.).

### 7.3. Módulo de Gestión de Sucursales

*   **Descripción:** Administra la información de las diferentes sucursales de la empresa. Cada sucursal tiene un saldo de caja, dirección, contacto y puede tener un responsable asignado.
*   **Ubicación:** Carpeta `sucursales/`.
*   **Características:** CRUD de sucursales, asignación de usuarios responsables, seguimiento del saldo de caja por sucursal, activación/desactivación de sucursales.

### 7.4. Módulo de Gestión de Clientes y Proveedores

*   **Descripción:** Permite registrar y gestionar la información de los clientes (a quienes se vende material) y los proveedores (a quienes se compra material). Incluye sus datos de contacto, tipo de documento, etc.
*   **Ubicación:** Carpetas `clientes/` y `proveedores/`.
*   **Características:** CRUD de clientes y proveedores, validaciones de documentos (cédula/RUC ecuatorianos), estados activo/inactivo.

### 7.5. Módulo de Gestión de Materiales, Productos y Unidades

*   **Descripción:** Centraliza la catalogación de los materiales reciclables, los productos derivados de estos materiales y las unidades de medida utilizadas. Los productos tienen precios de compra y venta.
*   **Ubicación:** Carpetas `materiales/`, `productos/` y `unidades/`, `categorias/`.
*   **Características:** CRUD de materiales, productos, unidades y categorías. Generación automática de códigos para productos. Gestión de precios de compra y venta por producto.

### 7.6. Módulo de Gestión de Inventarios

*   **Descripción:** Controla el stock de cada producto en cada sucursal. Permite saber cuánta cantidad de cada producto hay disponible en un lugar específico.
*   **Ubicación:** Carpeta `inventarios/`.
*   **Características:** Registro y actualización de cantidades de productos por sucursal, seguimiento de stock mínimo y máximo, integración con módulos de compras y ventas para la actualización automática del stock.

### 7.7. Módulo de Gestión de Compras

*   **Descripción:** Registra las transacciones de compra de materiales a proveedores. Incluye detalles como el proveedor, la sucursal, los productos comprados, cantidades, precios, descuentos e impuestos.
*   **Ubicación:** Carpeta `compras/`.
*   **Características:** Registro de compras con múltiples ítems, cálculo automático de totales (subtotal, IVA, descuento, total), actualización del inventario y del saldo de caja de la sucursal al completar la compra, generación de números de factura secuenciales, cancelación de compras (restaura stock y saldo).

### 7.8. Módulo de Gestión de Ventas

*   **Descripción:** Administra las transacciones de venta de productos a clientes. Similar a compras, pero enfocado en la salida de productos y el ingreso de dinero.
*   **Ubicación:** Carpeta `ventas/`.
*   **Características:** Registro de ventas con múltiples ítems, cálculo automático de totales, validación de stock disponible, actualización del inventario y del saldo de caja de la sucursal al completar la venta, generación de números de factura secuenciales, cancelación de ventas (restaura stock y saldo).

### 7.9. Módulo de Gestión de Gastos Operativos

*   **Descripción:** Permite registrar los diversos gastos de operación incurridos por cada sucursal. Incluye el concepto, descripción, monto y fecha.
*   **Ubicación:** Carpeta `empleados/` (específicamente `api_gastos.php` y `gastos_varios.php`).
*   **Características:** CRUD de gastos, descuento automático del saldo de caja de la sucursal al registrar un gasto, cancelación de gastos (devuelve el monto al saldo).

### 7.10. Módulo de Reportes y Gráficos

*   **Descripción:** Genera diversos reportes para el análisis del negocio, incluyendo resúmenes de gastos y compras por sucursal para la aplicación móvil.
*   **Ubicación:** Carpeta `reportes/`.
*   **Características:** Endpoint para la aplicación móvil que proporciona datos agregados de gastos y compras, con filtros por tiempo y sucursal. Ideal para visualizaciones de datos en la app Flutter.

## 8. Sistema de Autenticación y Autorización

El sistema implementa un robusto mecanismo de autenticación y autorización para asegurar que solo los usuarios autorizados tengan acceso a las funcionalidades y datos pertinentes.

### 8.1. Autenticación (Login)

*   **Mecanismo:** Basado en `email` y `password`.
*   **Proceso:**
    1.  El usuario envía sus credenciales al endpoint `config/login.php`.
    2.  El backend verifica las credenciales contra la tabla `usuarios` en la base de datos.
    3.  Las contraseñas se almacenan con **hash (`password_hash`)** para mayor seguridad y se verifican con `password_verify`.
    4.  Si las credenciales son correctas:
        *   Para la interfaz web: Se inicia una **sesión PHP (`$_SESSION`)** para mantener el estado de autenticación del usuario. La información clave del usuario (ID, nombre, rol, sucursal) se almacena en la sesión.
        *   Para la aplicación móvil: El endpoint `login.php` devuelve una respuesta JSON con el indicador de éxito y los datos del usuario, incluyendo una URL completa de su foto de perfil si existe. La gestión del token de sesión o JWT (JSON Web Token) en la aplicación móvil dependerá de la implementación de Flutter, pero el backend ya está preparado para devolver los datos del usuario tras un login exitoso.

### 8.2. Manejo de Sesiones (Web)

*   **`Auth.php`:** La clase `Auth` (ubicada en `config/auth.php`) centraliza la lógica de autenticación.
*   **`isAuthenticated()`:** Método clave para verificar si un usuario está actualmente logueado consultando la variable de sesión `$_SESSION['logged_in']`.
*   **`getCurrentUser()`:** Retorna la información del usuario de la sesión actual (`$_SESSION`).
*   **Protección de Endpoints:** La mayoría de los archivos de la API (`categorias/api.php`, `clientes/api.php`, etc.) incluyen la siguiente lógica al inicio para asegurar que solo usuarios autenticados puedan acceder:

```php
// ...
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}
// ...
```

### 8.3. Autorización (Roles y Permisos)

*   **Roles:** Los permisos se gestionan a través de `roles`. Cada usuario está asociado a un `rol` (`rol_id` en la tabla `usuarios`).
*   **Tabla `roles`:** Almacena `nombre`, `descripcion` y un campo `permisos` (normalmente un JSON) que define las funcionalidades a las que puede acceder un rol.
*   **`config/modulos_por_rol.php`:** Este archivo contiene funciones para listar los módulos disponibles y gestionar la asignación de módulos a roles en la base de datos.
*   **Control de Acceso Fino:** El sistema permite definir qué módulos o secciones de la aplicación son accesibles para cada rol. Esta lógica se implementa verificando el rol del usuario actual en PHP antes de ejecutar acciones sensibles o renderizar partes de la interfaz.
    *   Ejemplo: En el módulo de gastos (`empleados/api_gastos.php`), la lógica diferencia entre un administrador y un usuario de sucursal para filtrar los datos mostrados.

### 8.4. Restablecimiento de Contraseña

*   **Mecanismo:** Utiliza tokens de un solo uso enviados por correo electrónico.
*   **Proceso:**
    1.  El usuario solicita restablecer la contraseña a través de `config/password-reset.php?action=solicitar`.
    2.  Se genera un token único y temporal, que se almacena en la base de datos y se envía al email del usuario.
    3.  El usuario recibe un enlace con el token y puede restablecer su contraseña en `config/password-reset.php?action=restablecer`.
    4.  El token se verifica para asegurar su validez y caducidad.

### 8.5. Seguridad en Contraseñas

*   **Hashing:** Todas las contraseñas se almacenan utilizando la función `password_hash()` de PHP con el algoritmo `PASSWORD_DEFAULT` (actualmente bcrypt), que es seguro contra ataques de fuerza bruta y Rainbow Tables.
*   **Verificación:** Las contraseñas ingresadas por los usuarios se verifican con `password_verify()`.

### 8.6. CORS (Cross-Origin Resource Sharing)

*   Para permitir que la aplicación móvil (Flutter) acceda a los endpoints de la API, se configuran los encabezados `Access-Control-Allow-Origin: *`, `Access-Control-Allow-Methods` y `Access-Control-Allow-Headers` en los archivos API relevantes (`config/login.php`, `config/get_user.php`, `sucursales/api.php` para la acción `disponibles`, `reportes/api_graficos.php`, etc.). Esto es esencial para la comunicación entre dominios diferentes.

### 8.7. Principio LIFO (Last In, First Out) en Consultas SQL

El sistema implementa el principio LIFO (Last In, First Out - "Último en Entrar, Primero en Salir") en múltiples consultas SQL para garantizar que los registros más recientes se procesen o muestren primero. Este enfoque mejora la experiencia del usuario y asegura la integridad en la generación de códigos secuenciales.

#### Casos de Uso Principales:

**1. Generación de Códigos Secuenciales**

El sistema utiliza LIFO para obtener el último código generado y así crear el siguiente número en la secuencia:

*   **Productos (`productos/api.php`, línea 136):**
    ```sql
    SELECT nombre 
    FROM productos 
    WHERE nombre REGEXP '^[0-9]{4}$'
    ORDER BY CAST(nombre AS UNSIGNED) DESC 
    LIMIT 1
    ```
    Obtiene el último código numérico (formato 0001, 0002, etc.) para generar el siguiente código de producto secuencial.

*   **Facturas de Compra (`compras/api.php`, línea 233):**
    ```sql
    SELECT numero_factura 
    FROM compras 
    WHERE numero_factura IS NOT NULL AND estado <> 'cancelada'
    ORDER BY id DESC
    ```
    Recupera el último número de factura de compra para generar el consecutivo.

*   **Facturas de Venta (`ventas/api.php`, línea 268):**
    ```sql
    SELECT numero_factura 
    FROM ventas 
    WHERE numero_factura IS NOT NULL AND estado <> 'cancelada'
    ORDER BY id DESC
    ```
    Recupera el último número de factura de venta para generar el consecutivo.

**2. Listados de Transacciones**

Las transacciones se ordenan mostrando primero las más recientes, utilizando dos criterios de ordenamiento:

*   **Compras (`compras/api.php`, línea 82):**
    ```sql
    ORDER BY c.fecha_compra DESC, c.id DESC
    ```
    Lista las compras ordenadas por fecha descendente, y en caso de empate, por ID descendente.

*   **Ventas (`ventas/api.php`, línea 125):**
    ```sql
    ORDER BY v.fecha_venta DESC, v.id DESC
    ```
    Lista las ventas mostrando primero las más recientes.

*   **Gastos Operativos (`empleados/api_gastos.php`, línea 84):**
    ```sql
    ORDER BY g.fecha DESC, g.id DESC
    ```
    Ordena los gastos del más reciente al más antiguo.

**3. Reportes y Visualizaciones**

*   **Reporte de Compras (`reportes/api.php` y `reportes/pdf.php`):**
    ```sql
    ORDER BY c.fecha_compra DESC
    ```
    Los reportes de compras muestran las transacciones más recientes al inicio.

*   **Reporte de Ventas (`reportes/api.php` y `reportes/pdf.php`):**
    ```sql
    ORDER BY v.fecha_venta DESC
    ```
    Los reportes de ventas priorizan la información más actual.

*   **Porcentajes de Categorías (`config/porcentajes_categorias.php`, línea 132):**
    ```sql
    ORDER BY cantidad_total DESC
    ```
    Ordena las categorías por cantidad total descendente, mostrando primero las de mayor volumen.

#### Tabla Resumen de Implementaciones LIFO:

| Archivo | Línea | Módulo | Propósito | Criterio de Ordenamiento |
|---------|-------|--------|-----------|-------------------------|
| `productos/api.php` | 136 | Productos | Generación de códigos | `CAST(nombre AS UNSIGNED) DESC` |
| `compras/api.php` | 82 | Compras | Listado de compras | `fecha_compra DESC, id DESC` |
| `compras/api.php` | 233 | Compras | Numeración de facturas | `id DESC` |
| `ventas/api.php` | 125 | Ventas | Listado de ventas | `fecha_venta DESC, id DESC` |
| `ventas/api.php` | 268 | Ventas | Numeración de facturas | `id DESC` |
| `empleados/api_gastos.php` | 84 | Gastos | Listado de gastos | `fecha DESC, id DESC` |
| `reportes/api.php` | 558 | Reportes | Reporte de compras | `fecha_compra DESC` |
| `reportes/api.php` | 691 | Reportes | Reporte de ventas | `fecha_venta DESC` |
| `config/porcentajes_categorias.php` | 132 | Estadísticas | Porcentajes de categorías | `cantidad_total DESC` |
| `usuarios/test_api.php` | 88 | Usuarios | Listado de usuarios | `id DESC` |

#### Ventajas del Enfoque LIFO:

1. **Experiencia de Usuario Mejorada:**
   - Los usuarios ven primero la información más relevante y reciente
   - Reduce el tiempo de búsqueda de transacciones actuales
   - Facilita el seguimiento de operaciones recientes

2. **Integridad de Datos:**
   - Garantiza la secuencialidad correcta en códigos de productos
   - Asegura la numeración consecutiva de facturas
   - Previene duplicados en la generación de identificadores

3. **Eficiencia en Consultas:**
   - Combinado con índices en las columnas `id` y `fecha_*`, las consultas son rápidas
   - La cláusula `LIMIT 1` en generación de códigos optimiza el rendimiento
   - El ordenamiento descendente aprovecha los índices automáticos de claves primarias

4. **Consistencia del Sistema:**
   - Todos los módulos principales siguen el mismo patrón de ordenamiento
   - Facilita el mantenimiento y comprensión del código
   - Proporciona una experiencia uniforme en toda la aplicación

#### Consideraciones Técnicas:

*   **Índices de Base de Datos:** El rendimiento de LIFO depende de índices adecuados en las columnas `id` (clave primaria, indexada automáticamente) y columnas de fecha (`fecha_compra`, `fecha_venta`, `fecha_creacion`).
*   **Concurrencia:** En la generación de códigos secuenciales, existe una pequeña ventana de concurrencia. El sistema maneja esto verificando duplicados antes de insertar y usando transacciones de base de datos.
*   **Paginación:** Para listados grandes, se recomienda implementar paginación en el frontend, manteniendo el ordenamiento LIFO por página.
 