@echo off

set CMD=%1
set COMPOSE_FILE=.\var\docker-compose.yml
set PWD=%~dp0%
set PWD=%PWD:\=/%

IF /I %CMD% == init (
	docker run --rm --interactive --tty -v %PWD%:/app composer install --no-dev --no-progress --no-suggest
	copy /-y resources\config.example.yml config.yml
	copy /-y resources\env.example .env
	copy /-y resources\my.example.cnf etc\my.cnf
	copy /-y resources\xdebug.example.ini etc\xdebug.ini

	echo Laradock initialized!
)

if /I %CMD% == up (
	docker run --rm -ti -v %PWD%:/laradock -w /laradock php:7.2 php src/generate.php
	docker-compose -f %COMPOSE_FILE% --project-directory . up -d --remove-orphans %2 %3 %4 %5 %6 %7 %8 %9
	docker run --rm -ti -v %PWD%:/laradock -w /laradock php:7.2 php src/info.php
)

if /I %CMD% == info (
	docker run --rm -ti -v %PWD%:/laradock -w /laradock php:7.2 php src/info.php
)

if /I %CMD% == ssh (
	docker-compose -f %COMPOSE_FILE% --project-directory . exec -u laradock workspace bash
)

if /I %CMD% == root (
	docker-compose -f %COMPOSE_FILE% --project-directory . exec -u root workspace bash
)

if /I %CMD% == down (
	docker-compose -f %COMPOSE_FILE% --project-directory . down %2 %3 %4 %5 %6 %7 %8 %9
)

if /I %CMD% == ps (
	docker-compose -f %COMPOSE_FILE% --project-directory . ps %2 %3 %4 %5 %6 %7 %8 %9
)

if /I %CMD% == logs (
	docker-compose -f %COMPOSE_FILE% --project-directory . logs %2 %3 %4 %5 %6 %7 %8 %9
)

if /I %CMD% == exec (
	docker-compose -f %COMPOSE_FILE% --project-directory . exec %2 %3 %4 %5 %6 %7 %8 %9
)

if /I %CMD% == restart (
	docker-compose -f %COMPOSE_FILE% --project-directory . restart %2 %3 %4 %5 %6 %7 %8 %9
)

if /I %CMD% == rebuild (
	docker run --rm -ti -v %PWD%:/laradock -w /laradock php:7.2 php src/generate.php
	docker-compose -f %COMPOSE_FILE% --project-directory . build --pull --force-rm
	docker-compose -f %COMPOSE_FILE% --project-directory . up -d --remove-orphans
)

if /I %CMD% == upgrade (
	git fetch
	git reset --hard origin/master
	docker run --rm --interactive --tty -v %PWD%:/app composer install --no-dev --no-progress --no-suggest
	.\laradock.bat rebuild
	echo "Laradock upgraded!"
)
