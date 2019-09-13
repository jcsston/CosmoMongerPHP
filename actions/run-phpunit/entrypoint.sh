#!/bin/sh -l
set -eu

#  Setup Laravel App
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed

#  Run phpunit Tests
vendor/bin/phpunit $*
