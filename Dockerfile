FROM php:8.2-apache

# Enable PostgreSQL extension
RUN docker-php-ext-install pdo pdo_pgsql

# Copy project files
COPY . /var/www/html/

# Enable Apache rewrite
RUN a2enmod rewrite

# Set permissions
RUN chown -R www-data:www-data /var/www/html
