#!/usr/bin/zsh

COUNT=$(php artisan migrate:status | grep Pending | wc -l);
COUNT=$(echo $COUNT | tr -d ' ');

echo "$COUNT pending migrations";

MIGRATE=$(php artisan migrate --force);

RESULT=$(echo $?);

if [ "$RESULT" != "0" ]; then
    php artisan send:error "Unable to complete database migration: $MIGRATE";
fi

npm run build;

php artisan view:cache;
php artisan config:cache;
php artisan route:cache;
