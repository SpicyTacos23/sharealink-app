FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    curl \
    nginx \
    supervisor \
    nodejs \
    npm \
    yarn \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    autoconf \
    g++ \
    make

# NOTA: si el frontend no accede a MySQL directamente (confirmar con composer.json),
# se puede quitar "pdo" de esta lista.
RUN docker-php-ext-install \
    pdo \
    intl \
    opcache \
    zip \
    mbstring

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dependencias PHP (sin --no-dev: APP_ENV=dev necesita los paquetes de desarrollo)
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-scripts --no-interaction

# Assets con Yarn
COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile

# Proyecto completo
COPY . .

# asegurar que var existe y tiene permisos correctos
RUN mkdir -p /var/www/html/var
RUN chown -R www-data:www-data /var/www/html/var

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

COPY docker/entrypoint-frontend.sh /entrypoint-frontend.sh
RUN chmod +x /entrypoint-frontend.sh

ENTRYPOINT ["/entrypoint-frontend.sh"]