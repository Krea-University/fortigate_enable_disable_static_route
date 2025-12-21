#!/bin/bash
set -e

# Create sessions directory if it doesn't exist
mkdir -p /var/www/html/sessions

# Set permissions - make it writable by Apache
chmod 755 /var/www/html/sessions
chown www-data:www-data /var/www/html/sessions

# Enable output buffering to prevent header issues
cat > /usr/local/etc/php/conf.d/buffering.ini <<EOF
output_buffering = On
output_buffering_size = 4096
EOF

# Also ensure .env file exists
if [ ! -f /var/www/html/.env ]; then
    echo "Warning: .env file not found"
fi

# Start Apache
exec apache2-foreground "$@"
