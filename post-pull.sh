export NVM_DIR="$([ -z "${XDG_CONFIG_HOME-}" ] && printf %s "${HOME}/.nvm" || printf %s "${XDG_CONFIG_HOME}/nvm")"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" # This loads nvm

COUNT=$(php artisan migrate:status | grep Pending | wc -l);
COUNT=$(echo $COUNT | tr -d ' ');

echo "$COUNT pending migrations";

MIGRATE=$(php artisan migrate --force);

RESULT=$(echo $?);

if [ "$RESULT" != "0" ]; then
    php artisan send:error "Unable to complete database migration: $MIGRATE";
fi

RESULT=$(echo $?);

BUILD=$(yarn build);
if [ "$RESULT" != "0" ]; then
    php artisan send:error "Unable to complete build step: $BUILD";
fi

php artisan view:cache;
php artisan config:cache;
php artisan route:cache;

chown -R www-data:www-data /var/www/hmis/storage/logs
