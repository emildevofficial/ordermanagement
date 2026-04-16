FROM php:8.2-cli

# set working dir
WORKDIR /var/www

# install deps
RUN apt-get update && apt-get install -y \
    git unzip curl

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy composer files FIRST
COPY composer.json composer.lock /var/www/

# install dependencies
RUN composer install

# copy rest of app
COPY . /var/www/

# start app
CMD php -S 0.0.0.0:$PORT -t src/public