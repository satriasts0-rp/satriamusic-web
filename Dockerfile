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

# Mengaktifkan mod_rewrite dan mengubah konfigurasi port ke 7860
RUN a2enmod rewrite \
    && sed -i 's/80/7860/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf \
    && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Membuat VirtualHost untuk port 7860
RUN echo '<VirtualHost *:7860>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

EXPOSE 7860
EXPOSE 80
CMD ["apache2-foreground"]