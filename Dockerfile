FROM php:8.1.0-fpm

# Arguments defined in docker-compose.yml

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nano

ENV COMPOSER_ALLOW_SUPERUSER=1

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www
EXPOSE 8000
# CMD composer update

# CMD php artisan serve

# RUN php artisan migrate

# CMD php artisan serve --host=0.0.0.0 --port=8000

