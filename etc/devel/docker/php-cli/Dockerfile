FROM php:8.4-cli-alpine

RUN apk add --no-cache --update --virtual buildDeps autoconf \
 && apk add --no-cache alpine-sdk \ 
 && apk add --update linux-headers \
 && apk add icu-dev 
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN pecl install xdebug && docker-php-ext-enable xdebug \
 && docker-php-ext-configure intl && docker-php-ext-install intl
RUN rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
ADD php/conf.d/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d
RUN mkdir -p /app

WORKDIR /app
