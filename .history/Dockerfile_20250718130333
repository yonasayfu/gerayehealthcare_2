FROM php:8.2-apache

# Install system dependencies
# Ensure bash is available, git, unzip, libpq-dev, libzip-dev, zip, curl
RUN apt-get update && apt-get install -y \
    bash \ # Added bash for robustness
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    && rm -rf /var/lib/apt/lists/* \ # Clean up apt cache to reduce image size
    && docker-php-ext-install pdo pdo_pgsql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory inside the container
WORKDIR /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy Composer files and install PHP dependencies (only production dependencies)
# Copy these files before copying the entire app to leverage Docker cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Install Node.js and npm (using Node.js 20.x from NodeSource for consistency and proper PATH setup)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/* # Clean up apt cache

# Copy Node.js package files and install frontend dependencies
# Copy these files before copying the entire app to leverage Docker cache
COPY package.json package-lock.json ./
RUN npm install

# Copy the rest of the Laravel project files into the working directory
COPY . .

# Build frontend assets for production
# Added --verbose for more output in logs in case of failure
RUN npm run build -- --verbose

# Optimize Laravel for production environments
# Caching configuration, routes, and views for faster performance
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Run Laravel database migrations
# The --force flag is used for non-interactive environments like Docker builds
RUN php artisan migrate --force

# Set proper permissions for the web server to access files
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Configure Apache's DocumentRoot to point to Laravel's public directory
# This ensures Apache serves your application from the correct entry point
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Add a custom Apache configuration file for Laravel, enabling .htaccess support
# This is crucial for Laravel's URL rewriting (mod_rewrite)
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Expose port 80, which is the default HTTP port Apache listens on
EXPOSE 80

# Command to run when the container starts
# 'apache2-foreground' keeps Apache running in the foreground, essential for Docker
CMD ["apache2-foreground"]