FROM php:8.1-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .
# Check if .env file exists, otherwise copy .env.example
RUN if [ ! -f ".env" ]; then cp .env.example .env; fi
RUN composer install

RUN php artisan l5-swagger:generate



# Expose port
EXPOSE 8000
