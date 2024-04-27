FROM php:8.3-fpm

# instal extension, for more info see https://github.com/mlocati/docker-php-extension-installer
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    @composer \
    amqp \
    bz2 \
    calendar \
    csv \
    intl \
    pdo_mysql \
    soap \
    sockets \
    uuid \
    xsl \
    yaml \
    zip

WORKDIR /srv

# PHP configuration
COPY ./docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# install dependencies
COPY ./.env /srv/.env
COPY ./composer.json /srv/composer.json
COPY ./composer.lock /srv/composer.lock

RUN composer install && composer dump-autoload -o -n

# needs directories
RUN mkdir /srv/logs/ && chmod 0777 ./logs
RUN mkdir /srv/temp/ && chmod 0777 ./temp

# application
COPY ./backend ./backend
COPY ./www/api ./www/api

# expose port for dev server
EXPOSE 9000

# run dev server
CMD ["php", "-S", "0.0.0.0:9000", "-t", "/srv/www/api"]
