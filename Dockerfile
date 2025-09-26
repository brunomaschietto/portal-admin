FROM php:8.1-apache

# Instala extensão PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilita mod_rewrite do Apache (opcional, mas útil)
RUN a2enmod rewrite
