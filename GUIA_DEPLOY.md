# Guía de Despliegue Automático (AlwaysData + GitHub)

Esta guía explica cómo configurar el despliegue automático para que tu sitio se actualice solo cada vez que haces un `push` a la rama `main`.

## Requisitos Previos
*   Hosting con acceso SSH/PHP (AlwaysData, Hostinger, etc.).
*   Repositorio en GitHub.
*   Carpeta `deploy_tools/` subida al servidor.

---

## Paso 1: Generar Llave de Seguridad (RSA)
Necesitamos una "llave" para que el servidor pueda entrar a GitHub sin contraseña.

1.  Sube la carpeta `deploy_tools` a tu servidor.
2.  Abre en el navegador: `https://tusitio.com/deploy_tools/1_generar_llave.php`
3.  Copia el texto que aparece en la caja.
4.  Ve a **GitHub > Tu Repositorio > Settings > Deploy keys**.
5.  Haz clic en **Add deploy key**.
6.  Título: `Servidor Web`.
7.  Pega la llave.
8.  **IMPORTANTE:** Marca la casilla **Allow write access**.
9.  Guarda.

---

## Paso 2: Configurar y Probar Conexión
Ahora le diremos al servidor que use esa llave y probaremos si GitHub le deja entrar.

1.  Abre: `https://tusitio.com/deploy_tools/2_diagnostico.php`
2.  Debes ver el mensaje: `🎉 ¡CONEXIÓN EXITOSA!`.
3.  Si falla, revisa que hayas copiado bien la llave en el Paso 1.

---

## Paso 3: Vincular el Repositorio
Este paso conecta los archivos de tu servidor con el repositorio de GitHub.

1.  Abre: `https://tusitio.com/deploy_tools/3_vincular_git.php`
2.  Debes ver: `✅ ¡SISTEMA VINCULADO CORRECTAMENTE!`.

> **Nota:** Si usas este script en otro proyecto, abre el archivo `3_vincular_git.php` y cambia la variable `$repo_url` por la de tu nuevo repositorio.

---

## Paso 4: Configurar el Webhook (Automático)
Para que GitHub avise al servidor cuando hay cambios:

1.  Ve a **GitHub > Settings > Webhooks**.
2.  Haz clic en **Add webhook**.
3.  **Payload URL:** `https://tusitio.com/deploy.php`
4.  **Content type:** `application/json`.
5.  **Secret:** `Barcelona1925` (O el que hayas puesto en `deploy.php`).
6.  Haz clic en **Add webhook**.

---

## Paso 5: Limpieza (Seguridad)
Una vez que todo funcione, **BORRA** la carpeta `deploy_tools` del servidor. Esas herramientas son poderosas y no deben quedar públicas.

¡Listo! Ahora cada `git push` actualizará tu sitio en segundos.
