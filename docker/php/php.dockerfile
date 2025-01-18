# Usa la imagen base oficial de PHP
FROM php:8.2-fpm

# Instala dependencias del sistema necesarias para Laravel y PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip

# Instala Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copia los archivos del proyecto Laravel al contenedor
COPY ./src /var/www/html

# Instala las dependencias de Composer y prepara el entorno de Laravel
RUN composer install --no-scripts --no-autoloader || true && \
    composer dump-autoload && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Limpia archivos temporales para reducir el tamaño de la imagen
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
