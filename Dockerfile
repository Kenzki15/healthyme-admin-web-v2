# Multi-stage Dockerfile for deploying this Laravel app to Render
# - Builds frontend assets with Node
# - Installs PHP + extensions and Composer deps
# - Copies built assets into public/build
# - Uses php artisan serve to bind to Render's $PORT

# 1) Build frontend assets
FROM node:22-alpine AS node_builder
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --silent
COPY . .
RUN npm run build

# 2) Build PHP application image
FROM php:8.2-fpm-bullseye

# Install system packages & PHP extensions required by typical Laravel apps
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        zlib1g-dev \
        libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mbstring bcmath gd zip xml intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app source
COPY . .

# Copy built frontend assets from node_builder (Vite outputs to public/build)
COPY --from=node_builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    && php artisan key:generate --ansi || true \
    && php artisan config:cache || true \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure the storage directories exist and are writable
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
    && chown -R www-data:www-data storage bootstrap/cache

# Render provides a PORT environment variable. Fallback to 8000 if not set.
ENV PORT=8000
EXPOSE 8000

# Use the built-in PHP dev server to serve the app on $PORT (suitable for Render web services)
# For production-grade deployments you can replace this with nginx + php-fpm and a process manager.
CMD ["sh", "-lc", "php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
