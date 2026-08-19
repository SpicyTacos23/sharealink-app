APP_NAME=sharealink-app

.PHONY: up up-backend up-frontend up-infra \
	down down-backend down-frontend \
	restart restart-backend restart-frontend \
	logs logs-backend logs-worker logs-frontend \
	shell-backend shell-worker shell-frontend \
	schema-create schema-drop fixtures validate-schema \
	migrations-diff migrations-migrate \
	reset-db install wait-db wait-backend check-connection

### 🚢 DOCKER BÁSICO (STACK COMPLETO) ###
up:
	@echo "🚀 Levantando todo el stack (infra + backend + worker + frontend)..."
	docker compose up -d --build
	@$(MAKE) wait-backend
	@$(MAKE) wait-frontend
	@echo "🎉 Stack completo arriba: frontend (http://localhost:8080) + backend (http://localhost:8081)"

down:
	@echo "🛑 Apagando todos los contenedores..."
	docker compose down

restart: down up

### 🧩 STACK POR PARTES ###
up-infra:
	@echo "🧱 Levantando solo infraestructura (mysql + redis)..."
	docker compose up -d mysql redis

up-backend: up-infra
	@echo "⚙️  Levantando backend + worker..."
	docker compose up -d --build backend worker
	@$(MAKE) wait-backend
	@echo "✅ Backend disponible en http://localhost:8081"

up-frontend:
	@echo "🎨 Levantando frontend (requiere backend ya corriendo)..."
	docker compose up -d --build frontend
	@$(MAKE) wait-frontend
	@echo "✅ Frontend disponible en http://localhost:8080"

down-backend:
	@echo "🛑 Apagando backend + worker..."
	docker compose stop backend worker

down-frontend:
	@echo "🛑 Apagando frontend..."
	docker compose stop frontend

restart-backend: down-backend up-backend
restart-frontend: down-frontend up-frontend

### 📋 LOGS ###
logs:
	docker compose logs -f

logs-backend:
	docker compose logs -f backend

logs-worker:
	docker compose logs -f worker

logs-frontend:
	docker compose logs -f frontend

### 🐚 SHELLS ###
shell-backend:
	docker compose exec backend sh

shell-worker:
	docker compose exec worker sh

shell-frontend:
	docker compose exec frontend sh

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

### ⏳ ESPERAR AL BACKEND (para health real, no solo el contenedor) ###
wait-backend:
	@echo "⏳ Esperando a que el backend responda en /api/doc..."
	@until docker compose exec backend curl -sf http://localhost/api/doc > /dev/null 2>&1; do \
		echo "  ... backend aún no responde, reintentando en 2s"; \
		sleep 2; \
	done
	@echo "✅ Backend listo y respondiendo."

### 🔗 CONECTAR FRONT Y BACK ###
check-connection: up-backend wait-backend up-frontend
	@echo "🔎 Verificando que el frontend puede alcanzar al backend..."
	@docker compose exec frontend curl -sf http://backend/api/doc > /dev/null \
		&& echo "✅ Frontend y backend se comunican correctamente (http://backend)" \
		|| (echo "❌ El frontend NO puede alcanzar al backend. Revisa la red de docker compose." && exit 1)
	@echo "🎉 Stack conectado: frontend (http://localhost:8080) ↔ backend (http://localhost:8081)"


### ⏳ ESPERAR AL BACKEND (para health real, no solo el contenedor) ###
wait-backend:
	@echo "⏳ Esperando a que el backend responda en /api/doc..."
	@until docker compose exec backend curl -sf http://localhost/api/doc > /dev/null 2>&1; do \
		echo "  ... backend aún no responde, reintentando en 2s"; \
		sleep 2; \
	done
	@echo "✅ Backend listo y respondiendo."

### ⏳ ESPERAR AL FRONTEND (assets compilados + cache limpia + nginx arriba) ###
wait-frontend:
	@echo "⏳ Esperando a que el frontend termine build de assets y responda..."
	@until docker compose exec frontend curl -sf http://localhost/ > /dev/null 2>&1; do \
		echo "  ... frontend aún no responde (¿compilando assets?), reintentando en 2s"; \
		sleep 2; \
	done
	@echo "✅ Frontend listo y respondiendo."
