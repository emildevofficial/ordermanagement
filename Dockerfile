FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        curl \
        libzip-dev \
        nginx \
        gettext-base \
    && docker-php-ext-install pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY src/ .
COPY docker/nginx/railway.conf.template /etc/nginx/templates/default.conf.template

RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction \
    && composer clear-config-cache \
    && chown -R www-data:www-data /var/www/html/data

EXPOSE 8080

CMD ["sh", "-c", "export PORT=\"${PORT:-8080}\" && envsubst '$PORT' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf && php-fpm -D && nginx -g 'daemon off;'"]
