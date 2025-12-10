FROM php:8.4-apache AS base

LABEL maintainer="KUN"

# Install only production dependencies
RUN apt-get update && apt-get install -y \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libpq-dev \
    libicu-dev \
    libsqlite3-dev \
    postgresql-client \
    procps \
    && docker-php-ext-install pdo pdo_mysql pdo pdo_sqlite \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip bcmath opcache intl exif pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite

# Configure Apache to use Laravel's public directory
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>' >> /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Create user
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Dependencies stage
FROM base AS dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Production stage
FROM base AS production
# Copy application files FIRST
COPY --chown=www:www . .

# Copy vendor AFTER (to avoid overwriting)
COPY --from=dependencies /var/www/html/vendor ./vendor

# Regenerate autoloader and clear any cached config
RUN composer dump-autoload --optimize --no-dev

# Remove non-production files
RUN rm -rf docker/ .git .env.example README.md CLAUDE.md \
    tests/ phpunit.xml node_modules/ \
    bootstrap/cache/*.php \
    && mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache \
    && chown -R www:www storage bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache \
    && chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure Apache can run as www user
RUN mkdir -p /var/run/apache2 && chown -R www:www /var/run/apache2
RUN sed -i 's/User www-data/User www/g' /etc/apache2/apache2.conf
RUN sed -i 's/Group www-data/Group www/g' /etc/apache2/apache2.conf

USER www

HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

EXPOSE 80
CMD ["apache2-foreground"]