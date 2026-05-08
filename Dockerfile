FROM php:8.4-cli-bookworm AS php-base

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libpq-dev libzip-dev libonig-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mbstring pdo_pgsql zip gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM php-base AS vendor

COPY . .

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-progress

FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci

COPY . .

RUN npm run build

FROM php-base AS app

WORKDIR /app

ENV APP_ENV=production \
    APP_DEBUG=false \
    PORT=10000

COPY --from=vendor /app /app
COPY --from=frontend /app/public/build /app/public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD ["sh", "bin/start.sh"]
