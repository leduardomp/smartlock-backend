# syntax=docker/dockerfile:1
FROM php:7.4-apache
COPY ./Apache.conf /etc/Apache2/sites-enabled
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql
RUN a2enmod rewrite
RUN service apache2 restart
EXPOSE 80
