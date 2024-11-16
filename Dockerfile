FROM php:8.0-apache

# Install PostgreSQL PDO and pgsql extensions
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo_pgsql pgsql

# Install PostgreSQL PDO extension
RUN docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

RUN mkdir /app/upload && chmod 777 /app/upload

# Copy your PHP files to the container
COPY . /var/www/html/

# Expose port 80 to be able to access the app
EXPOSE 80
