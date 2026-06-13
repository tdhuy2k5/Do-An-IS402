# ==========================================
# BASE IMAGE
# ==========================================
FROM dunglas/frankenphp:1.4-alpine AS base

RUN install-php-extensions \
    gd \
    pdo_mysql \
    zip \
    bcmath \
    opcache \
    redi

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

ENV FRANKENPHP_DOCUMENT_ROOT=/app/public
ENV SERVER_NAME=:8080

RUN mkdir -p /data/caddy /config/caddy \
    && chown -R www-data:www-data /data /config /app


# ==========================================
# PRODUCTION IMAGE
# ==========================================
FROM base AS production

# 1. Copy only composer files first (cache layer)
COPY --chown=www-data:www-data composer.json composer.lock ./

USER www-data

# 2. Install dependencies (ONLY ONCE)
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts
# 3. Copy application code
COPY --chown=www-data:www-data . .

# 4. Fix Laravel permissions
RUN mkdir -p \
    storage/logs \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]