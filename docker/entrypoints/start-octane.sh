#!/usr/bin/env bash
set -e

php artisan optimize:clear

# Init Octane with Swoole/OpenSwoole
php artisan octane:start \
  --server=swoole \
  --host=0.0.0.0 \
  --port=8000 \
  --workers="${OCTANE_WORKERS:-auto}" \
  --max-requests="${OCTANE_MAX_REQUESTS:-500}"
