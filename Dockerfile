FROM php:8.2-cli

WORKDIR /app

# install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy project
COPY . .

# install PHP dependencies
RUN composer install

# expose Railway port
EXPOSE 8080

# start app (IMPORTANT)
CMD php -S 0.0.0.0:$PORT -t src/public