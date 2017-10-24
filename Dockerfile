FROM php:5-apache
RUN apt-get update && apt-get install -y php5-mysqlnd \
  && docker-php-ext-install -j$(nproc) mysql mysqli
COPY ./ /var/www/html/
COPY php.ini /usr/local/etc/php/
EXPOSE 80
