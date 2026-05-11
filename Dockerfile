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
COPY docker/nginx/default.conf /etc/nginx/templates/default.conf.template
COPY docker/php-fpm/railway.conf /usr/local/etc/php-fpm.d/zz-railway.conf
COPY docker/start.sh /usr/local/bin/start.sh

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PORT=8080

RUN composer install --prefer-dist --optimize-autoloader --no-interaction \
    && composer clear-config-cache \
    && chown -R www-data:www-data /var/www/html/data \
    && rm -f /etc/nginx/sites-enabled/default \
    && chmod +x /usr/local/bin/start.sh

EXPOSE 8080

CMD ["/usr/local/bin/start.sh"]
