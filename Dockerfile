FROM php:8.1-cli

# Устанавливаем нужные зависимости
RUN apt-get update && \
    apt-get install -y unzip git zip libzip-dev libpng-dev libonig-dev libxml2-dev default-mysql-client && \
    docker-php-ext-install zip pdo_mysql

# Установим Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создаём рабочую папку
WORKDIR /app

# Копируем все файлы
COPY . .

# Устанавливаем зависимости
RUN composer install --no-dev

# Открываем порт
EXPOSE 8080

# Запускаем приложение
CMD ["php", "-S", "0.0.0.0:8080", "-t", "web"]
