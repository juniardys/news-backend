init-start:
	docker-compose up -d --build
	make fresh-db
	fetch-news

start:
	docker-compose up -d

stop:
	docker-compose stop

restart:
	make stop
	make start

build:
	docker-compose up -d --build

rebuild:
	make down
	make build

down:
	docker-compose down

fresh-db:
	docker exec news-backend-app bash -c 'php artisan migrate:fresh --seed --force'
	docker exec news-backend-app bash -c 'php artisan passport:install'

fetch-news:
	docker exec news-backend-app bash -c 'php artisan python:fetch-sources-and-categories'
	docker exec news-backend-app bash -c 'php artisan python:fetch-news'
