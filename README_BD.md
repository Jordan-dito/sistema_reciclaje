# Sistema de Gestión de Reciclaje - Base de Datos
## Tesis de Grado

### 📋 Estructura del Proyecto

```
tesis-reciclaje/
├── .env                          # Configuración de credenciales (CREAR MANUALMENTE)
├── config/
│   ├── database.php             # Conexión a base de datos
│   ├── auth.php                 # Sistema de autenticación
│   └── generate_passwords.php   # Generador de hashes
├── database.sql                 # Script SQL completo
├── index.php                    # Página de login
└── Dashboard.php                # Dashboard principal
```

---

## 🚀 Instalación y Configuración

### Paso 1: Crear el archivo .env

Crea un archivo llamado `.env` en la raíz del proyecto con el siguiente contenido:

```env
# Configuración de Base de Datos - Sistema de Reciclaje
DB_HOST=localhost
DB_PORT=3306
DB_NAME=sistema_reciclaje
DB_USER=root
DB_PASS=

# Configuración de la Aplicación
APP_NAME=Sistema de Gestión de Reciclaje
APP_ENV=development
APP_DEBUG=true

# Configuración de Sesión
SESSION_LIFETIME=120
```

**IMPORTANTE:** 
- Ajusta `DB_USER` y `DB_PASS` según tus credenciales de MySQL
- No subas este archivo a control de versiones (añádelo al `.gitignore`)

### Paso 2: Importar la Base de Datos

1. Abre phpMyAdmin o tu cliente MySQL preferido
2. Importa el archivo `database.sql`
3. O ejecuta en la línea de comandos:

```bash
mysql -u root -p < database.sql
```

### Paso 3: Verificar la Conexión

Crea un archivo de prueba `test_connection.php`:

```php
<?php
require_once 'config/database.php';

try {
    $db = getDB();
    echo "✓ Conexión exitosa a la base de datos\n";
    
    // Probar una consulta
    $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    echo "✓ Usuarios en la base de datos: " . $result['total'] . "\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
```

Ejecuta: `php test_connection.php`

---

## 👤 Usuarios por Defecto

Después de importar la base de datos, tendrás estos usuarios:

### Administrador
- **Email:** `admin@sistema.com`
- **Contraseña:** `Admin123!`
- **Rol:** Administrador

### Usuario Normal
- **Email:** `usuario@sistema.com`
- **Contraseña:** `Usuario123!`
- **Rol:** Usuario

**⚠️ IMPORTANTE:** Cambia estas contraseñas después del primer inicio de sesión.

---

## 📊 Estructura de la Base de Datos

### Tablas Principales

1. **roles**
   - Almacena los roles del sistema (administrador, usuario)
   - Incluye permisos en formato JSON

2. **usuarios**
   - Información de los usuarios
   - Relación con roles mediante `rol_id`
   - Control de intentos de login y bloqueo

3. **sesiones**
   - Sesiones activas de los usuarios
   - Soporte para "Recordarme"
   - Tokens de sesión

4. **logs_actividad**
   - Registro de todas las actividades del sistema
   - Auditoría completa

### Vistas

- `v_usuarios_completos`: Usuarios con información del rol
- `v_actividades_recientes`: Últimas 100 actividades

### Procedimientos Almacenados

- `limpiar_sesiones_expiradas()`: Limpia sesiones antiguas
- `registrar_actividad()`: Registra una actividad en el log

---

## 🔐 Sistema de Autenticación

### Uso Básico

```php
<?php
require_once 'config/auth.php';

$auth = new Auth();

// Login
$resultado = $auth->login('admin@sistema.com', 'Admin123!', false);
if ($resultado['success']) {
    echo "Login exitoso";
}

// Verificar autenticación
if ($auth->isAuthenticated()) {
    $usuario = $auth->getCurrentUser();
    echo "Usuario: " . $usuario['nombre'];
}

// Verificar permisos
if ($auth->hasPermission('usuarios', 'crear')) {
    echo "Tiene permiso para crear usuarios";
}

// Logout
$auth->logout();
```

### Proteger una Página

```php
<?php
require_once 'config/auth.php';

// Requerir autenticación
$auth = requireAuth();

// Requerir permiso específico
requirePermission('usuarios', 'crear');
```

---

## 🔧 Generar Nuevas Contraseñas

Si necesitas generar nuevos hashes de contraseñas:

```bash
php config/generate_passwords.php
```

O en PHP:

```php
echo password_hash('tu_contraseña', PASSWORD_DEFAULT);
```

---

## 📝 Notas Importantes

1. **Seguridad:**
   - Las contraseñas se almacenan con `password_hash()` de PHP
   - Las sesiones tienen tiempo de expiración configurable
   - Los intentos de login fallidos bloquean la cuenta (5 intentos)

2. **Desarrollo:**
   - Con `APP_DEBUG=true` verás errores detallados
   - En producción, cambiar a `APP_DEBUG=false`

3. **Base de Datos:**
   - Usa MySQL 5.7+ o MariaDB 10.2+
   - Requiere charset `utf8mb4` para soportar emojis

4. **Permisos:**
   - Los permisos están en formato JSON en la tabla `roles`
   - Los administradores tienen todos los permisos automáticamente

---

## 🛠️ Solución de Problemas

### Error: "El archivo .env no existe"
- Crea manualmente el archivo `.env` en la raíz del proyecto

### Error de conexión a la base de datos
- Verifica que MySQL esté corriendo
- Revisa las credenciales en `.env`
- Asegúrate de que la base de datos `sistema_reciclaje` exista

### Usuario bloqueado
- Ejecuta: `UPDATE usuarios SET estado='activo', intentos_login=0, fecha_bloqueo=NULL WHERE email='tu@email.com';`

---

## 📚 Documentación Adicional

Para más información sobre el sistema, consulta:
- Archivo `config/database.php` - Configuración de conexión
- Archivo `config/auth.php` - Sistema de autenticación completo
- Archivo `database.sql` - Estructura completa de la base de datos

---

**Sistema desarrollado para Tesis de Grado**

