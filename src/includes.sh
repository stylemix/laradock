BASE_COMPOSE=./var/docker-compose.yml

function laradock_init_files() {
	OPTIONS=$1
	cp ${OPTIONS} resources/config.example.yml config.yml
	cp ${OPTIONS} resources/docker-compose.override.example.yml docker-compose.override.yml
	cp ${OPTIONS} resources/env.example .env
	cp ${OPTIONS} resources/my.example.cnf etc/my.cnf
	cp ${OPTIONS} resources/xdebug.example.ini etc/xdebug.ini
}

function laradock_generate() {
	docker run --rm -ti -v $PWD:/laradock -w /laradock php:7.2 php src/generate.php
}

function laradock_composer_install() {
	docker run --rm --interactive --tty \
		-v $PWD:/app \
		composer install --no-dev --no-progress --no-suggest
}

function laradock_compose() {
	OVERRIDE_COMPOSE=docker-compose.override.yml
	OVERRIDE_ARG=""
	[[ -f ${OVERRIDE_COMPOSE} ]] && OVERRIDE_ARG="-f ${OVERRIDE_COMPOSE}"
	docker-compose -f ${BASE_COMPOSE} ${OVERRIDE_ARG} --project-directory . ${@}
}

function laradock_info() {
	docker run --rm -ti -v $PWD:/laradock -w /laradock php:7.2 php src/info.php
}
