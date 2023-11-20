FROM php:7.4-fpm-alpine AS base

RUN apk add --update zlib-dev libpng-dev libzip-dev $PHPIZE_DEPS

RUN docker-php-ext-install exif

RUN apk add --no-cache \
    freetype \
    libjpeg-turbo \
    libpng \
    libwebp-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    openssl-dev \
    && docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/include/ \
    --with-webp=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd \
    && apk del --no-cache \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    && rm -rf /tmp/*

RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install opcache
RUN pecl install apcu
RUN docker-php-ext-enable apcu

FROM base AS dev

COPY /composer.json composer.json
COPY /composer.lock composer.lock
COPY /app app
COPY /bootstrap bootstrap
COPY /config config
COPY /artisan artisan
COPY /routes routes

FROM base AS build-fpm

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY /artisan artisan

COPY /composer.json composer.json
COPY /composer.lock composer.lock

RUN composer install --prefer-dist --no-ansi --no-dev --no-autoloader

COPY /bootstrap bootstrap
COPY /app app
COPY /config config
COPY /routes routes


COPY . /var/www/html

RUN rm .env -f

RUN composer dump-autoload -o

FROM build-fpm AS fpm

COPY --from=build-fpm /var/www/html /var/www/html

COPY resources/ops/docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY resources/ops/docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
