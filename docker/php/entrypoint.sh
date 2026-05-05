#!/usr/bin/env sh
set -eu

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
mkdir -p storage/app/public
chown -R www-data:www-data storage bootstrap/cache || true

if [ -z "${APP_KEY:-}" ]; then
    echo "[entrypoint] APP_KEY is missing; generating a runtime key"
    export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
fi

cat > .env <<EOF
APP_NAME=${APP_NAME:-SRHR}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_STACK=${LOG_STACK:-stderr,single}
LOG_LEVEL=${LOG_LEVEL:-info}

DB_CONNECTION=${DB_CONNECTION:-sqlite}
DB_DATABASE=${DB_DATABASE:-database/database.sqlite}

SESSION_DRIVER=${SESSION_DRIVER:-file}
CACHE_STORE=${CACHE_STORE:-file}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-public}
EOF

chown www-data:www-data .env || true

if [ "${DB_CONNECTION:-}" = "sqlite" ] && [ -n "${DB_DATABASE:-}" ]; then
    mkdir -p "$(dirname "$DB_DATABASE")"
    touch "$DB_DATABASE"
    chown -R www-data:www-data "$(dirname "$DB_DATABASE")" || true
fi

if [ ! -L public/storage ]; then
    echo "[entrypoint] ensuring public storage symlink"
    php artisan storage:link --force --no-interaction || true
fi

echo "[entrypoint] clearing cached Laravel bootstrap state"
php artisan optimize:clear || true

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
