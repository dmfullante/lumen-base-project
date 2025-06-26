# Use official PHP image with Apache
FROM php:8.3-apache

# Install dependencies, php-zip, and socat
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libmagickwand-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Enable rewrite module
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy the current application code to the working directory
COPY . .

# Install project dependencies
RUN composer install

# Set proper permissions for Lumen to write logs
RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/storage
RUN chown -R www-data:www-data storage; 
RUN chmod -R 775 /var/www/html/storage; 
RUN usermod -a -G www-data root

# Expose port 5177 (default for Apache)
EXPOSE 5177

# The default CMD from `php:8.3-apache` runs Apache2 automatically
