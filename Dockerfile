# Dockerfile
FROM php:8.2-apache

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files into container
COPY src/ /var/www/html/

# Set working directory
WORKDIR /var/www/html/
# Set proper file permissions (optional but recommended)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
# Enable PHP error reporting (optional, for development)
RUN echo "display_errors=On\nerror_reporting=E_ALL" > /usr/local/etc/php/conf.d/docker-php-errors.ini
