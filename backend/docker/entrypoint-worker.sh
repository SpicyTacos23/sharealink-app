#!/bin/bash
set -e

sep() { echo "────────────────────────────────────────"; }

sep
echo "⚙️  [BACKEND-WORKER] Iniciando contenedor worker"
sep

echo "⏳ Esperando a que la base de datos tenga las tablas de Messenger..."
until php bin/console dbal:run-sql "SELECT 1 FROM messenger_messages LIMIT 1" > /dev/null 2>&1; do
  echo "    ...tabla messenger_messages aún no existe, reintentando en 2s"
  sleep 2
done
echo "    ✅ Tablas de Messenger disponibles"

echo "🎧 Lanzando consumers (async, tmdb_sync) vía supervisord..."
sep
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.worker.conf