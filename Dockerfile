# =====================================================
# 1️⃣ Stage: Node Build
# =====================================================
FROM node:22.17-alpine AS node-builder

WORKDIR /app

# Cache de dependencias Node
COPY package.json package-lock.json ./
RUN npm ci 

# Copiar todo (excepto .dockerignore)
COPY . .
RUN npm run build


# =====================================================
# 2️⃣ Stage: Composer Build
# =====================================================
FROM php:8.2-cli-alpine AS php-builder

WORKDIR /app

# Cache de paquetes apk
RUN --mount=type=cache,target=/var/cache/apk \
    apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    sqlite-dev \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# Instalar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_sqlite \
    pdo_pgsql \
    mbstring \
    bcmath \
    zip \
    gd \
    intl

# ── Imagick ───────────────────────────────────────────────
RUN apk add --no-cache --virtual .imagick-build-deps \
    $PHPIZE_DEPS \
    imagemagick-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apk del .imagick-build-deps

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Cache de Composer
COPY composer.json composer.lock ./
RUN --mount=type=cache,target=/root/.composer \
    composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

# Copiar todo el código (sin vendor, node_modules, storage por .dockerignore)
COPY . .

RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache

# Generar autoloader optimizado
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

# Publicar vendor assets si los hay
RUN php artisan vendor:publish --all --force

# =====================================================
# 3️⃣ Stage: Runtime Final
# =====================================================
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Runtime libraries
RUN --mount=type=cache,target=/var/cache/apk \
    apk add --no-cache \
    libzip \
    icu \
    oniguruma \
    sqlite-libs \
    postgresql-libs \
    libpng \
    libjpeg-turbo \
    freetype \
    bash

# Instalar extensiones PHP
RUN --mount=type=cache,target=/var/cache/apk \
    apk add --no-cache --virtual .build-deps \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    sqlite-dev \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_sqlite \
    pdo_pgsql \
    mbstring \
    bcmath \
    zip \
    gd \
    intl && \
    apk del .build-deps

# ── Imagick runtime + extensión ───────────────────────────
RUN --mount=type=cache,target=/var/cache/apk \
    apk add --no-cache --virtual .imagick-build-deps \
    $PHPIZE_DEPS \
    imagemagick-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apk add --no-cache imagemagick \
    && apk del .imagick-build-deps
    
# Copiar aplicación completa (código + vendor generado)
COPY --from=php-builder --chown=www-data:www-data /app /var/www/html

# Copiar assets compilados
COPY --from=node-builder --chown=www-data:www-data /app/public/build /var/www/html/public/build

# Crear directorios de storage (se montarán como volúmenes)
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

USER www-data

EXPOSE 9000

CMD ["php-fpm"]