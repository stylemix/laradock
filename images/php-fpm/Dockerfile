ARG PHP_VERSION
FROM laradock/php-fpm:2.5-${PHP_VERSION}

# Set Environment Variables
ENV DEBIAN_FRONTEND noninteractive

# Copy configuration files
COPY php.ini /usr/local/etc/php/php.ini
COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY laravel.ini /usr/local/etc/php/conf.d
COPY xlaravel.pool.conf /usr/local/etc/php-fpm.d/

# always run apt update when start and after add new source list, then clean up at end.
RUN set -xe; \
    apt-get update -yqq && \
    pecl channel-update pecl.php.net && \
    apt-get install -yqq \
      apt-utils \
      #
      #--------------------------------------------------------------------------
      # Mandatory Software's Installation
      #--------------------------------------------------------------------------
      #
      # Mandatory Software's such as ("mcrypt", "pdo_mysql", "libssl-dev", ....)
      # are installed on the base image 'laradock/php-fpm' image. If you want
      # to add more Software's or remove existing one, you need to edit the
      # base image (https://github.com/Laradock/php-fpm).
      #
      # next lines are here becase there is no auto build on dockerhub see https://github.com/laradock/laradock/pull/1903#issuecomment-463142846
      libzip-dev zip unzip && \
    docker-php-ext-configure zip --with-libzip && \
    docker-php-ext-install zip pcntl bcmath mysqli opcache && \
    # Install intl and requirements
    apt-get install -y zlib1g-dev libicu-dev g++ && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl && \
    # Install yaml and requirements
    apt-get install libyaml-dev -y && \
    pecl install yaml && \
    docker-php-ext-enable yaml && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    # Extension is disbaled initially and is can be enabled by xdebug script
    sed -i "s/^zend_extension=/;zend_extension=/g" /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

#
#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------
#

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

RUN usermod -u 1000 www-data

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
