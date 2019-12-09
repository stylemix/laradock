#!/usr/bin/env bash
docker build -t azamatx/laradock-nginx:latest ./nginx
docker build -t azamatx/laradock-php-fpm:7.1 --build-arg PHP_VERSION=7.1 ./php-fpm
docker build -t azamatx/laradock-php-fpm:7.2 --build-arg PHP_VERSION=7.2 ./php-fpm
docker build -t azamatx/laradock-php-fpm:7.3 --build-arg PHP_VERSION=7.3 ./php-fpm
docker build -t azamatx/laradock-mariadb:latest ./mariadb
docker build -t azamatx/laradock-workspace:latest ./workspace

#docker push azamatx/laradock-nginx:latest
#docker push azamatx/laradock-php-fpm:7.1
#docker push azamatx/laradock-mariadb:latest
#docker push azamatx/laradock-workspace:latest
