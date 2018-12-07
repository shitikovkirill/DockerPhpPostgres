FROM fabianmartin/php-symfony:7.2-debian


RUN apt-get update \
    && apt-get install autoconf libpq-dev build-essential -y \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo_pgsql

RUN cd /tmp \
    && apt-get install wget unzip -y \
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
    && rm -f /tmp/phpredis.zip

RUN apt-get install curl -y \
    && version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version \
    && mkdir -p /tmp/blackfire \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > $PHP_INI_DIR/conf.d/blackfire.ini \
    && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz

RUN mkdir -p /tmp/blackfire \
    && curl -A "Docker" -L https://blackfire.io/api/v1/releases/client/linux_static/amd64 | tar zxp -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire /usr/bin/blackfire \
    && rm -Rf /tmp/blackfire