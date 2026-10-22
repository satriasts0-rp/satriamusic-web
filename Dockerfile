FROM php:8.2-apache

# Install ekstensi PostgreSQL untuk koneksi Supabase
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Ambil composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin source code
COPY . .

# Install dependensi Laravel
RUN composer install --no-dev --optimize-autoloader

# Atur izin akses folder storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Arahkan Apache Document Root ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/conf-enabled/*.conf

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

EXPOSE 80
CMD ["apache2-foreground"]