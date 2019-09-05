#!/bin/sh -l
set -eu

#  Setup Laravel App
cp .env.example .env
php artisan key:generate
#php artisan migrate

#  Run phpunit Tests
vendor/bin/phpunit $*
