FROM php:8.1-apache

# Instala extensão PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilita mod_rewrite do Apache
RUN a2enmod rewrite

# Configura o DocumentRoot para a pasta www
ENV APACHE_DOCUMENT_ROOT /var/www/html/www
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instala o Composer dentro do container
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