# ============================================================
# Stage 1 — Composer dependencies
# ============================================================
FROM php:8.4-cli AS composer

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN php -v && composer --version

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts


# ============================================================
# Stage 2 — Frontend build
# ============================================================
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package*.json ./

RUN npm ci

COPY resources ./resources
COPY vite.config.* ./
COPY public ./public

ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT
ARG VITE_REVERB_SCHEME
ARG VITE_VAPID_PUBLIC_KEY

ENV VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY
ENV VITE_REVERB_HOST=$VITE_REVERB_HOST
ENV VITE_REVERB_PORT=$VITE_REVERB_PORT
ENV VITE_REVERB_SCHEME=$VITE_REVERB_SCHEME
ENV VITE_VAPID_PUBLIC_KEY=$VITE_VAPID_PUBLIC_KEY

RUN npm run build


# ============================================================
# Stage 3 — Production Laravel
# ============================================================
FROM php:8.4-apache

WORKDIR /var/www/html


# ------------------------------------------------------------
# System dependencies
# ------------------------------------------------------------
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*


# ------------------------------------------------------------
# PHP extensions
# ------------------------------------------------------------
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        bcmath \
        intl \
        mbstring \
        exif \
        pcntl \
        zip \
        gd \
        opcache


# ------------------------------------------------------------
# Redis / PHPRedis
# ------------------------------------------------------------
RUN pecl install redis \
    && docker-php-ext-enable redis


# ------------------------------------------------------------
# Apache modules
# ------------------------------------------------------------
RUN a2enmod rewrite headers


# Laravel public directory
RUN sed -ri \
    -e 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/000-default.conf


# ------------------------------------------------------------
# Composer
# ------------------------------------------------------------
COPY --from=composer /usr/bin/composer /usr/bin/composer


# ------------------------------------------------------------
# Application
# ------------------------------------------------------------
COPY . .


# Vendor provenant du stage Composer
COPY --from=composer /app/vendor ./vendor


# Frontend compilé par Vite
COPY --from=frontend /app/public/build ./public/build


# ------------------------------------------------------------
# Laravel permissions
# ------------------------------------------------------------
RUN mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data \
        storage \
        bootstrap/cache \
    && chmod -R 775 \
        storage \
        bootstrap/cache


# ------------------------------------------------------------
# Production optimizations
# ------------------------------------------------------------
RUN php artisan package:discover --ansi \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear


# ------------------------------------------------------------
# PHP production configuration
# ------------------------------------------------------------
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"


# ------------------------------------------------------------
# OPcache
# ------------------------------------------------------------
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.enable_cli=1'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=16'; \
        echo 'opcache.max_accelerated_files=20000'; \
    } > "$PHP_INI_DIR/conf.d/opcache.ini"


EXPOSE 80


CMD ["apache2-foreground"]