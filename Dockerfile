FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC+1

RUN apt-get update && apt-get install -y software-properties-common

RUN add-apt-repository ppa:ondrej/php -y && apt-get update

RUN apt-get install -y \
    php8.4-fpm \
    php8.4-bcmath \
    php8.4-mbstring \
    php8.4-pdo \
    php8.4-xml \
    php8.4-pgsql \
    php8.4-curl \
    php8.4-redis \
    php8.4-zip \
    git curl unzip nginx

COPY --from=bitnami/laravel /opt/bitnami/php/bin/composer /usr/bin/composer
COPY --from=node:24 /usr/local/bin /usr/bin/
COPY --from=node:24 /usr/local/lib /usr/lib/

WORKDIR /var/www

COPY composer.json package.json *.lock /var/www/

RUN npm i

COPY . .

RUN composer install -n
RUN php artisan turso-php:install

COPY infra/php-override.ini /etc/php/8.4/cli/conf.d/30-override.ini
# RUN ln -s nginx.conf /etc/nginx/sites-enabled/emr
# RUN nginx -s reload

EXPOSE 9000 8000
