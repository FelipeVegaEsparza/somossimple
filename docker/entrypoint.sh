#!/usr/bin/env sh
set -e

cd /var/www/html

role="${1:-web}"

# Directorios de escritura necesarios.
mkdir -p \
    storage/app/public \
    storage/app/backups \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

php artisan storage:link >/dev/null 2>&1 || true

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "[entrypoint] DB -> host=${DB_HOST} port=${DB_PORT} db=${DB_DATABASE} user=${DB_USERNAME}"

# Comprueba la conexión real a la base (sin depender de que exista la tabla de migraciones).
db_check() {
    php -r '
        $host = getenv("DB_HOST") ?: "127.0.0.1";
        $port = getenv("DB_PORT") ?: "3306";
        $db   = getenv("DB_DATABASE") ?: "";
        $user = getenv("DB_USERNAME") ?: "";
        $pass = getenv("DB_PASSWORD") ?: "";
        try {
            new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, [PDO::ATTR_TIMEOUT => 3]);
            exit(0);
        } catch (Throwable $e) {
            fwrite(STDERR, $e->getMessage());
            exit(1);
        }
    ' 2>&1
}

wait_for_db() {
    attempt=0
    while :; do
        if output=$(db_check); then
            echo "[entrypoint] Base de datos disponible."
            return 0
        fi
        attempt=$((attempt + 1))
        echo "[entrypoint] Sin conexión a la base de datos (intento ${attempt}): ${output}"
        if [ "$attempt" -ge 40 ]; then
            echo "[entrypoint] Abortando: no se pudo conectar a la base de datos." >&2
            return 1
        fi
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
