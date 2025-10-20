FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /var/www/app

# Install dependencies first
RUN composer install

# Set proper permissions (after composer install to ensure all files are present)
RUN chown -R www-data:www-data /var/www/app \
    && chmod -R 775 /var/www/app/storage \
    && chmod -R 775 /var/www/app/bootstrap/cache

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
