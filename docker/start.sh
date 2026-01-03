#!/bin/sh
set -e

echo "Starting RealMan Livestock application..."

# Wait for database to be ready
echo "Waiting for database connection..."
php /var/www/html/artisan db:show || echo "Database not ready yet..."

# Run database migrations
echo "Running database migrations..."
php /var/www/html/artisan migrate --force --no-interaction || echo "Migrations failed or already up to date"

# Clear and cache configuration
echo "Optimizing application..."
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache
php /var/www/html/artisan event:cache

# Create storage link if not exists
if [ ! -L /var/www/html/public/storage ]; then
    echo "Creating storage symlink..."
    php /var/www/html/artisan storage:link
fi

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start supervisord
echo "Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
