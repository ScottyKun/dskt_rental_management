FROM php:8.3-fpm

# Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip intl gd

# Installer Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copier l'application
COPY . .

# Permissions Laravel
RUN chown -R www-data:www-data /var/www

# Commande par défaut
CMD ["php-fpm"]
