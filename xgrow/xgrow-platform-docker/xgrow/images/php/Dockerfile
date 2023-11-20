FROM php:7.4-fpm-alpine AS base

RUN apk add --update zlib-dev libpng-dev libzip-dev $PHPIZE_DEPS

RUN docker-php-ext-install exif
RUN docker-php-ext-install gd
RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install opcache
RUN pecl install mongodb
RUN pecl install apcu
RUN docker-php-ext-enable apcu
RUN docker-php-ext-enable mongodb
RUN pecl install redis && docker-php-ext-enable redis
RUN apk add git

RUN cd /usr/local/bin && curl --silent --show-error https://getcomposer.org/installer | php
RUN mv /usr/local/bin/composer.phar /usr/local/bin/composer
RUN composer install
