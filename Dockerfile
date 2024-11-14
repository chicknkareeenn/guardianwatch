# Use the official PHP image
FROM php:7.4-apache

# Install PostgreSQL PDO extension
RUN docker-php-ext-install pdo pdo_pgsql

# Copy your PHP files to the container
COPY . /var/www/html/

# Expose port 80 for the web server
EXPOSE 80
