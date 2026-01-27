#!/bin/sh
set -e

echo "Checking permissions..."

# Only chown if we are running as root (standard in Docker)
if [ "$(id -u)" = '0' ]; then
    chown -R www-data:www-data /var/www/hmis/storage /var/www/hmis/bootstrap/cache
fi

php artisan config:clear
yarn build
composer dump-autoload -o

/usr/bin/supervisord -c infra/supervisor-prod.conf
# exec php-fpm
