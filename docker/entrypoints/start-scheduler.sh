#!/usr/bin/env bash
set -e
# scheduler "daemon" without cron inside of container
php artisan schedule:work
