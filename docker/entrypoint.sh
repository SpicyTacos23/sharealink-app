#!/bin/bash

echo "🎨 Compilando assets..."
yarn build || true

echo "� Ajustando permisos de var..."
chown -R www-data:www-data var || true
chmod -R 775 var || true

echo "�🗑️ Limpiando caché..."
php bin/console cache:clear --no-interaction || true

echo "🚀 Arrancando..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf