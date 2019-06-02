FROM mysql:latest

## Engine for create REST Server
FROM php:7.3-apache

## Install re-config access with .htaccess
RUN a2enmod rewrite

## install necessary extensions
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo pdo_mysql 

## install mcrypt
RUN apt-get update \
    && apt-get install libmcrypt-dev -y libreadline-dev
# RUN apt-get update  \
#     && docker-php-ext-install mcrypt
RUN pecl install xdebug-2.7.2 && docker-php-ext-enable xdebug
COPY ./server/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
    
COPY ./source/ /var/www/html/
COPY ./init/ /var/init/
COPY ./server/ /var/server/

RUN service apache2 restart
