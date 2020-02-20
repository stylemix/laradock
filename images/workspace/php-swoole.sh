#!/usr/bin/env bash
VERSION=$1
CURRENT=$(readlink /etc/alternatives/php)
CURRENT=${CURRENT##\/usr\/bin\/php}

set_php() {
	update-alternatives --set php /usr/bin/php$1
	update-alternatives --set php-config /usr/bin/php-config$1
	update-alternatives --set phpize /usr/bin/phpize$1
}
set_php $VERSION

pecl uninstall -r swoole

if [[ ${VERSION} = "5.6" ]]; then
    pecl -d php_suffix=${VERSION} -q install swoole-2.0.10;
elif [[ ${VERSION} = "7.0" ]]; then
    pecl -d php_suffix=${VERSION} install swoole-2.2.0;
else
    pecl -d php_suffix=${VERSION} install swoole;
fi

echo "extension=swoole.so" >> /etc/php/${VERSION}/mods-available/swoole.ini;
ln -s /etc/php/${VERSION}/mods-available/swoole.ini /etc/php/${VERSION}/cli/conf.d/20-swoole.ini
php${VERSION} -m | grep swoole

set_php $CURRENT
