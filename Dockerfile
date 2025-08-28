# Usar uma imagem pronta de PHP com servidor Apache
FROM php:8.2-apache

# Instalar ferramentas que o PHP precisa para conectar no MySQL e gerar PDFs
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Instalar o Composer (gerenciador de pacotes do PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir a pasta de trabalho principal dentro do container
WORKDIR /var/www/html

# Copiar seus arquivos composer.json e composer.lock para dentro do container
COPY composer.json composer.lock* ./

# Instalar as dependências do projeto (o dompdf)
RUN composer install

# Copiar todo o resto do seu projeto (os arquivos .php, a pasta Logo, etc.)
COPY . .

# Dar permissão para o servidor Apache acessar os arquivos
RUN chown -R www-data:www-data /var/www/html
