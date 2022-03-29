# FROM php:5.6.40-apache-jessie
FROM php:7.4.28-apache
# FROM php:7.4.27-alpine
RUN docker-php-ext-install mysqli
COPY ./ /var/www/html/
COPY php.ini /usr/local/etc/php/
EXPOSE 80
