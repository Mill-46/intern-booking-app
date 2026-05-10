set -eu

DB_HOST=$(php -r "echo getenv('DB_HOST') ?? getenv('PGHOST') ?? getenv('DATABASE_HOST') ?? '';" )
# fallback ke parse DATABASE_URL jika perlu (omitted for brevity)

# wait for db (example 60s total)
RETRIES=12
until php -r "exit((bool)@fsockopen('${DB_HOST}', ${DB_PORT:-5432}) ? 0 : 1);" ; do
  RETRIES=$((RETRIES-1))
  if [ "$RETRIES" -le 0 ]; then
    echo "DB not available, skipping migrations"
    break
  fi
  echo "Waiting for DB... sleeping 5s"
  sleep 5
done

# run migrations only if DB reachable
php artisan migrate --force || true

# start server
php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
