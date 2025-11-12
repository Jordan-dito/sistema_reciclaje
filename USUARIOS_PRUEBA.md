# 👤 Usuarios para Pruebas
## Sistema de Gestión de Reciclaje

---

## 🔐 Credenciales de Acceso

### 1. **Administrador del Sistema** (Acceso Completo)
- **Email:** `admin@sistema.com`
- **Contraseña:** `Admin123!`
- **Rol:** Administrador
- **Acceso a módulos:**
  - ✅ Dashboard
  - ✅ Usuarios
  - ✅ Sucursales
  - ✅ Inventarios
  - ✅ Clientes
  - ✅ Proveedores
  - ✅ Compras
  - ✅ Ventas
  - ✅ Reportes
  - ✅ Configuración

---

### 2. **Gerente del Sistema** (Acceso Operativo)
- **Email:** `gerente@sistema.com`
- **Contraseña:** `Usuario123!`
- **Rol:** Gerente
- **Acceso a módulos:**
  - ✅ Dashboard
  - ✅ Sucursales
  - ✅ Inventarios
  - ✅ Clientes
  - ✅ Proveedores
  - ✅ Compras
  - ✅ Ventas
  - ✅ Reportes
  - ❌ Usuarios (sin acceso)
  - ❌ Configuración (sin acceso)

---

### 3. **Usuario Normal** (Acceso Limitado)
- **Email:** `usuario@sistema.com`
- **Contraseña:** `Usuario123!`
- **Rol:** Usuario
- **Acceso a módulos:**
  - ✅ Dashboard
  - ✅ Inventarios
  - ✅ Reportes
  - ❌ Usuarios (sin acceso)
  - ❌ Sucursales (sin acceso)
  - ❌ Clientes (sin acceso)
  - ❌ Proveedores (sin acceso)
  - ❌ Compras (sin acceso)
  - ❌ Ventas (sin acceso)
  - ❌ Configuración (sin acceso)

---

## 📝 Notas Importantes

1. **Cambiar contraseñas:** Se recomienda cambiar estas contraseñas después del primer inicio de sesión en producción.

2. **Crear nuevos usuarios:** Puedes crear nuevos usuarios desde el módulo de **Usuarios** (solo disponible para Administradores).

3. **Permisos:** Los permisos están configurados en la tabla `rol_modulos` de la base de datos.

---

## 🚀 Cómo Iniciar Sesión

1. Abre el navegador y ve a: `http://localhost/tesis reciclaje/` (o la URL de tu servidor)
2. Ingresa el email y contraseña de cualquiera de los usuarios anteriores
3. Haz clic en "Iniciar Sesión"

---

## 🔧 Si las contraseñas no funcionan

Si las contraseñas no funcionan, puedes regenerarlas ejecutando:

```bash
php config/generate_passwords.php
```

O actualizar directamente en la base de datos usando los hashes generados.

---

**Última actualización:** Base de datos `database.sql`

