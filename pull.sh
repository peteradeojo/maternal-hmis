echo "[$(date +'%Y-%m-%d %H:%M:%S')] Starting pull"

PULL=$(git pull);
RESULT=$(echo $?);

if [ "$RESULT" != "0" ]; then
    php artisan send:error "Failed git pull: $PULL";
fi

echo "[$(date +'%Y-%m-%d %H:%M:%S')] $PULL";

if [ "$PULL" != "Already up to date." ]; then
    source ./post-pull.sh
fi
