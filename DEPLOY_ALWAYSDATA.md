# Guía de Despliegue Automático en AlwaysData

Esta guía te ayudará a configurar el despliegue automático desde GitHub a AlwaysData.

## Opción 1: Usando Git en AlwaysData (Recomendado)

### Paso 1: Conectar tu servidor AlwaysData con GitHub

1. **Accede a tu servidor AlwaysData por SSH:**
   ```bash
   ssh tu-usuario@ssh-hermanosyanez.alwaysdata.net
   ```

2. **Navega a tu directorio web:**
   ```bash
   cd ~/www
   ```

3. **Clona tu repositorio (si aún no lo has hecho):**
   ```bash
   git clone https://github.com/Jordan-dito/sistema_reciclaje.git
   ```

4. **O si ya tienes archivos, inicializa git:**
   ```bash
   cd sistema_reciclaje  # o el nombre de tu carpeta
   git init
   git remote add origin https://github.com/Jordan-dito/sistema_reciclaje.git
   git pull origin main
   ```

### Paso 2: Configurar el Webhook en GitHub

1. Ve a tu repositorio en GitHub: `https://github.com/Jordan-dito/sistema_reciclaje`
2. Ve a **Settings** > **Webhooks** > **Add webhook**
3. Configura:
   - **Payload URL**: `https://hermanosyanez.alwaysdata.net/deploy.php`
   - **Content type**: `application/json`
   - **Secret**: Genera un secreto seguro (guárdalo)
   - **Events**: Selecciona "Just the push event"
   - **Active**: ✓ Activado

4. Haz clic en **Add webhook**

### Paso 3: Configurar el Script de Despliegue

1. **Sube el archivo `deploy.php` a tu servidor AlwaysData:**
   - Puedes subirlo por FTP o por SSH
   - Debe estar en la raíz de tu proyecto: `~/www/sistema_reciclaje/deploy.php`

2. **Edita `deploy.php` y actualiza el secreto:**
   ```php
   $webhook_secret = 'TU_SECRETO_AQUI'; // Cambia por el secreto que configuraste en GitHub
   ```

3. **Asegúrate de que el archivo tenga permisos de ejecución:**
   ```bash
   chmod 755 deploy.php
   ```

### Paso 4: Probar el Despliegue

1. Haz un cambio pequeño en tu repositorio
2. Haz commit y push:
   ```bash
   git add .
   git commit -m "Test deploy"
   git push origin main
   ```

3. Verifica el log:
   ```bash
   tail -f deploy.log
   ```

## Opción 2: Usando GitHub Actions (Alternativa)

Si AlwaysData no permite git o prefieres otra solución, puedes usar GitHub Actions.

### Crear el workflow

Crea el archivo `.github/workflows/deploy.yml`:

```yaml
name: Deploy to AlwaysData

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Deploy to AlwaysData via FTP
      uses: SamKirkland/FTP-Deploy-Action@4.0.0
      with:
        server: ftp-hermanosyanez.alwaysdata.net
        username: ${{ secrets.FTP_USERNAME }}
        password: ${{ secrets.FTP_PASSWORD }}
        local-dir: ./
        server-dir: /www/
        exclude: |
          **/.git*
          **/.git*/**
          **/node_modules/**
          **/.env
          **/deploy.log
```

### Configurar Secrets en GitHub

1. Ve a tu repositorio: **Settings** > **Secrets** > **Actions**
2. Agrega:
   - `FTP_USERNAME`: Tu usuario FTP de AlwaysData
   - `FTP_PASSWORD`: Tu contraseña FTP de AlwaysData

## Opción 3: Despliegue Manual con Script

Si prefieres un despliegue manual pero automatizado, crea un script local:

### `deploy-local.sh` (para Windows, usa Git Bash o WSL)

```bash
#!/bin/bash

echo "🚀 Iniciando despliegue..."

# Hacer commit de cambios locales
git add .
git commit -m "Deploy: $(date +%Y-%m-%d\ %H:%M:%S)"
git push origin main

echo "✅ Cambios subidos a GitHub"
echo "⏳ Esperando despliegue automático..."
echo "📋 Revisa el log en: ssh tu-usuario@ssh-hermanosyanez.alwaysdata.net 'tail -f ~/www/sistema_reciclaje/deploy.log'"
```

## Verificación del Despliegue

### Ver logs en tiempo real:
```bash
ssh tu-usuario@ssh-hermanosyanez.alwaysdata.net
tail -f ~/www/sistema_reciclaje/deploy.log
```

### Verificar que los archivos se actualizaron:
```bash
ssh tu-usuario@ssh-hermanosyanez.alwaysdata.net
cd ~/www/sistema_reciclaje
git log -1
```

## Solución de Problemas

### Error: "Git no disponible"
- AlwaysData puede tener git deshabilitado en algunos planes
- Solución: Usa la Opción 2 (GitHub Actions) o contacta a AlwaysData

### Error: "Permisos denegados"
```bash
chmod 755 deploy.php
chmod -R 755 ~/www/sistema_reciclaje
```

### Error: "Webhook no funciona"
- Verifica que la URL sea accesible públicamente
- Revisa los logs de AlwaysData
- Verifica que el secreto coincida en GitHub y deploy.php

### El despliegue no se ejecuta
1. Verifica que el webhook esté activo en GitHub
2. Revisa los "Recent Deliveries" en GitHub Webhooks
3. Verifica el log: `tail -f deploy.log`

## Seguridad

⚠️ **IMPORTANTE:**
- Nunca subas el archivo `.env` a GitHub
- Usa un secreto fuerte para el webhook
- Limita el acceso al archivo `deploy.php` si es posible
- Considera agregar autenticación adicional al webhook

## Archivos a Excluir del Despliegue

Asegúrate de que estos archivos NO se suban:
- `.env` (contiene credenciales)
- `deploy.log` (log local)
- Archivos temporales
- `node_modules/` (si usas npm)

Agrega al `.gitignore`:
```
.env
deploy.log
*.log
node_modules/
```

## Comandos Útiles

```bash
# Ver estado de git en el servidor
git status

# Ver últimos commits
git log --oneline -10

# Forzar actualización manual
git fetch origin
git reset --hard origin/main

# Ver permisos de archivos
ls -la
```

---

**¿Necesitas ayuda?** Revisa la documentación de AlwaysData o contacta a su soporte.

