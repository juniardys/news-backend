init-start:
	docker-compose up -d --build
	docker exec news-backend-app bash -c 'composer install --optimize-autoloader'
	docker exec news-backend-app bash -c 'php artisan key:generate'
	docker exec news-backend-app bash -c 'php artisan optimize:clear'
	docker exec news-backend-app bash -c 'php artisan python:install-requirements'
	docker exec news-backend-app bash -c 'php artisan migrate:fresh --seed --force'
	docker exec news-backend-app bash -c 'php artisan passport:install'
	docker exec news-backend-app bash -c 'php artisan python:fetch-sources-and-categories'
	docker exec news-backend-app bash -c 'php artisan python:fetch-news'

start:
	docker-compose up -d

stop:
	docker-compose stop

restart:
	docker-compose stop
	docker-compose up -d

build:
	docker-compose up -d --build
	docker exec news-backend-app bash -c 'composer install --optimize-autoloader'
	docker exec news-backend-app bash -c 'php artisan key:generate'
	docker exec news-backend-app bash -c 'php artisan optimize:clear'
	docker exec news-backend-app bash -c 'php artisan python:install-requirements'

rebuild:
	docker-compose down
	docker-compose up -d --build
	docker exec news-backend-app bash -c 'composer install --optimize-autoloader'
	docker exec news-backend-app bash -c 'php artisan key:generate'
	docker exec news-backend-app bash -c 'php artisan optimize:clear'
	docker exec news-backend-app bash -c 'php artisan python:install-requirements'

down:
	docker-compose down

fresh-db:
	docker exec news-backend-app bash -c 'php artisan migrate:fresh --seed --force'
	docker exec news-backend-app bash -c 'php artisan passport:install'

fetch-news:
	docker exec news-backend-app bash -c 'php artisan python:fetch-sources-and-categories'
	docker exec news-backend-app bash -c 'php artisan python:fetch-news'
