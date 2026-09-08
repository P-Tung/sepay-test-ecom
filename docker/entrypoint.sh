#!/bin/sh

set -eu

cd /var/www/html

mkdir -p database storage/framework/cache storage/framework/sessions storage/framework/views storage/logs

if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

php artisan config:clear
php artisan migrate --force
if [ "${SEED_DATABASE:-false}" = "true" ]; then
    php artisan db:seed --force
fi
php artisan storage:link || true
chown -R www-data:www-data database storage bootstrap/cache

exec "$@"
