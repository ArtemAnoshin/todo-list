# Запуск проекта

## Запуск Docker контейнеров
```docker-compose up```

## Запуск Composer
```docker-compose exec app composer install```

## Миграции
```docker-compose exec app php artisan migrate```

## Запуск тестов
```docker-compose exec app php artisan test --env=testing```


