# Arquitectura del Sistema de Gestión de Reciclaje

## Descripción General

Este documento explica de forma sencilla cómo está construido el Sistema de Gestión de Reciclaje, para que cualquier persona pueda entender su funcionamiento.

## Diagrama de Arquitectura

![Arquitectura del Sistema](assets/arquitectura_sistema_reciclaje.png)

## ¿Qué es este sistema?

Es una aplicación web que ayuda a gestionar empresas de reciclaje, permitiendo:
- Registrar compras de materiales reciclables
- Gestionar ventas de productos
- Controlar inventarios por sucursal
- Administrar clientes y proveedores
- Generar reportes financieros

## Componentes del Sistema (Explicación Simple)

### 🖥️ CAPA 1: PRESENTACIÓN (Lo que ven los usuarios)

**Panel Web de Administración**
- Es la página web donde los empleados y administradores trabajan
- Se accede desde cualquier navegador (Chrome, Firefox, Edge)
- Tecnologías: HTML (estructura), CSS/Bootstrap (diseño), jQuery (interactividad)

**App Móvil Flutter** (Planeada/En desarrollo)
- Aplicación para teléfonos Android e iOS
- Permite consultar reportes y datos desde cualquier lugar
- Ideal para gerentes que necesitan información en tiempo real

### 🔌 CAPA 2: API REST (El mensajero)

**¿Qué es una API?**
Es como un "mesero" que recibe pedidos (solicitudes) y trae respuestas. Permite que el Panel Web y la App Móvil se comuniquen con el sistema.

**Endpoints disponibles:**
- **Usuarios**: Login, registro, cambio de contraseña
- **Clientes**: Registrar y gestionar clientes
- **Proveedores**: Administrar proveedores de materiales
- **Productos**: Catálogo de productos reciclables
- **Inventarios**: Control de stock por sucursal
- **Compras**: Registrar compras a proveedores
- **Ventas**: Registrar ventas a clientes
- **Reportes**: Generar gráficos y estadísticas
- **Sucursales**: Gestionar diferentes ubicaciones

**Formato de comunicación:** HTTP/JSON
- HTTP: Protocolo estándar de internet
- JSON: Formato de texto simple para intercambiar datos

### ⚙️ CAPA 3: LÓGICA DE NEGOCIO (El cerebro)

Aquí es donde ocurre toda la "magia" del sistema. Es el código PHP que:

**Autenticación**
- Verifica que los usuarios sean quienes dicen ser
- Usa contraseñas encriptadas (hash) para seguridad
- Mantiene sesiones activas mientras trabajas

**Autorización (Roles)**
- Define qué puede hacer cada tipo de usuario
- Ejemplo: Un cajero puede vender, pero no puede eliminar productos
- Roles comunes: Administrador, Gerente, Cajero, Vendedor

**Validaciones**
- Verifica que los datos sean correctos antes de guardarlos
- Ejemplo: Valida que un RUC ecuatoriano sea válido
- Evita errores y datos incorrectos

**Gestión de Transacciones**
- Maneja compras, ventas y gastos
- Actualiza automáticamente el inventario
- Controla el saldo de caja de cada sucursal

**Generación de Reportes**
- Crea estadísticas y gráficos
- Calcula totales, ganancias y márgenes
- Exporta datos en PDF

**Tecnologías utilizadas:**
- **PDO**: Para conectarse de forma segura a la base de datos
- **Composer**: Gestor de librerías PHP
- **PHPMailer**: Para enviar correos (recuperación de contraseña)

### 💾 CAPA 4: BASE DE DATOS (El almacén)

**MySQL**
Es donde se guarda toda la información del sistema de forma organizada.

**Tablas principales:**
- **usuarios**: Información de empleados y administradores
- **roles**: Permisos y niveles de acceso
- **sucursales**: Datos de cada local o punto de venta
- **clientes**: Personas o empresas que compran
- **proveedores**: Personas o empresas que venden materiales
- **productos**: Catálogo de materiales reciclables
- **inventarios**: Cantidad de productos en cada sucursal
- **compras**: Registro de compras realizadas
- **ventas**: Registro de ventas realizadas
- **gastos**: Gastos operativos de cada sucursal

## Flujo de Trabajo (Ejemplo: Registrar una Venta)

1. **Usuario** abre el Panel Web y hace clic en "Nueva Venta"
2. **Panel Web** envía una solicitud HTTP a la API REST
3. **API REST** recibe la solicitud y la pasa al Backend PHP
4. **Backend PHP**:
   - Verifica que el usuario tenga permiso para vender
   - Valida que haya suficiente stock
   - Calcula el total de la venta
   - Actualiza el inventario (resta productos)
   - Actualiza el saldo de caja (suma dinero)
   - Guarda la venta en la base de datos
