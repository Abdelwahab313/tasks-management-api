
FROM php:8.2-fpm

# Install system dependencies and PHP extensions in a single layer
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*

# Install composer with specific version and optimize it
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set composer environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/composer \
    COMPOSER_CACHE_DIR=/composer/cache

# Create composer cache directory and set permissions
RUN mkdir -p /composer/cache \
    && chmod -R 777 /composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first
COPY composer.json composer.lock ./

# Install dependencies (with optimizations)
RUN composer install --no-scripts --no-autoloader --no-dev

# Copy the rest of the application
COPY . .

# Generate optimized autoload files
RUN composer dump-autoload --optimize --classmap-authoritative

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html


# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
