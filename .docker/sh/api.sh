#!/bin/bash
echo "Welcome to the MS Card Payment API Container"

echo "Adjusting permission and ownership for Laravel writable directories..."

# Storage (logs, cache, sessions, views)
chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

# Bootstrap cache (compiled files, routes, configs)
chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/bootstrap/cache

echo "Permissions fixed."

# Start Apache in foreground
exec apache2-foreground
