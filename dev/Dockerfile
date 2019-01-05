FROM fabianmartin/php-symfony:7.2-xdebug-debian


RUN deps='autoconf build-essential' \
    && apt-get update \
    && apt-get install -y $deps \
    && apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo_pgsql \
    && apt-get purge -y --auto-remove $deps \
    && apt-get autoclean

RUN deps='autoconf build-essential wget unzip' \
    && apt-get install -y $deps \
    && cd /tmp \
    && wget https://github.com/phpredis/phpredis/archive/master.zip -O phpredis.zip \
    && unzip -o /tmp/phpredis.zip \
    && mv /tmp/phpredis-* /tmp/phpredis \
    && cd /tmp/phpredis \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && touch /usr/local/etc/php/conf.d/redis.ini \
    && echo extension=redis.so > /usr/local/etc/php/conf.d/redis.ini \
    && rm -rf /tmp/phpredis \
    && rm -f /tmp/phpredis.zip \
    && apt-get purge -y --auto-remove $deps \
    && apt-get autoclean

RUN deps='curl' \
    && apt-get install -y $deps \
    && version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version \
    && mkdir -p /tmp/blackfire \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
    && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz \
    && apt-get purge -y --auto-remove $deps \
    && apt-get autoclean

RUN deps='curl' \
    && apt-get install -y $deps \
    && mkdir -p /tmp/blackfire \
    && curl -A "Docker" -L https://blackfire.io/api/v1/releases/client/linux_static/amd64 | tar zxp -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire /usr/bin/blackfire \
    && rm -Rf /tmp/blackfire \
    && apt-get purge -y --auto-remove $deps \
    && apt-get autoclean

RUN sh -c "echo 'precedence ::ffff:0:0/96 100' >> /etc/gai.conf"