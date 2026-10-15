FROM php:8.4-cli-alpine AS base

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app


FROM base AS development

RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps

COPY xdebug.ini /usr/local/etc/php/conf.d/99-xdebug.ini