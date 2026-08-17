#!/bin/bash
set -e

echo "🚀 Backend iniciando..."

# SOLO tareas locales del contenedor
php bin/console cache:clear --no-interaction

echo "🚀 Lanzando supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf