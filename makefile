composer-install:
	docker-compose exec app composer install

composer-update:
	docker-compose exec app composer update

composer-outdated:
	docker-compose -f docker-compose.yml exec app composer outdated --direct

docker-build:
	docker-compose -f docker-compose.yml up --build -d --remove-orphans

docker-start: | docker-up migrations-migrate

docker-start-xdebug: | docker-up-xdebug migrations-migrate

docker-stop:
	docker-compose -f docker-compose.yml stop

docker-down:
	docker-compose -f docker-compose.yml down

docker-restart: | docker-stop docker-start

docker-restart-xdebug: | docker-stop docker-start-xdebug

docker-stop-containers:
	docker stop $$(docker ps -q)

docker-up:
	docker-compose -f docker-compose.yml up -d --remove-orphans

docker-up-xdebug:
	. ./.scripts/xdebug-network-alias.sh
	docker-compose -f docker-compose.yml -f  docker-compose.dev.xdebug.yml up -d --remove-orphans

git-prune-origin:
	git fetch --prune origin

git-prune-local:
	git fetch --all
	git checkout master
	git pull
	git branch -vv | grep ': gone]' | awk '{print $1}' | xargs git branch -d

login:
	docker-compose -f docker-compose.yml exec app bash

migrations-diff:
	docker-compose exec app bin/console make:migration

migrations-migrate:
	docker-compose exec app bin/console doctrine:migrations:migrate --no-interaction

php-cs:
	docker-compose exec app composer cs

php-csf:
	docker-compose exec app composer csf

php-md:
	docker-compose exec app composer phpmd

php-stan:
	docker-compose exec app composer phpstan

php-psalm:
	docker-compose exec app composer psalm

test:
	docker-compose exec app composer test

test-unit:
	docker-compose exec app composer test:unit

test-coverage:
	docker-compose -f docker-compose.yml -f  docker-compose.dev.pcov.yml up -d --remove-orphans
	docker-compose exec app composer test:coverage
	open var/testcoverage/index.html
	make docker-start