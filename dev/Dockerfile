FROM php:7.2-fpm-alpine

ENV SYMFONY_ENV dev
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk add --no-cache bash

# install the PHP extensions we need (https://make.wordpress.org/hosting/handbook/handbook/server-environment/#php-extensions)
RUN set -ex; \
	\
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		imagemagick-dev \
		libjpeg-turbo-dev \
		libpng-dev \
	; \
	\
	docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr; \
	docker-php-ext-install -j "$(nproc)" \
		bcmath \
		exif \
		gd \
		mysqli \
		opcache \
		zip \
	; \
	pecl install imagick-3.4.4; \
	docker-php-ext-enable imagick; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --virtual .wordpress-phpexts-rundeps $runDeps; \
	apk del .build-deps

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=2'; \
		echo 'opcache.fast_shutdown=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini
# https://codex.wordpress.org/Editing_wp-config.php#Configure_Error_Logging
RUN { \
		echo 'error_reporting = 4339'; \
		echo 'display_errors = Off'; \
		echo 'display_startup_errors = Off'; \
		echo 'log_errors = On'; \
		echo 'error_log = /dev/stderr'; \
		echo 'log_errors_max_len = 1024'; \
		echo 'ignore_repeated_errors = On'; \
		echo 'ignore_repeated_source = Off'; \
		echo 'html_errors = Off'; \
	} > /usr/local/etc/php/conf.d/error-logging.ini

RUN set -ex \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && apk add --no-cache --virtual .ext-deps zlib-dev postgresql-dev icu-dev sqlite-dev git composer \
	&& pecl install redis apcu xdebug \
	&& docker-php-ext-enable redis apcu xdebug \
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