# From official php image.
FROM php:8.4-fpm-alpine
# Install dependencies
RUN apk add --no-cache \
    sqlite-dev \
    mariadb-connector-c-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite
# Set workdir
WORKDIR /var/www/html
# Copy application files
COPY . .
# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Run composer install
RUN composer install
# Install vite
RUN npm install --save-dev vite laravel-vite-plugin
# Build npm
RUN npm run build
