FROM php:8.3-fpm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    pkg-config \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Configuration PHP sessions
RUN echo "session.save_path = /tmp/sessions" >> /usr/local/etc/php/conf.d/sessions.ini \
    && mkdir -p /tmp/sessions \
    && chown www-data:www-data /tmp/sessions

# Extension MongoDB
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du projet
COPY . .

# Fix git safe directory + installer les dépendances
RUN git config --global --add safe.directory /var/www/html \
    && if [ -f composer.json ]; then composer install --no-interaction --prefer-dist; fi

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000
CMD ["php-fpm"]