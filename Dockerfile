# syntax=docker/dockerfile:1

# ---- PHP app + production dependencies -------------------------------------
FROM dunglas/frankenphp:1-php8.4 AS app

WORKDIR /app
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN install-php-extensions zip gd

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi

# ---- Front-end assets (Tailwind scans the pagination views in vendor/) ------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY --from=app /app /app
RUN npm run build

# ---- Runtime ----------------------------------------------------------------
FROM app
COPY --from=assets /app/public/build ./public/build

COPY docker/entrypoint.sh /usr/local/bin/fintrack-entrypoint
RUN chmod +x /usr/local/bin/fintrack-entrypoint

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/data/database.sqlite \
    SERVER_NAME=:8080

# /data holds the SQLite database and app key; receipts live under storage/app.
VOLUME ["/data", "/app/storage/app"]
EXPOSE 8080

ENTRYPOINT ["fintrack-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
