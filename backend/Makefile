APP_NAME=mediabridge-backend

.PHONY: up down restart logs shell \
	schema-create schema-drop fixtures validate-schema \
	migrations-diff migrations-migrate \
	reset-db install wait-db

### 🚢 DOCKER BÁSICO ###
up:
	@echo "🚀 Levantando contenedores..."
	docker compose up -d --build

down:
	@echo "🛑 Apagando contenedores..."
	docker compose down

restart: down up

logs:
	docker compose logs -f backend

shell:
	docker compose exec backend sh

### 🧠 BASE DE DATOS (DESARROLLO) ###
schema-create:
	@echo "⚠️  Creando/actualizando esquema desde entidades (solo desarrollo)..."
	docker compose exec backend php bin/console doctrine:schema:update --force

schema-drop:
	@echo "💣 Eliminando esquema de base de datos..."
	docker compose exec backend php bin/console doctrine:schema:drop --force --full-database

fixtures:
	@echo "🌱 Cargando fixtures..."
	docker compose exec backend php bin/console doctrine:fixtures:load --no-interaction --append

validate-schema:
	@echo "🔍 Validando esquema vs mapping..."
	docker compose exec backend php bin/console doctrine:schema:validate

### 🧱 MIGRACIONES ###
migrations-diff:
	@echo "📝 Generando migraciones (diff)..."
	docker compose exec backend php bin/console doctrine:migrations:diff

migrations-migrate:
	@echo "📦 Ejecutando migraciones..."
	docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction

### 🔄 RESET COMPLETO (DESARROLLO) ###
reset-db:
	@echo "💣 Reseteando base de datos (DROP + CREATE + FIXTURES)..."
	docker compose exec backend php bin/console doctrine:database:drop --force --if-exists
	docker compose exec backend php bin/console doctrine:database:create
	$(MAKE) schema-create
	$(MAKE) fixtures
	@echo "✅ Base de datos reconstruida."

### 🧪 ENTORNO COMPLETO ###
install: up wait-db schema-create fixtures
	@echo "🎉 Entorno listo."

### ⏳ ESPERAR A MYSQL ###
wait-db:
	@echo "⏳ Esperando a MySQL..."
	@until docker compose exec mysql mariadb-admin ping -uroot -proot --silent 2>/dev/null; do \
		echo "  ... reintentando en 2s"; \
		sleep 2; \
	done
	@echo "⏳ MySQL responde, esperando conexiones..."
	@sleep 3
	@echo "✅ MySQL listo."
