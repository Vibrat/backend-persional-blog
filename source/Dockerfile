FROM mysql:latest

## Engine for create REST Server
FROM php:7.3-apache

## Install re-config access with .htaccess
RUN a2enmod rewrite

## install necessary extensions
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo_mysql

## install mcrypt
RUN apt-get update \
    && apt-get install libmcrypt-dev -y libreadline-dev
# RUN apt-get update  \
#     && docker-php-ext-install mcrypt
    
COPY ./ /var/www/html/
