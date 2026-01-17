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
    php8.4-pgsql \
    php8.4-curl \
    php8.4-zip \
    git curl unzip

COPY --from=bitnami/laravel /opt/bitnami/php/bin/composer /usr/bin/composer
COPY --from=node:24 /usr/local/bin/npm /usr/local/bin/node /usr/bin/

RUN git clone https://github.com/tursodatabase/libsql-php.git

RUN apt-get install php-dev -y

RUN cd libsql-php \
    && phpize \
    && ./configure \
    && make -j$(nproc) \
    && make install \
    && echo "extension=libsql" > /usr/local/etc/php/conf.d/libsql.ini

WORKDIR /var/www

COPY composer.json package.json *.lock /var/www/

RUN npm i
RUN composer install -n
