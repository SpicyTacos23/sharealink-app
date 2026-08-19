FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    curl \
    nginx \
    supervisor \
    redis \
    mariadb-client \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    autoconf \
    g++ \
    make

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    intl \
    opcache \
    zip \
    mbstring

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-scripts --no-interaction

COPY . .

RUN mkdir -p /var/www/html/var
RUN chown -R www-data:www-data /var/www/html/var

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.web.conf /etc/supervisor/conf.d/supervisord.web.conf
COPY docker/supervisord.worker.conf /etc/supervisor/conf.d/supervisord.worker.conf

EXPOSE 80

COPY docker/entrypoint-web.sh /entrypoint-web.sh
COPY docker/entrypoint-worker.sh /entrypoint-worker.sh
RUN chmod +x /entrypoint-web.sh /entrypoint-worker.sh

ENTRYPOINT ["/entrypoint-web.sh"]