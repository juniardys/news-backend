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
COPY . .

# Install python
RUN apt-get install -y python3 python3-pip

# Install Cron
RUN apt-get install -y cron
RUN echo "* * * * * cd /var/www/app && /usr/local/bin/php artisan schedule:run schedule:run >> /var/log/cron.log 2>&1" >> /etc/cron.d/cron
RUN chmod 0644 /etc/cron.d/cron
RUN crontab -u root /etc/cron.d/cron
RUN touch /var/log/cron.log

# Expose port
EXPOSE 9000

# Start the application
CMD bash -c 'cron && php-fpm'
