#!/bin/bash
set -e

echo "=========================================="
echo "Starting Laravel Deployment Setup..."
echo "=========================================="

# Wait for database to be ready
echo "Waiting for database connection..."
sleep 10

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Cache config for better performance
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=========================================="
echo "Setup complete! Starting Apache..."
echo "=========================================="

# Start Apache
exec apache2-foreground