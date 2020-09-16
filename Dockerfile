FROM mysql:latest

## Engine for create REST Server
FROM php:7.3-apache

## Install re-config access with .htaccess
RUN a2enmod rewrite

## install necessary extensions
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo pdo_mysql

## installing gd - processing image
RUN apt-get update \
    && apt-get install libmcrypt-dev -y libreadline-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd


# RUN apt-get update  \
#     && docker-php-ext-install gd

# RUN apt-get update  \
#     && docker-php-ext-install mcrypt
RUN pecl install xdebug-2.7.2 && docker-php-ext-enable xdebug
COPY ./server/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY ./source/ /var/www/html/
COPY ./init/ /var/init/
COPY ./server/ /var/server/
# COPY ./server/apache/ports.conf /etc/apache2/ports.conf

CMD docker-php-entrypoint apache2-foreground
