Запуск octane (контейнер php)
```
    php artisan octane:start --host=0.0.0.0 --port=8000 --max-requests=512 --workers=8 --task-workers=2 --watch --quiet
```

Запуск очередей (контейнер php)
```
    php /var/www/laravel/artisan queue:work --name=redis-queue --sleep=0 --quiet >> /var/www/laravel/storage/logs/queue.log
```

Запуск npm-dev (контейнер node) 
```
    npm run dev
```