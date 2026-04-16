FROM php:8.2-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip curl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy everything (mos u ndaj më)
COPY . .

# install deps
RUN composer install

CMD php -S 0.0.0.0:$PORT -t src/public