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

RUN docker-php-ext-install \
    pdo \
    #pdo_mysql \
    intl \
    opcache \
    zip \
    mbstring

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dependencias PHP
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Assets con Yarn
COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile

# Proyecto completo
COPY . .

RUN chown -R www-data:www-data /var/www/html/var

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]