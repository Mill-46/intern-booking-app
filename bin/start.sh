set -eu

# Railway runtime port is injected via $PORT.
# This script waits for the database (if configured), runs migrations when
# reachable, clears config/view/route caches to ensure runtime reads env vars,
# then starts the PHP built-in server.

DB_HOST=${DB_HOST:-}
DB_PORT=${DB_PORT:-}

if [ -z "$DB_HOST" ] && [ -n "${DATABASE_URL:-}" ]; then
  DB_HOST=$(php -r '$u=parse_url(getenv("DATABASE_URL")); echo $u["host"] ?? "";') || true
  DB_PORT=$(php -r '$u=parse_url(getenv("DATABASE_URL")); echo $u["port"] ?? "5432";') || true
fi

RETRIES=12
DB_AVAILABLE=1
if [ -n "$DB_HOST" ]; then
  until php -r 'if(@fsockopen($argv[1], (int)$argv[2])) { exit(0);} exit(1);' "$DB_HOST" "${DB_PORT:-5432}"; do
    RETRIES=$((RETRIES-1))
    if [ "$RETRIES" -le 0 ]; then
      echo "DB not available, skipping migrations"
      DB_AVAILABLE=0
      break
    fi
    echo "Waiting for DB... sleeping 5s"
    sleep 5
  done
fi

# Ensure config/view/route cache does not lock-in build-time env values
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

if [ "$DB_AVAILABLE" -eq 1 ] && [ -n "${DB_HOST}" ]; then
  echo "Running migrations"
  php artisan migrate --force || true
fi

php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
