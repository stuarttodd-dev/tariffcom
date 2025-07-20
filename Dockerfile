FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    unzip curl libpng-dev libonig-dev libxml2-dev zip git \
    mariadb-client libzip-dev libcurl4-openssl-dev \
    redis npm nodejs supervisor \
    && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY .docker/php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html

EXPOSE 8000

# Default command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
