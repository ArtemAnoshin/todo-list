# Запуск проекта

## Запуск Docker контейнеров
```docker-compose up```

## Запуск Composer
```docker-compose exec app composer install```

## Миграции
```docker-compose exec app php artisan migrate```

## Запуск тестов
```docker-compose exec app php artisan test --env=testing```

## Сидеры
```docker-compose exec app php artisan db:seed --class=UserSeeder```
```docker-compose exec app php artisan db:seed --class=TaskSeeder```

## Юзер для теста:
user1@example.com
password1

## Админ для теста:
admin@example.com
password

## переименуйте .env.example в .env

## Фронтенд
```cd .\frontend\```
```npm install```
```npm run dev```




