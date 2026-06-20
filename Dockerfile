FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libssl-dev \
    pkg-config \
    zip \
    git \
    && docker-php-ext-install intl pdo_mysql zip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb


COPY --link --from=ghcr.io/symfony-cli/symfony-cli:latest /usr/local/bin/symfony /usr/local/bin/symfony

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]