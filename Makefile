#################################
#         Init Commands         #  setenforce Permissive
#################################
.PHONY: start
start: docker-down docker-pull docker-build docker-up

.PHONY: stop
stop: docker-down

.PHONY: exec
bash: docker-exec

.PHONY: up
up: docker-up

#################################
.PHONY: docker-up
docker-up:
	docker-compose up -d

.PHONY: ps
ps:
	docker-compose ps

.PHONY: ps
ps:
	docker-compose ps

.PHONY: docker-down
docker-down:
	docker-compose down --remove-orphans

.PHONY: docker-down-clear
docker-down-clear:
	docker-compose down -v --remove-orphans

.PHONY: docker-pull
docker-pull:
	docker-compose pull

.PHONY: docker-build
docker-build:
	docker-compose build

.PHONY: docker-exec
docker-exec:
	docker exec -ti zoho_php_room /bin/bash

.PHONY: docker-php-admin-exec # chmod -R 755 /etc/phpmyadmin/config.inc.php
docker-php-admin-exec:
	docker exec -ti zoho_phpmyadmin_room /bin/bash
#################################
