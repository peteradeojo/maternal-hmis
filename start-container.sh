#!/bin/sh

php artisan migrate --force --seed

supervisord -n -c /app/supervisor.conf
