# 📦 Lista de Archivos para Subir al Hosting

## ✅ ARCHIVOS QUE SÍ DEBES SUBIR

### Archivos principales
- ✅ `index.php` - Página de login
- ✅ `Dashboard.php` - Panel principal
- ✅ `register.php` - Registro de usuarios
- ✅ `database.sql` - **Base de datos completa**

### Directorio config/
- ✅ `config/auth.php`
- ✅ `config/database.php`
- ✅ `config/login.php`
- ✅ `config/logout.php`
- ✅ `config/register.php`

### Directorios de módulos
- ✅ `usuarios/` - Todo el directorio
- ✅ `sucursales/` - Todo el directorio
- ✅ `inventarios/` - Todo el directorio
- ✅ `clientes/` - Todo el directorio
- ✅ `proveedores/` - Todo el directorio
- ✅ `compras/` - Todo el directorio
- ✅ `ventas/` - Todo el directorio
- ✅ `roles/` - Todo el directorio

### Assets (recursos)
- ✅ `assets/` - **Todo el directorio completo** (CSS, JS, imágenes, fuentes)

### Documentación (opcional, pero recomendado)
- ✅ `README.md`
- ✅ `INSTALACION.md`
- ✅ `.env.example` - **IMPORTANTE: Para que otros sepan qué configurar**
- ✅ `.gitignore`

---

## ❌ ARCHIVOS QUE NO DEBES SUBIR

### Archivos sensibles
- ❌ `.env` - **NUNCA subir este archivo** (contiene credenciales)
- ❌ `.env.local`
- ❌ Cualquier archivo que contenga contraseñas o credenciales

### Archivos de desarrollo
- ❌ `config/generate_passwords.php` - Solo para desarrollo local
- ❌ `database_update.sql` - Ya no existe, fue eliminado

### Archivos de sistema
- ❌ `.git/` - Si usas Git, no subas el directorio .git
- ❌ `.DS_Store` (Mac)
- ❌ `Thumbs.db` (Windows)
- ❌ `*.log` - Archivos de log

### Archivos de prueba
- ❌ `test_connection.php` - Si existe, no subirlo

---

## 📋 CHECKLIST ANTES DE SUBIR

Antes de subir al hosting, verifica:

### 1. Archivo .env
- [ ] **NO incluyas** el archivo `.env` en los archivos a subir
- [ ] Verifica que `.env` esté en `.gitignore`
- [ ] Crea el `.env` directamente en el servidor después de subir los archivos

### 2. Base de datos
- [ ] Tienes el archivo `database.sql` listo para importar
- [ ] Sabes el nombre de la base de datos que usarás en el hosting
- [ ] Tienes las credenciales de MySQL del hosting

### 3. Configuración
- [ ] El archivo `.env.example` está presente para referencia
- [ ] Todos los archivos PHP están presentes
- [ ] El directorio `assets/` está completo

---

## 🚀 PASOS DESPUÉS DE SUBIR

1. **Crear el archivo `.env` en el servidor**
   - Copia `.env.example` a `.env`
   - Completa con las credenciales de tu hosting

2. **Importar la base de datos**
   - Accede a phpMyAdmin
   - Importa `database.sql`
   - Verifica que el nombre de la BD coincida con `DB_NAME` en `.env`

3. **Verificar permisos**
   - Archivos PHP: 644
   - Directorios: 755
   - `.env`: 600 (solo lectura para el propietario)

4. **Probar el sistema**
   - Accede a `https://tudominio.com/`
   - Inicia sesión con: `admin@sistema.com` / `Admin123!`
   - **Cambia la contraseña inmediatamente**

---

## ⚠️ IMPORTANTE

- **NUNCA** subas el archivo `.env` con credenciales
- **Siempre** crea el `.env` directamente en el servidor
- **Verifica** que `.env` esté en `.gitignore` antes de hacer commit
- **Cambia** las contraseñas por defecto después del primer login

---

## ✅ TODO LISTO PARA SUBIR

Si has verificado todos los puntos anteriores, **¡tu proyecto está listo para subir al hosting!**