5. **MySQL** almacena toda la información
6. **Backend PHP** envía una respuesta de éxito
7. **API REST** devuelve la respuesta al Panel Web
8. **Panel Web** muestra un mensaje: "¡Venta registrada exitosamente!"

## Características Especiales del Sistema

### 🔒 Seguridad
- **Hash de contraseñas**: Las contraseñas nunca se guardan en texto plano
- **Sesiones**: Mantiene tu sesión activa mientras trabajas
- **Validación de datos**: Verifica que toda la información sea correcta
- **CORS habilitado**: Permite que la app móvil se conecte de forma segura

### 📊 Principio LIFO (Last In, First Out)
- "Último en entrar, primero en salir"
- Los registros más recientes aparecen primero
- Facilita encontrar las transacciones del día
- Genera códigos secuenciales correctamente

### 🗑️ Soft Delete
- Los registros no se eliminan realmente
- Se marcan como "inactivos" o "cancelados"
- Permite recuperar información si es necesario
- Mantiene el historial completo

### ✅ Validaciones Inteligentes
- **RUC/Cédula**: Verifica que sean válidos según las reglas ecuatorianas
- **Email**: Comprueba que tenga formato correcto
- **Teléfono**: Valida números de teléfono
- **Stock**: Evita vender más de lo que hay disponible
- **Saldo**: No permite gastos mayores al saldo disponible

## Ventajas de esta Arquitectura

### ✨ Modular
- Cada parte del sistema es independiente
- Fácil de mantener y actualizar
- Se puede mejorar una parte sin afectar las demás

### 🔄 Escalable
- Puede crecer según las necesidades del negocio
- Se pueden agregar nuevas sucursales sin problemas
- Soporta múltiples usuarios simultáneos

### 📱 Multiplataforma
- Funciona en cualquier navegador web
- Compatible con computadoras, tablets y teléfonos
- La app móvil funcionará en Android e iOS

### 🛡️ Segura
- Protege la información sensible
- Control de acceso por roles
- Auditoría de quién hace qué

### 🚀 Rápida
- Consultas optimizadas a la base de datos
- Respuestas en formato JSON ligero
- Interfaz moderna y responsiva

## Tecnologías Utilizadas (Resumen)

| Componente | Tecnología | Propósito |
|------------|-----------|-----------|
| Frontend Web | HTML, CSS, Bootstrap, jQuery | Interfaz de usuario |
| App Móvil | Flutter (Dart) | Aplicación para teléfonos |
| Backend | PHP 7.x/8.x | Lógica del negocio |
| Base de Datos | MySQL | Almacenamiento de datos |
| API | REST (JSON) | Comunicación entre capas |
| Servidor Web | Apache (XAMPP) | Servidor local/producción |
| Gráficos | ECharts, Chart.js | Visualización de datos |
| Correos | PHPMailer | Envío de emails |

## Requisitos del Sistema

### Para Desarrollo Local
- **XAMPP** (incluye Apache, MySQL, PHP)
- **Navegador web** moderno (Chrome, Firefox, Edge)
- **Editor de código** (VS Code, Sublime Text, etc.)
- **Git** (para control de versiones)

### Para Producción
- **Servidor web** con PHP 7.x o superior
- **MySQL** 5.7 o superior
- **Certificado SSL** (para HTTPS)
- **Dominio** propio (opcional)

## Glosario de Términos

- **API**: Interfaz de Programación de Aplicaciones - permite que diferentes programas se comuniquen
- **Backend**: La parte del sistema que no ves, donde ocurre el procesamiento
- **Frontend**: La parte visual del sistema con la que interactúas
- **Base de Datos**: Lugar donde se guarda toda la información de forma organizada
- **Endpoint**: Una dirección específica de la API que realiza una función
- **JSON**: Formato de texto para intercambiar datos entre sistemas
- **HTTP**: Protocolo de comunicación en internet
- **CRUD**: Create (Crear), Read (Leer), Update (Actualizar), Delete (Eliminar)
- **PDO**: PHP Data Objects - forma segura de conectarse a bases de datos
- **Hash**: Transformación de una contraseña en un código irreversible
- **Sesión**: Período en el que un usuario está activo en el sistema
- **Rol**: Conjunto de permisos que define qué puede hacer un usuario
- **Soft Delete**: Marcar como eliminado sin borrar realmente
- **LIFO**: Last In, First Out - último en entrar, primero en salir

## Contacto y Soporte

Para más información técnica, consulta:
- **Manual Técnico**: `MANUAL_TECNICO.md`
- **Guía de Despliegue**: `GUIA_DEPLOY.md`
- **Documentación de API**: Revisar archivos `api.php` en cada módulo

---

**Versión del documento**: 1.0  
**Última actualización**: Febrero 2026  
**Sistema**: Gestión de Reciclaje - Tesis de Grado
