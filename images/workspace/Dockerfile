FROM phusion/baseimage:latest

# Set Environment Variables
ENV DEBIAN_FRONTEND noninteractive
RUN locale-gen en_US.UTF-8
ENV LANGUAGE=en_US.UTF-8
ENV LC_ALL=en_US.UTF-8
ENV LC_CTYPE=en_US.UTF-8
ENV LANG=en_US.UTF-8
ENV TERM xterm
ARG TZ=UTC
ENV TZ ${TZ}

# Start as root
USER root

###########################################################################
# Laradock non-root user:
###########################################################################

# Add a non-root user to prevent files being created with root permissions on host machine.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN set -xe; \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone && \
    groupadd -g ${PGID} laradock && \
    useradd -u ${PUID} -g laradock -m laradock -G docker_env && \
    usermod -p "*" laradock -s /bin/bash && \
    # always run apt update when start and after add new source list, then clean up at end.
    apt-get install -yqq software-properties-common && \
    add-apt-repository -y ppa:ondrej/php && \
    apt-get update -yqq
RUN apt-get install -yqq --allow-downgrades --allow-remove-essential --allow-change-held-packages \
      apt-utils \
      libzip-dev \
      zip \
      unzip \
      inetutils-ping \
      mysql-client \
      supervisor \
      procps \
      nasm \
      pkg-config \
      libcurl4-openssl-dev \
      libedit-dev \
      libssl-dev \
      libxml2-dev \
      xz-utils \
      libsqlite3-dev \
      sqlite3 \
      git \
      curl \
      vim \
      nano \
      postgresql-client \
      php7.1-cli \
      php7.1-common \
      php7.1-curl \
      php7.1-intl \
      php7.1-json \
      php7.1-xml \
      php7.1-mbstring \
      php7.1-mcrypt \
      php7.1-mysql \
      php7.1-pgsql \
      php7.1-sqlite \
      php7.1-sqlite3 \
      php7.1-zip \
      php7.1-bcmath \
      php7.1-memcached \
      php7.1-gd \
      php7.1-dev \
      php7.1-zip \
      php7.1-xdebug
RUN pecl channel-update pecl.php.net && \
    curl -s http://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar | bash && \
    chmod +x /usr/local/bin/wp
RUN apt-get install -yqq --allow-downgrades --allow-remove-essential --allow-change-held-packages \
      php7.2-cli \
      php7.2-common \
      php7.2-curl \
      php7.2-intl \
      php7.2-json \
      php7.2-xml \
      php7.2-mbstring \
      php7.2-mysql \
      php7.2-pgsql \
      php7.2-sqlite \
      php7.2-sqlite3 \
      php7.2-zip \
      php7.2-bcmath \
      php7.2-memcached \
      php7.2-gd \
      php7.2-dev \
      php7.2-xdebug

# Source the bash
RUN . ~/.bashrc

COPY aliases.sh /root/aliases.sh
COPY aliases.sh /home/laradock/aliases.sh
COPY insecure_id_rsa /tmp/id_rsa
COPY insecure_id_rsa.pub /tmp/id_rsa.pub
COPY composer.json /home/laradock/.composer/composer.json
COPY supervisord.conf /etc/supervisord.conf
COPY entrypoint.sh /entrypoint.sh

###########################################################################
# ssh:
###########################################################################

RUN rm -f /etc/service/sshd/down && \
    cat /tmp/id_rsa.pub >> /root/.ssh/authorized_keys && \
    cat /tmp/id_rsa.pub >> /root/.ssh/id_rsa.pub && \
    cat /tmp/id_rsa >> /root/.ssh/id_rsa && \
    rm -f /tmp/id_rsa* && \
    chmod 644 /root/.ssh/authorized_keys /root/.ssh/id_rsa.pub && \
    chmod 400 /root/.ssh/id_rsa && \
    cp -rf /root/.ssh /home/laradock && \
    chown -R laradock:laradock /home/laradock/.ssh && \
    chmod 755 /entrypoint.sh

# Make sure that ~/.composer belongs to laradock
RUN chown -R laradock:laradock /home/laradock/.composer && \
    echo "" >> ~/.bashrc && \
    echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.bashrc && \
    sed -i 's/\r//' /root/aliases.sh && \
    sed -i 's/\r//' /home/laradock/aliases.sh && \
    chown laradock:laradock /home/laradock/aliases.sh && \
    echo "" >> ~/.bashrc && \
    echo "# Load Custom Aliases" >> ~/.bashrc && \
    echo "source ~/aliases.sh" >> ~/.bashrc && \
	  echo "" >> ~/.bashrc

USER laradock

RUN echo "" >> ~/.bashrc && \
    echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.bashrc && \
    echo "" >> ~/.bashrc && \
    echo "# Load Custom Aliases" >> ~/.bashrc && \
    echo "source ~/aliases.sh" >> ~/.bashrc && \
	  echo "" >> ~/.bashrc && \
    composer global require laravel/installer laravel/envoy:~1.0 --no-progress

# Switch to root
USER root

RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

# Set default work directory
WORKDIR /var/www

CMD ["/entrypoint.sh"]
