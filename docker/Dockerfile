FROM php:8.3-fpm

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl libpng-dev libjpeg62-turbo-dev libwebp-dev libfreetype6-dev \
        libzip-dev libicu-dev libonig-dev supervisor \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql gd zip intl bcmath pcntl opcache exif

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Run as www-data
RUN chown -R www-data:www-data /var/www/html
USER www-data

EXPOSE 9000
CMD ["php-fpm"]
