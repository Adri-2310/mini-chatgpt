# Build arguments
ARG PHP_VERSION=8.2

# Use the appropriate base image
FROM php:${PHP_VERSION}-fpm-alpine AS base

# Install required extensions and tools
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    && docker-php-ext-install -j$(nproc) \
    zip \
    intl \
    mbstring \
    pdo_mysql \
    gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Create bootstrap/cache directory BEFORE copying anything
RUN mkdir -p bootstrap/cache && chmod 755 bootstrap/cache

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --ignore-platform-reqs --no-interaction --prefer-dist

# Install Node.js and build assets (if needed)
FROM node:20-alpine AS node-build
WORKDIR /app
COPY --from=base /app .
RUN npm ci && npm run build

# Final stage
FROM php:${PHP_VERSION}-fpm-alpine

RUN apk add --no-cache \
    curl \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    && docker-php-ext-install -j$(nproc) \
    zip \
    intl \
    mbstring \
    pdo_mysql \
    gd

WORKDIR /app

# Copy from build stage
COPY --from=node-build /app .

# Ensure bootstrap/cache exists with correct permissions
RUN mkdir -p bootstrap/cache storage/logs storage/framework/{cache,sessions,views} && \
    chmod 755 bootstrap/cache && \
    chmod 777 storage/logs && \
    chmod 777 storage/framework/cache && \
    chmod 777 storage/framework/sessions && \
    chmod 777 storage/framework/views

# Set proper ownership for Laravel
RUN chown -R www-data:www-data /app

# Create entrypoint script for migrations and cache
RUN echo '#!/bin/sh\n\
set -e\n\
\n\
if [ "$APP_ENV" = "production" ]; then\n\
  echo "Running migrations..."\n\
  php artisan migrate --force\n\
fi\n\
\n\
echo "Clearing cache..."\n\
php artisan cache:clear\n\
php artisan config:clear\n\
php artisan view:clear\n\
\n\
echo "Starting PHP-FPM..."\n\
exec php-fpm\n\
' > /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD php -r "exit(file_exists('/app/storage/logs/laravel.log') ? 0 : 1);"

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
