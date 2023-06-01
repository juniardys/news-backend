FROM php:8.1.15-fpm

# Set working directory
WORKDIR /var/www/app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application files
COPY . /var/www/app

# Install python
RUN apt-get install -y python3 python3-pip

# Install Cron
RUN apt-get install -y cron
RUN echo "* * * * * root php /var/www/artisan schedule:run >> /var/log/cron.log 2>&1" >> /etc/crontab
RUN touch /var/log/cron.log

# Run composer install
RUN composer install

# Folder permissions
RUN chown -R www-data:www-data /var/www/app
RUN chmod -R 755 /var/www/app

# Generate laravel key
RUN php artisan key:generate

# Clear laravel cache
RUN php artisan optimize:clear

# Install python requirements
RUN php artisan python:install-requirements

# Expose port
EXPOSE 9000

# Start the application
CMD bash -c 'cron && php-fpm'
