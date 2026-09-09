FROM php:8.3-cli-alpine

# Create user with id 1000
RUN addgroup -g 1000 -S user \
    && adduser -u 1000 -D user -G user

# Install system packages
RUN apk add --no-cache \
    bash \
    curl \
    git \
    vim \
    postgresql-dev \
    libxml2-dev \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    dom \
    xml

# Install Redis extension
RUN mkdir -p /usr/src/php/ext/redis \
    && curl -fsSL --ipv4 https://github.com/phpredis/phpredis/archive/6.0.2.tar.gz \
       | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && docker-php-ext-install redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Use the non-root user
USER user

# Prevent container from exiting
CMD ["sleep", "infinity"]