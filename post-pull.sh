#!/usr/bin/zsh

COUNT=$(pa migrate:status | grep Pending | wc -l) | tr -d ' ';
COUNT=$(echo $COUNT | tr -d ' ');

echo "$COUNT pending migrations";

MIGRATE=$(pa migrate --force);

RESULT=$(echo $?);

if [ "$RESULT" != "0" ]; then
    pa send:error "Unable to complete database migration: $MIGRATE";
fi

yarn build;

pa view:cache;
pa config:cache;
pa route:cache;
