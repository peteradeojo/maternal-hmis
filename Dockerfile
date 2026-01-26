FROM php:8.4-fpm
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC+1

# Install dependencies
RUN apt-get update && apt-get install -y \
    openssl \
    ca-certificates \
    libxml2-dev \
    libpq-dev \
    # oniguruma-dev \
    gettext \
    # busybox-extras \
    nginx \
    supervisor zip curl

# Install php extensions
RUN install-php-extensions \
    bcmath \
    ctype \
    dom \
    ffi \
    fileinfo \
    mbstring \
    pdo pdo_mysql \
    pdo_pgsql \
    tokenizer \
    pcntl \
    redis-stable \
    ssh2 swoole \
    gd zip curl

# Node (build-time only)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs

RUN npm i -g yarn --force

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# ---- libSQL / Turso extension install ----
RUN composer global require darkterminal/turso-php-installer
RUN $(composer config --global home)/vendor/bin/turso-php-installer install -n --php-version=8.4

WORKDIR /var/www/hmis

# Dependency manifests
COPY composer.json package.json *.lock /var/www/hmis/
COPY artisan .
COPY bootstrap/ ./bootstrap/
COPY public/ ./public/
COPY tests/ ./tests/

RUN yarn
RUN composer install --no-dev --no-autoloader

# Copy app
COPY . /var/www/hmis/

# Build assets
# RUN yarn build
# RUN composer dump-autoload -o

# PHP config
COPY infra/php-override.ini /usr/local/etc/php/conf.d/30-override.ini
COPY infra/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

EXPOSE 9000 8000 8001

COPY infra/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
# CMD ["php-fpm"]
CMD ["/usr/local/bin/entrypoint.sh"]
