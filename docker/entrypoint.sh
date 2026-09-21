#!/usr/bin/env sh
set -e

cd /var/www/html

role="${1:-web}"

# Directorios de escritura necesarios.
mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# El enlace público hacia storage/app/public (logos, portadas, imágenes).
php artisan storage:link >/dev/null 2>&1 || true

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

wait_for_db() {
    attempt=0
    until php artisan migrate:status >/dev/null 2>&1; do
        attempt=$((attempt + 1))
        if [ "$attempt" -ge 40 ]; then
            echo "[entrypoint] No se pudo conectar a la base de datos tras ${attempt} intentos." >&2
            return 1
        fi
        echo "[entrypoint] Esperando base de datos... (intento $attempt)"
        sleep 3
    done
}

prepare_app() {
    php artisan package:discover --ansi
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache || true
}

case "$role" in
    web)
        wait_for_db
        php artisan migrate --force
        prepare_app
        echo "[entrypoint] Iniciando nginx + php-fpm."
        exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
        ;;
    worker)
        wait_for_db
        prepare_app
        echo "[entrypoint] Iniciando worker de colas."
        exec php artisan queue:work --sleep=3 --tries=3 --max-time=3600
        ;;
    scheduler)
        wait_for_db
        prepare_app
        echo "[entrypoint] Iniciando scheduler."
        while true; do
            php artisan schedule:run --verbose --no-interaction
            sleep 60
        done
        ;;
    *)
        exec "$@"
        ;;
esac
