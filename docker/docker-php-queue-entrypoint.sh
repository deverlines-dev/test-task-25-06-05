#!/bin/sh

php /var/www/laravel/artisan queue:work --name=redis-queue --sleep=0 --quiet >> /var/www/laravel/storage/logs/queue.log