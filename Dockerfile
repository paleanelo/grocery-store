FROM php:8.1-cli

# Установим необходимые зависимости
RUN apt-get update && apt-get install -y unzip git zip libzip-dev && \
    docker-php-ext-install zip

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

# Стартуем сервер
CMD ["php", "-S", "0.0.0.0:8080", "-t", "web"]
