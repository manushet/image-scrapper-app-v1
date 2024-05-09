docker-up:
	docker compose up --build -d

docker-exec-php:
	docker exec -it image-scrapper-v2-php_fpm-1 bash

docker-down:
	docker compose down --remove-orphans