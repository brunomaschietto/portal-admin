FROM php:8.1-apache

# Instala extensão PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilita mod_rewrite do Apache (opcional, mas útil)
RUN a2enmod rewrite

# Instala o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho no Apache
WORKDIR /var/www/html

# Copia composer.json e composer.lock da raiz
COPY composer.json composer.lock ./

# Roda composer install para criar o vendor/ dentro da imagem
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Copia o restante do projeto
COPY . .

# Expõe porta do Apache
EXPOSE 80