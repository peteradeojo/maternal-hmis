#!/usr/bin/bash
# Navigate to project directory and build
# 1. Load NVM into this session
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

cd /var/www/hmis
git pull

composer install
composer dump-autoload -o

if php artisan migrate:status | grep -q "Pending"; then
    echo "Found pending migrations. Running migrate..."
    php artisan migrate --force -n --isolated
else
    echo "No pending migrations to run."
fi

npm ci

npm run build
./cloudflare-purge.sh
sudo supervisorctl restart all

# # Pull latest images
# docker compose -f docker-compose.yml -f docker-compose-prod.yml pull

# # Restart services (fixed .yml typo here)
# docker compose -f docker-compose.yml -f docker-compose-prod.yml up -d

# # Optional: Clean up old images to save disk space
# docker image prune -f
