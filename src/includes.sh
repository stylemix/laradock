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
	docker run --rm -ti -v $PWD:/laradock -w /laradock --net bridge azamatx/laradock-workspace bash src/docker-ip.sh > var/docker-host-ip
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

laradock_xdebug_status() {
	PHP_FPM_CONTAINER="php-fpm-${1}"
    echo "Checking Xdebug status in ${PHP_FPM_CONTAINER}..."
	laradock_compose exec ${PHP_FPM_CONTAINER} php -v
}

laradock_xdebug_start() {
	PHP_FPM_CONTAINER="php-fpm-${1}"
	echo "Starting Xdebug in ${PHP_FPM_CONTAINER}..."

	CMD=(sed -i 's/^;zend_extension=/zend_extension=/g' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini)
	laradock_compose exec ${PHP_FPM_CONTAINER} ${CMD[@]} && \
	laradock_compose restart ${PHP_FPM_CONTAINER} && \
	laradock_compose exec ${PHP_FPM_CONTAINER} php -v
}

laradock_xdebug_stop() {
	PHP_FPM_CONTAINER="php-fpm-${1}"
    echo "Stopping Xdebug in ${PHP_FPM_CONTAINER}..."

    CMD=(sed -i 's/^zend_extension=/;zend_extension=/g' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini)
	laradock_compose exec ${PHP_FPM_CONTAINER} ${CMD[@]} && \
	laradock_compose restart ${PHP_FPM_CONTAINER} && \
	laradock_compose exec ${PHP_FPM_CONTAINER} php -v
}

laradock_xdebug() {
	case $1 in
		stop|STOP)
			laradock_xdebug_stop $2
			;;
		start|START)
			laradock_xdebug_start $2
			;;
		status|STATUS)
			laradock_xdebug_status $2
			;;
		*)
			echo "Xdebug must have already been installed."
			echo "Usage:"
			echo "$ laradock xdebug stop|start|status <version>"
			echo "where <version> - 56 | 70 | 71 | 72 | 73 | 74"
	esac

	exit 1
}
