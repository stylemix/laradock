FROM azamatx/laradock-workspace

ARG CHANGE_SOURCE=false
ARG UBUNTU_SOURCE
COPY ./sources.sh /tmp/sources.sh

RUN if [ ${CHANGE_SOURCE} = true ]; then \
    chmod +x /tmp/sources.sh && \
    /bin/sh -c /tmp/sources.sh && \
    rm -rf /tmp/sources.sh \
;fi

ARG INSTALL_PHP_56=false
ARG INSTALL_PHP_70=false
ARG INSTALL_PHP_71=false
ARG INSTALL_PHP_72=false
ARG INSTALL_PHP_73=false
ARG INSTALL_OH_MY_ZSH=false

RUN if [ ${INSTALL_PHP_56} = true ] \
    || [ ${INSTALL_PHP_70} = true ] \
    || [ ${INSTALL_PHP_71} = true ] \
    || [ ${INSTALL_PHP_72} = true ] \
    || [ ${INSTALL_PHP_73} = true ] \
    || [ ${INSTALL_OH_MY_ZSH} = true ] \
    ; then \
     apt-get update -yq \
;fi

###########################################################################
# Oh-my-zsh
###########################################################################
RUN if [ ${INSTALL_OH_MY_ZSH} = true ]; then \
    apt-get install -yq zsh \
    && zsh --version \
    && chsh -s $(which zsh) laradock \
;fi

RUN if [ ${INSTALL_OH_MY_ZSH} = true ]; then \
    sh -c "ZSH=/usr/local/oh-my-zsh $(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh) --unattended" \
;fi

ENV INSTALL_PHP_56 ${INSTALL_PHP_56}
RUN if [ ${INSTALL_PHP_56} = true ]; then \
  apt-get install -yq --allow-downgrades --allow-remove-essential --allow-change-held-packages \
    php5.6-bcmath \
    php5.6-cli \
    php5.6-common \
    php5.6-curl \
    php5.6-dev \
    php5.6-gd \
    php5.6-intl \
    php5.6-json \
    php5.6-mbstring \
    php5.6-mcrypt \
    php5.6-memcached \
    php5.6-mysql \
    php5.6-pgsql \
    php5.6-sqlite \
    php5.6-sqlite3 \
    php5.6-zip \
    php5.6-xdebug \
    php5.6-xml \
;fi

ENV INSTALL_PHP_70 ${INSTALL_PHP_70}
RUN if [ ${INSTALL_PHP_70} = true ]; then \
  apt-get install -yq --allow-downgrades --allow-remove-essential --allow-change-held-packages \
    php7.0-bcmath \
    php7.0-cli \
    php7.0-common \
    php7.0-curl \
    php7.0-dev \
    php7.0-gd \
    php7.0-intl \
    php7.0-json \
    php7.0-mbstring \
    php7.0-mcrypt \
    php7.0-memcached \
    php7.0-mysql \
    php7.0-pgsql \
    php7.0-sqlite \
    php7.0-sqlite3 \
    php7.0-zip \
    php7.0-xdebug \
    php7.0-xml \
;fi

ENV INSTALL_PHP_71 ${INSTALL_PHP_71}
RUN if [ ${INSTALL_PHP_71} = true ]; then \
  apt-get install -yq --allow-downgrades --allow-remove-essential --allow-change-held-packages \
    php7.1-bcmath \
    php7.1-cli \
    php7.1-common \
    php7.1-curl \
    php7.1-dev \
    php7.1-gd \
    php7.1-intl \
    php7.1-json \
    php7.1-mbstring \
    php7.1-mcrypt \
    php7.1-memcached \
    php7.1-mysql \
    php7.1-pgsql \
    php7.1-sqlite \
    php7.1-sqlite3 \
    php7.1-zip \
    php7.1-xdebug \
    php7.1-xml \
;fi

ENV INSTALL_PHP_72 ${INSTALL_PHP_72}
RUN if [ ${INSTALL_PHP_72} = true ]; then \
  apt-get install -yq --allow-downgrades --allow-remove-essential --allow-change-held-packages \
    php7.2-bcmath \
    php7.2-cli \
    php7.2-common \
    php7.2-curl \
    php7.2-dev \
    php7.2-gd \
    php7.2-intl \
    php7.2-json \
    php7.2-mbstring \
    php7.2-memcached \
    php7.2-mysql \
    php7.2-pgsql \
    php7.2-sqlite \
    php7.2-sqlite3 \
    php7.2-zip \
    php7.2-xdebug \
    php7.2-xml \
;fi

ENV INSTALL_PHP_73 ${INSTALL_PHP_73}
RUN if [ ${INSTALL_PHP_73} = true ]; then \
  apt-get install -yq --allow-downgrades --allow-remove-essential --allow-change-held-packages \
    php7.3-bcmath \
    php7.3-cli \
    php7.3-common \
    php7.3-curl \
    php7.3-dev \
    php7.3-gd \
    php7.3-intl \
    php7.3-json \
    php7.3-mbstring \
    php7.3-memcached \
    php7.3-mysql \
    php7.3-pgsql \
    php7.3-sqlite \
    php7.3-sqlite3 \
    php7.3-zip \
    php7.3-xdebug \
    php7.3-xml \
;fi

###########################################################################
# Swoole EXTENSION
###########################################################################
COPY php-swoole.sh /etc/laradock
ARG INSTALL_PHP_SWOOLE=false
RUN if [ ${INSTALL_PHP_SWOOLE} = true ]; then \
    chmod +x /etc/laradock/php-swoole.sh \
    # Install Php Swoole Extension
    && if [ ${INSTALL_PHP_56} = true ]; then \
      /etc/laradock/php-swoole.sh 5.6; \
    fi \
    && if [ ${INSTALL_PHP_70} = true ]; then \
      /etc/laradock/php-swoole.sh 7.0; \
    fi \
    && if [ ${INSTALL_PHP_71} = true ]; then \
      /etc/laradock/php-swoole.sh 7.1; \
    fi \
    && if [ ${INSTALL_PHP_72} = true ]; then \
      /etc/laradock/php-swoole.sh 7.2; \
    fi \
    && if [ ${INSTALL_PHP_73} = true ]; then \
      /etc/laradock/php-swoole.sh 7.3; \
    fi \
    && /etc/laradock/php-swoole.sh 7.4 \
;fi

###########################################################################
# Node / NVM:
###########################################################################

ENV PROFILE /etc/laradock/profile.sh
ENV NVM_DIR /usr/local/nvm
ARG INSTALL_NODEJS=false
ARG INSTALL_YARN=false
ARG YARN_VERSION=latest
COPY nodejs.sh /etc/laradock
RUN if [ ${INSTALL_NODEJS} != false ]; then \
    mkdir -p $NVM_DIR \
    && chmod +x /etc/laradock/nodejs.sh \
    && INSTALL_NODEJS=${INSTALL_NODEJS} bash /etc/laradock/nodejs.sh \
;fi
RUN if [ ${INSTALL_NODEJS} != false ] && [ ${INSTALL_YARN} = true ]; then \
    [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh" && \
    if [ ${YARN_VERSION} = "latest" ]; then \
        curl -o- -L https://yarnpkg.com/install.sh | bash; \
    else \
        curl -o- -L https://yarnpkg.com/install.sh | bash -s -- --version ${YARN_VERSION}; \
    fi && \
    mv /root/.yarn /usr/local/yarn && \
    # Update to correct PATH to Yarn in profile config
    sed -i 's/\$HOME\/.yarn/\/usr\/local\/yarn/' $PROFILE \
;fi

RUN apt-get clean && \
    rm -f /etc/service/sshd/down && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
