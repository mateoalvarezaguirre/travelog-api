FROM php:8.3-fpm-alpine

ARG PUID=1000
ARG PGID=1000

# Dependencias del sistema y extensiones PHP típicas para Laravel
RUN apk add --no-cache \
    bash git curl zip unzip icu-dev oniguruma-dev libzip-dev libpng-dev \
    libjpeg-turbo-dev freetype-dev libxml2-dev supervisor

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_mysql mbstring intl zip gd opcache bcmath pcntl

# Opcional: xdebug para dev (comenta en prod)
# RUN pecl install xdebug && docker-php-ext-enable xdebug

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Usuario no root (evita problemas de permisos en host)
RUN addgroup -g ${PGID} app && adduser -D -G app -u ${PUID} app
USER app

WORKDIR /var/www/html

# Opcache recomendado
USER root
RUN { \
      echo 'opcache.enable=1'; \
      echo 'opcache.enable_cli=1'; \
      echo 'opcache.validate_timestamps=1'; \
      echo 'opcache.revalidate_freq=0'; \
      echo 'opcache.jit=1255'; \
      echo 'opcache.jit_buffer_size=128M'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Supervisor ya instalado; no arrancamos aquí (los servicios queue/scheduler lo ejecutan)
USER app
