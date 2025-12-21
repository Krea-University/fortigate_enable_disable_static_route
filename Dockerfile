FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Update package manager and install system dependencies
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions
RUN docker-php-ext-install curl

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Create sessions directory inside html
RUN mkdir -p /var/www/html/sessions && chmod 777 /var/www/html/sessions

# Update PHP session settings to use local sessions directory
RUN echo 'session.save_path = "/var/www/html/sessions"' >> /usr/local/etc/php/conf.d/sessions.ini

# Set proper permissions for all files
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html && chmod 777 /var/www/html/sessions

# Copy application files
COPY . /var/www/html/

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 80
EXPOSE 80

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
