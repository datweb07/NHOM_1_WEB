FROM php:8.2-apache

RUN apt-get update && apt-get install -y git unzip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Bật rewrite URL
RUN a2enmod rewrite

# ÉP THƯ MỤC GỐC VÀO TRONG THƯ MỤC PUBLIC
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# ---> BẮT ĐẦU PHẦN FIX LỖI PORT <---
# Cấu hình Apache sử dụng biến môi trường PORT do hosting cấp (dùng nháy đơn để giữ nguyên chữ ${PORT})
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
# ---> KẾT THÚC PHẦN FIX LỖI PORT <---

WORKDIR /var/www/html/
COPY . /var/www/html/

RUN composer install