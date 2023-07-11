# Base image
FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage

# Install application dependencies
RUN composer install --no-interaction --optimize-autoloader --no-scripts

# Generate application key
RUN php artisan key:generate

# Check if .env file exists, otherwise copy .env.example
RUN if [ ! -f ".env" ]; then cp .env.example .env; fi

# Create SQLite database
RUN touch database/database.sqlite && \
    chown www-data:www-data database/database.sqlite

# Run database migrations
RUN php artisan migrate --force

# Generate Swagger documentation
RUN php artisan l5-swagger:generate

# Expose port
EXPOSE 8000

# Start PHP server
CMD php artisan serve --host=0.0.0.0 --port=8000
