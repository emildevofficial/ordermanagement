FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git unzip curl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy only composer.json
COPY composer.json /var/www/

RUN composer install

# copy rest of project
COPY . /var/www/

CMD php -S 0.0.0.0:$PORT -t src/public