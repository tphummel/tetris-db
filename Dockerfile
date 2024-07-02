FROM php:7.4.27-apache
RUN docker-php-ext-install mysqli
COPY ./ /var/www/html/
COPY php.ini /usr/local/etc/php/
EXPOSE 80
