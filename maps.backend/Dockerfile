# Используем официальный образ PHP с Nginx
FROM php:8.2-fpm

# Устанавливаем необходимые пакеты
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev zip git

# Устанавливаем расширения PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Копируем файлы проекта в контейнер
COPY . /var/www/html

WORKDIR /var/www/html

# Устанавливаем зависимости Laravel
RUN composer install

# Устанавливаем права на папки
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
