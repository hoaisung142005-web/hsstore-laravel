#!/bin/bash

echo "🧹 Clearing old caches..."
php artisan optimize:clear || true
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/views/*

echo "🔗 Creating storage symlink..."
php artisan storage:link || true

echo "🧽 Fixing permissions..."
chmod -R 775 storage bootstrap/cache || true

echo "⏳ Waiting for database to be ready..."
sleep 10

echo "📦 Running migrations and seeders..."
php artisan migrate --force || true
php artisan db:seed --class=ProductsTableSeeder --force || true
php artisan db:seed --class=ReviewSeeder --force || true

echo "⚙️ Rebuilding caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "🚀 Starting Laravel app on Railway..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
