default:
	@echo "Available commands up, down, ps, build, php, migrate_fresh, migrate, nginx, db"

up:
	@docker compose up -d

down:
	@docker compose down

ps:
	@docker compose ps

build:
	@docker compose build

php:
	@docker compose exec php bash

composer_i:
	@docker compose run php composer install

migrate_fresh:
	@docker compose run php php artisan migrate:fresh

migrate:
	@docker compose run php php artisan migrate

nginx:
	@docker compose exec nginx bash

db:
	@docker compose exec db bash

env:
	@cp .env.example .env
