# ==========================================
# STAGE 1: BASE
# ==========================================
FROM dunglas/frankenphp:1.4-alpine AS base

RUN install-php-extensions \
    gd \
    pdo_mysql \
    zip \
    bcmath \
    opcache \
    redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

ENV FRANKENPHP_DOCUMENT_ROOT=/app/public
ENV SERVER_NAME=:8080

# Create required directories
RUN mkdir -p /data/caddy /config/caddy

# ==========================================
# STAGE 2: TEST
# ==========================================
FROM base AS tester

COPY . .

RUN chown -R www-data:www-data /app

USER www-data

RUN composer install --prefer-dist --no-interaction

CMD ["php", "artisan", "test"]

# ==========================================
# STAGE 3: PRODUCTION
# ==========================================
FROM base AS production

# Copy composer manifests first for better cache usage
COPY composer.json composer.lock ./

RUN chown -R www-data:www-data /app

USER www-data

# Install production dependencies ONCE
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    && composer clear-cache

# Copy application source
COPY --chown=www-data:www-data . .

# Laravel runtime directories
RUN mkdir -p \
    storage/logs \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]