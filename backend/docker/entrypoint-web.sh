#!/bin/bash
set -e

sep() { echo "────────────────────────────────────────"; }

sep
echo "🚀 [BACKEND-WEB] Iniciando contenedor web"
sep

echo "🔑 [1/5] Generando claves JWT (si no existen)..."
php bin/console lexik:jwt:generate-keypair --skip-if-exists
echo "    ✅ Claves JWT listas"

echo "🗄️  [2/5] Ejecutando migraciones de base de datos..."
php bin/console doctrine:migrations:migrate --no-interaction
echo "    ✅ Migraciones aplicadas"

echo "📨 [3/5] Configurando tablas de transporte de Messenger..."
php bin/console messenger:setup-transports
echo "    ✅ Tablas messenger_messages (async, tmdb_sync, failed) listas"

echo "🧹 [4/5] Limpiando caché..."
php bin/console cache:clear --no-interaction
echo "    ✅ Caché limpiada"

echo "🌐 [5/5] Lanzando nginx + php-fpm vía supervisord..."
sep
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.web.conf