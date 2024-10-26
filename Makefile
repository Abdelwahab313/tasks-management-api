# Check if container is running
IS_RUNNING := $(shell docker inspect task-api-app 2>/dev/null | grep "Running" | xargs | sed "s/.$$//")
RUNNING := Running: true
$(if $(IS_RUNNING), $(info ### Task API container ${IS_RUNNING} ###), $(info ### Task API container Running: false ###))

# Main setup command
setup: build up migrate install

# Docker commands
build:
	docker compose build --pull

up:
	docker compose up -d

down:
	docker compose down

destroy:
	docker compose down -v

logs:
	docker compose logs --follow $(filter-out $@,$(MAKECMDGOALS))

# Conditional Laravel commands
install:
ifeq ($(IS_RUNNING), $(RUNNING))
	docker compose exec app composer install --no-cache --optimize-autoloader --prefer-dist
	docker compose exec app composer validate --strict
	docker compose exec app php artisan optimize:clear
else
	docker compose run --rm app composer install --no-cache --optimize-autoloader --prefer-dist
	docker compose run --rm app composer validate --strict
	docker compose run --rm app php artisan optimize:clear
endif

bash:
ifeq ($(IS_RUNNING), $(RUNNING))
	docker compose exec app bash
else
	docker compose run --rm app bash
endif

artisan:
ifeq ($(IS_RUNNING), $(RUNNING))
	docker compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))
else
	docker compose run --rm app php artisan $(filter-out $@,$(MAKECMDGOALS))
endif

migrate:
ifeq ($(IS_RUNNING), $(RUNNING))
	docker compose exec app php artisan migrate $(filter-out $@,$(MAKECMDGOALS))
else
	docker compose run --rm app php artisan migrate $(filter-out $@,$(MAKECMDGOALS))
endif

fresh:
ifeq ($(IS_RUNNING), $(RUNNING))
	docker compose exec app php artisan migrate:fresh --seed
else
	docker compose run --rm app php artisan migrate:fresh --seed
endif

test:
ifeq ($(IS_RUNNING), $(RUNNING))
	docker compose exec app php artisan test  $(filter-out $@,$(MAKECMDGOALS))
else
	docker compose run --rm app php artisan test  $(filter-out $@,$(MAKECMDGOALS))
endif

# Development helpers
make-controller:
ifeq ($(IS_RUNNING), $(RUNNING))
	docker compose exec app php artisan make:controller $(filter-out $@,$(MAKECMDGOALS))
else
	docker compose run --rm app php artisan make:controller $(filter-out $@,$(MAKECMDGOALS))
endif

make-model:
ifeq ($(IS_RUNNING), $(RUNNING))
	docker compose exec app php artisan make:model $(filter-out $@,$(MAKECMDGOALS))
else
	docker compose run --rm app php artisan make:model $(filter-out $@,$(MAKECMDGOALS))
endif

make-migration:
ifeq ($(IS_RUNNING), $(RUNNING))
	docker compose exec app php artisan make:migration $(filter-out $@,$(MAKECMDGOALS))
else
	docker compose run --rm app php artisan make:migration $(filter-out $@,$(MAKECMDGOALS))
endif

# Cache commands
cache-clear:
ifeq ($(IS_RUNNING), $(RUNNING))
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear
else
	docker compose run --rm app php artisan cache:clear
	docker compose run --rm app php artisan config:clear
	docker compose run --rm app php artisan route:clear
	docker compose run --rm app php artisan view:clear
endif

.PHONY: setup build up down destroy logs install bash artisan migrate fresh test make-controller make-model make-migration cache-clear
