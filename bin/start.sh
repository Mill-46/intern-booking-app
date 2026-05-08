#!/usr/bin/env sh
set -eu

# Railway runtime port is injected via $PORT.
# Do not run migrations/optimize here to avoid boot failure when DB is unavailable.
php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
