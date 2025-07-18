FROM php:8.2-apache

# Install system dependencies (non-PHP-specific)
RUN apt-get update && apt-get install -y \
    bash \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions using the recommended docker-php-ext-install helper
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    mbstring \
    exif \
    gd \
    intl \
    xml \
    bcmath \
    sockets \
    tokenizer \
    fileinfo

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory inside the container
WORKDIR /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy Composer files and install PHP dependencies (only production dependencies)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --verbose

# Install Node.js and npm (using Node.js 20.x from NodeSource for consistency and proper PATH setup)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Copy Node.js package files and install frontend dependencies
COPY package.json package-lock.json ./
RUN npm install

# Copy the rest of the Laravel project files into the working directory
COPY . .

# Build frontend assets for production
RUN npm run build -- --verbose

# Optimize Laravel for production environments
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Run Laravel database migrations
RUN php artisan migrate --force

# Set proper permissions for the web server to access files
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Configure Apache's DocumentRoot to point to Laravel's public directory
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Add a custom Apache configuration file for Laravel, enabling .htaccess support
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Expose port 80
EXPOSE 80

# Command to run when the container starts
CMD ["apache2-foreground"]