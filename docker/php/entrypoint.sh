#!/usr/bin/env sh
set -eu

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true

if [ "${DB_CONNECTION:-}" = "sqlite" ] && [ -n "${DB_DATABASE:-}" ]; then
    mkdir -p "$(dirname "$DB_DATABASE")"
    touch "$DB_DATABASE"
    chown -R www-data:www-data "$(dirname "$DB_DATABASE")" || true
fi

if [ "${CACHE_LARAVEL_CONFIG:-true}" = "true" ]; then
    echo "[entrypoint] caching Laravel config"
    php artisan config:cache || true
    echo "[entrypoint] caching Laravel views"
    php artisan view:cache || true
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    if [ "${RUN_SEEDERS:-false}" = "true" ]; then
        echo "[entrypoint] running php artisan migrate --seed --force"
        php artisan migrate --seed --force
    else
        echo "[entrypoint] running php artisan migrate --force"
        php artisan migrate --force
    fi
elif [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "[entrypoint] running php artisan db:seed --force"
    php artisan db:seed --force
fi

exec "$@"
