# ==========================================
# STAGE 1: BASE INFRASTRUCTURE
# ==========================================
FROM dunglas/frankenphp:1.4-alpine AS base

# Install production extensions
RUN install-php-extensions gd pdo_mysql zip bcmath opcache redis
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

ENV FRANKENPHP_DOCUMENT_ROOT=/app/public
ENV SERVER_NAME=:8080

# Create Caddy configuration directories
RUN mkdir -p /data/caddy /config/caddy \
    && chown -R www-data:www-data /data /config /app

# ==========================================
# STAGE 2: QUALITY GATE (TESTING ENVIRONMENT)
# ==========================================
FROM base AS tester

# Copy all code including dev files
COPY --chown=www-data:www-data . .

USER www-data

# Install EVERYTHING (including dev dependencies for PHPUnit/Pest)
RUN composer install --prefer-dist

# CI triggers this target to run your automated tests
# Example command CI can execute: php artisan test
CMD ["php", "artisan", "test"]


# ==========================================
# STAGE 3: FINAL PRODUCTION RUNTIME
# ==========================================
FROM base AS production

# Copy package files first for cache efficiency
COPY --chown=www-data:www-data composer.json composer.lock* ./

USER www-data

# Fast pre-install without scripts
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist \
    && composer clear-cache

# Copy the rest of the application files
COPY --chown=www-data:www-data . .

# CRUCIAL FIX: Ensure correct permissions for Laravel runtime directories
RUN mkdir -p /app/storage/logs /app/storage/framework/sessions /app/storage/framework/views \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Re-run composer to execute optimization scripts and clean autoloader
RUN composer install --no-dev --prefer-dist \
    && composer dump-autoload --no-dev --optimize \
    && composer clear-cache

EXPOSE 8080