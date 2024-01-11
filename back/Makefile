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

flogs:
	@docker compose logs --follow

php:
	@docker compose exec php bash

composer_i:
	@docker compose run php composer install

migrate_fresh:
	@docker compose run php php artisan migrate:fresh

migrate:
	@docker compose run php php artisan migrate

seed:
	@docker compose run php php artisan db:seed

list:
	@docker compose run php php artisan list

link:
	@docker compose run php php artisan storage:link

show:
	@docker compose run php php artisan db:show

nginx:
	@docker compose exec nginx bash

db:
	@docker compose exec db bash

env:
	@cp .env.example .env
