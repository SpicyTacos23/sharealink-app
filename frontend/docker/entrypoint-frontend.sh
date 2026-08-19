#!/bin/bash
set -e

sep() { echo "────────────────────────────────────────"; }

sep
echo "🎨 [FRONTEND] Iniciando contenedor frontend"
sep

echo "📦 [1/4] Compilando assets (yarn build)..."
yarn build
echo "    ✅ Assets compilados"

echo "🔧 [2/4] Ajustando permisos de var/..."
chown -R www-data:www-data var
chmod -R 775 var
echo "    ✅ Permisos ajustados"

echo "🧹 [3/4] Limpiando caché..."
php bin/console cache:clear --no-interaction
echo "    ✅ Caché limpiada"

echo "🌐 [4/4] Lanzando nginx + php-fpm vía supervisord..."
sep
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf