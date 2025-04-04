FROM php:8.1-apache

# 필요한 라이브러리 설치
RUN apt-get update && apt-get install -y libicu-dev

# PHP 확장 설치
RUN docker-php-ext-install mysqli pdo pdo_mysql intl

# DocumentRoot를 public 폴더로 변경
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# 전체 소스 복사 (개발 환경)
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
