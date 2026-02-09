FROM php:8.3-cli-alpine

RUN apk add --no-cache bash git curl tzdata icu-dev libzip-dev oniguruma-dev \
  && docker-php-ext-install -j$(nproc) pdo_mysql bcmath intl pcntl zip opcache \
  && apk add --no-cache $PHPIZE_DEPS \
  && pecl install openswoole redis \
  && docker-php-ext-enable openswoole redis

# Custom PHP configuration
COPY ./docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . /var/www

# Install Laravel dependencies
RUN composer update --no-interaction --prefer-dist

# Entrypoints
COPY --chown=root:root --chmod=0755 docker/entrypoints/start-octane.sh     /usr/local/bin/start-octane.sh
COPY --chown=root:root --chmod=0755 docker/entrypoints/start-horizon.sh    /usr/local/bin/start-horizon.sh
COPY --chown=root:root --chmod=0755 docker/entrypoints/start-scheduler.sh  /usr/local/bin/start-scheduler.sh

# Permisos mínimos
RUN addgroup -g 1000 app && adduser -G app -g app -D -u 1000 app \
    && chown -R app:app /var/www
USER app

EXPOSE 8000
CMD ["start-octane.sh"]
