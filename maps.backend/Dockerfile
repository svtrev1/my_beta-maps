# Используем официальный образ PHP-FPM (последний 8.2.x)
FROM php:8.2-fpm

# Устанавливаем только необходимые зависимости и без «рекомендованных»
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libpng-dev \
      libjpeg-dev \
      libfreetype6-dev \
      libxml2-dev \
      libpq-dev \
      libzip-dev \
      zip \
      unzip \
      git \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
      gd \
      pdo_pgsql \
      pgsql \
 && rm -rf /var/lib/apt/lists/*

# Логирование ошибок
RUN { \
     echo "memory_limit = 256M"; \
     echo "max_execution_time = 120"; \
     echo "opcache.enable=0"; \
     echo "error_log = /var/log/php_errors.log"; \
     echo "log_errors = On"; \
     echo "display_errors = On"; \
     echo "error_reporting = E_ALL"; \
   } > /usr/local/etc/php/conf.d/custom.ini \
 && touch /var/log/php_errors.log \
 && chmod 777 /var/log/php_errors.log

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Копируем только файлы зависимостей
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Копируем весь остальной код после установки зависимостей
COPY . .

# Правильные права
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache public

EXPOSE 9000

CMD ["php-fpm", "-F", "-R"]
