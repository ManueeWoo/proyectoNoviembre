FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && touch /tmp/xdebug.log \
    && chown www-data:www-data /tmp/xdebug.log \
    && chmod 664 /tmp/xdebug.log

COPY .configuracion/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY .configuracion/apache.conf /etc/apache2/sites-available/000-default.conf