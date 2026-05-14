FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        curl \
        libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY src/ .

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --prefer-dist --optimize-autoloader --no-interaction \
    && composer clear-config-cache \
    && chown -R www-data:www-data /var/www/html/data

EXPOSE 9000

CMD ["php-fpm"]