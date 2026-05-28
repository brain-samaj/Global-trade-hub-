FROM php:8.2-apache

# Install system dependencies FIRST
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Enable Apache rewrite
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
