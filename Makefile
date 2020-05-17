APP_DIR := $(abspath $(lastword $(MAKEFILE_LIST)))

build: env hooks dependencies start build start database
reload: stop start

env:
	sudo apt install php7.4-fpm php7.4-common php7.4-curl php7.4-json php7.4-pgsql php7.4-sqlite php7.4-dom php7.4-mbstring php-xdebug
	sudo apt install nginx

dependencies:
	symfony composer install
start:
	docker-compose -f docker-compose.yaml -f docker-compose.override.yaml up -d
	symfony server:start --daemon
	sleep 5

stop:
	docker-compose kill; docker-compose rm -f
	symfony server:stop

database:
	-symfony console doctrine:database:drop --force
	symfony console doctrine:database:create
	symfony console doctrine:migration:migrate -n
	symfony console doctrine:fixtures:load -n

test:
	symfony console doctrine:schema:update --force --env=test
	symfony console doctrine:fixtures:load -n --env=test
	symfony php bin/phpunit

git_hooks:
	chmod +x hooks/syntax-checkup.sh
	rm -f .git/hooks/pre-commit
	rm -f .git/hooks/pre-push
	ln -s hooks/syntax-checkup.sh .git/hooks/pre-commit
	ln -s hooks/syntax-checkup.sh .git/hooks/pre-push

jwt:
	mkdir -p config/jwt
	openssl genrsa -out config/jwt/private.pem -aes256 4096
	openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
	openssl rsa -in config/jwt/private.pem -out config/jwt/private2.pem
	mv config/jwt/private2.pem config/jwt/private.pem
	chmod 700 config/jwt/*
