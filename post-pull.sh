#!/usr/bin/zsh

COUNT=$(php artisan migrate:status | grep Pending | wc -l);
COUNT=$(echo $COUNT | tr -d ' ');

echo "$COUNT pending migrations";

MIGRATE=$(pa migrate --force);

RESULT=$(echo $?);

if [ "$RESULT" != "0" ]; then
    pa send:error "Unable to complete database migration: $MIGRATE";
fi

yarn build;

php artisan view:cache;
php artisan config:cache;
php route:cache;
