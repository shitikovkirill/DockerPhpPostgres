FROM wordpress:php7.2-fpm-alpine

ENV SYMFONY_ENV dev
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN set -ex \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && apk add --no-cache --virtual .ext-deps zlib-dev postgresql-dev icu-dev sqlite-dev git composer \
	&& pecl install redis apcu \
	&& docker-php-ext-enable redis apcu \
	&& docker-php-ext-install -j$(nproc) zip iconv intl json mbstring opcache pgsql pdo_pgsql pdo_sqlite sockets bcmath \
	&& apk del .build-deps \
	&& rm -rf /var/cache/apt/*


RUN addgroup -g 500 symfony \
    && adduser -u 500 -D -G symfony -h /home/symfony -s /bin/sh symfony \
    && mkdir -p /srv/symfony \
    && chown symfony:symfony /srv/symfony

RUN set -ex \
    && apk add --no-cache --virtual .build-deps curl \
    && version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s "https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version" \
    && mkdir -p /tmp/blackfire \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire-*.so $(php -r "echo ini_get('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > "$PHP_INI_DIR/conf.d/blackfire.ini" \
    && rm -rf /tmp/blackfire /tmp/blackfire-probe.tar.gz \
    && apk del .build-deps \
    && rm -rf /var/cache/apt/*

RUN set -ex \
    && apk add --no-cache --virtual .build-deps curl \
    && mkdir -p /tmp/blackfire \
    && curl -A "Docker" -L https://blackfire.io/api/v1/releases/client/linux_static/amd64 | tar zxp -C /tmp/blackfire \
    && mv /tmp/blackfire/blackfire /usr/bin/blackfire \
    && rm -Rf /tmp/blackfire \
    && apk del .build-deps \
    && rm -rf /var/cache/apt/*

COPY conf.d /usr/local/etc/php/conf.d/
COPY pool.d /usr/local/etc/php-fpm.d/

WORKDIR /srv/symfony

CMD ["php-fpm"]