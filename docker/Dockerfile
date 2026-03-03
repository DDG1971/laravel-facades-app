# Этап 1: Node.js для сборки ассетов
FROM node:20 AS build

# Рабочая директория внутри контейнера
WORKDIR /app/laravel
# Копируем package.json и lock
COPY laravel/package*.json ./
RUN npm install
# Копируем конфиги Vite/Tailwind/PostCSS
COPY laravel/vite.config.js laravel/tailwind.config.js laravel/postcss.config.js ./
# Копируем весь Laravel‑проект
COPY laravel/ ./
# Сборка ассетов
RUN npm run build

#RUN cp public/build/.vite/manifest.json public/build/manifest.json
# Этап 2: PHP-FPM с Laravel
FROM php:8.2-fpm

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql zip

# Устанавливаем Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Рабочая директория внутри контейнера
WORKDIR /var/www/html

# Сначала копируем только composer.json и composer.lock
COPY laravel/composer.json laravel/composer.lock ./

# Устанавливаем зависимости
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Копируем Laravel‑проект
COPY laravel ./

# Теперь запускаем скрипты Composer (artisan уже есть)
RUN composer install --no-dev --optimize-autoloader

# Копируем собранные ассеты
COPY --from=build /app/laravel/public/build ./public/build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache









