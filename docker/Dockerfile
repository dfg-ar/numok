# Dockerfile for Numok PHP application
# - Uses php:8.2-apache as the base
# - Installs required PHP extensions (pdo_mysql)
# - Sets Apache DocumentRoot to /var/www/html/public
# - Installs Composer dependencies
# - Provides an entrypoint that prepares config/config.php from env

# Stage for getting Composer binary
FROM composer:2 AS composer

# Runtime image with Apache + PHP
FROM php:8.2-apache

# Install required extensions and enable Apache modules
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        default-mysql-client \
        libonig-dev \
        pkg-config \
        iputils-ping \
        curl \
        netcat-openbsd \
    && docker-php-ext-install pdo pdo_mysql mbstring \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Set Apache DocumentRoot to public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/sites-enabled/*.conf

# Allow .htaccess overrides for the DocumentRoot
RUN printf "<Directory %s>\n    AllowOverride All\n</Directory>\n" "$APACHE_DOCUMENT_ROOT" \
    > /etc/apache2/conf-available/override.conf \
    && a2enconf override

# Copy Composer into the image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Install PHP dependencies first for better layer caching
COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

# Copy the rest of the application code
COPY . .

# Ensure writable directories exist and have proper ownership
RUN mkdir -p public/tracking \
    && chown -R www-data:www-data public/tracking

# Add entrypoint script
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

# Entry point prepares config then starts Apache in foreground
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]


