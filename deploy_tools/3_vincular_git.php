<?php
/**
 * HERRAMIENTA 3: VINCULAR REPOSITORIO (GIT INIT/CLONE)
 * Este script conecta la carpeta actual con el repositorio remoto.
 */
header('Content-Type: text/plain; charset=utf-8');

// --- CONFIGURACIÓN ---
// ¡CAMBIA ESTO POR LA URL SSH DE TU REPOSITORIO!
$repo_url = "git@github.com:Jordan-dito/sistema_reciclaje.git"; 
// ---------------------

echo "--- INICIALIZANDO DESPLIEGUE GIT ---\n";
echo "Repositorio: $repo_url\n\n";

if (!is_dir('.git')) {
    echo "1. Inicializando Git...\n";
    exec("git init 2>&1", $out);
    exec("git remote add origin $repo_url 2>&1", $out);
    echo "✅ Repositorio inicializado.\n";
} else {
    echo "1. Git ya estaba inicializado. Actualizando URL remota...\n";
    exec("git remote set-url origin $repo_url 2>&1", $out);
}

echo "\n2. Descargando historial (Fetch)...\n";
exec("git fetch origin main 2>&1", $out);

echo "\n3. Sincronizando archivos (Reset Mixed)...\n";
// Usamos mixed para no borrar archivos de configuración locales como config/database.php si no están trackeados
exec("git branch -M main 2>&1", $out);
exec("git reset --mixed origin/main 2>&1", $out);
exec("git branch --set-upstream-to=origin/main main 2>&1", $out);

echo implode("\n", $out);

echo "\n\n✅ ¡SISTEMA VINCULADO CORRECTAMENTE!\n";
echo "A partir de ahora, el archivo deploy.php recibirá los cambios automáticamente.";
?>
