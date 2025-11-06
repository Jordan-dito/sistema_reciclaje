# Guía de Instalación para Hosting
## Sistema de Gestión de Reciclaje

### 📋 Requisitos del Hosting

- PHP 8.1 o superior
- MySQL 5.7+ o MariaDB 10.2+
- Soporte para sesiones PHP
- Extensión PDO habilitada
- Extensión JSON habilitada

---

### 🚀 Pasos de Instalación

#### 1. Subir archivos al servidor

Sube todos los archivos del proyecto a tu servidor web (usando FTP, cPanel File Manager, Git, etc.).

**IMPORTANTE**: NO subas el archivo `.env` al servidor si contiene credenciales de desarrollo.

#### 2. Crear el archivo `.env` en el servidor

En el servidor, crea un archivo llamado `.env` en la raíz del proyecto (al mismo nivel que `index.php`).

Puedes usar el archivo `.env.example` como plantilla:

```bash
# Copiar el archivo de ejemplo
cp .env.example .env
```

Luego edita el archivo `.env` con las credenciales de tu hosting:

```env
# Configuración de Base de Datos del Hosting
DB_HOST=localhost
DB_PORT=3306
DB_NAME=tu_nombre_bd
DB_USER=tu_usuario_bd
DB_PASS=tu_contraseña_bd

# Configuración de la Aplicación
APP_NAME=Sistema de Gestión de Reciclaje
APP_ENV=production
APP_DEBUG=false

# Configuración de Sesión
SESSION_LIFETIME=120
```

**⚠️ IMPORTANTE**: 
- Reemplaza `tu_nombre_bd`, `tu_usuario_bd` y `tu_contraseña_bd` con las credenciales reales de tu base de datos
- En producción, establece `APP_DEBUG=false` para ocultar errores
- Establece `APP_ENV=production` para producción

#### 3. Crear la base de datos

**Opción A: Usando phpMyAdmin**
1. Accede a phpMyAdmin desde tu panel de control (cPanel, Plesk, etc.)
2. Crea una nueva base de datos
3. Importa el archivo `database.sql`
   - Selecciona la base de datos creada
   - Ve a la pestaña "Importar"
   - Selecciona `database.sql` y haz clic en "Continuar"

**Opción B: Usando línea de comandos (SSH)**
```bash
mysql -u tu_usuario_bd -p tu_nombre_bd < database.sql
```

**⚠️ IMPORTANTE**: 
- El nombre de la base de datos debe coincidir con `DB_NAME` en tu archivo `.env`
- Si cambias el nombre de la base de datos en el SQL, asegúrate de actualizar también `DB_NAME` en `.env`

#### 4. Configurar permisos de archivos

Asegúrate de que los permisos de archivos sean correctos:

```bash
# Archivos PHP: 644
chmod 644 *.php

# Directorios: 755
chmod 755 config/
chmod 755 usuarios/
# ... etc para otros directorios

# .env: 600 (solo lectura/escritura para el propietario)
chmod 600 .env
```

#### 5. Verificar configuración

1. Accede a tu sitio web: `https://tudominio.com/`
2. Deberías ver la página de login
3. Inicia sesión con las credenciales por defecto:
   - Email: `admin@sistema.com`
   - Contraseña: `Admin123!`

**⚠️ IMPORTANTE**: Cambia estas contraseñas inmediatamente después del primer inicio de sesión.

---

### 🔒 Seguridad para Producción

1. **Archivo .env**: 
   - Asegúrate de que `.env` tenga permisos 600
   - Verifica que `.env` esté en `.gitignore` (no debe subirse al repositorio)

2. **APP_DEBUG**: 
   - Siempre establece `APP_DEBUG=false` en producción
   - Esto evita que se muestren errores que puedan exponer información sensible

3. **APP_ENV**: 
   - Establece `APP_ENV=production` en producción
   - Esto activa configuraciones más seguras

4. **Contraseñas**: 
   - Cambia todas las contraseñas por defecto
   - Usa contraseñas fuertes para la base de datos

5. **HTTPS**: 
   - Configura SSL/HTTPS en tu servidor
   - Esto protege las credenciales durante el login

---

### 🐛 Solución de Problemas

#### Error: "El archivo .env no existe"
- Verifica que el archivo `.env` existe en la raíz del proyecto
- Verifica los permisos del archivo

#### Error: "Error de conexión a la base de datos"
- Verifica que las credenciales en `.env` sean correctas
- Verifica que el servidor MySQL esté corriendo
- Verifica que el nombre de la base de datos coincida entre `.env` y `database.sql`

#### Error 500 (Internal Server Error)
- Verifica los logs de error de PHP en tu hosting
- Verifica que `APP_DEBUG=true` temporalmente para ver el error exacto
- Verifica que todas las extensiones PHP requeridas estén habilitadas

#### Página en blanco
- Verifica los logs de error del servidor
- Verifica que PHP esté configurado correctamente
- Verifica que el archivo `.env` tenga el formato correcto (sin espacios extra, una variable por línea)

---

### 📞 Soporte

Si encuentras problemas durante la instalación:
1. Revisa los logs de error de PHP
2. Verifica que todos los requisitos se cumplan
3. Asegúrate de que el archivo `.env` esté configurado correctamente

---

### ✅ Listo para Usar

Una vez completados estos pasos, tu sistema estará listo para usar en producción.

**Recuerda**: 
- Cambiar las contraseñas por defecto
- Configurar HTTPS
- Mantener `APP_DEBUG=false` en producción
- Hacer backups regulares de la base de datos

