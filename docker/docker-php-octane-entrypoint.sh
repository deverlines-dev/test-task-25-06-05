#!/bin/sh

[ ! -d "/var/www/laravel/vendor" ] && composer install
[ ! -d "/var/www/laravel/node_modules" ] && npm install

php artisan octane:start --host=0.0.0.0 --port=8000 --max-requests=512 --workers=8 --task-workers=2 --watch --quiet
