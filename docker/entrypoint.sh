#!/bin/sh
set -e

cd /var/www/html

# 1. Create .env from .env.example on a fresh clone.
if [ ! -f .env ]; then
    echo "[entrypoint] Creating .env from .env.example"
    cp .env.example .env
fi

# 2. Generate the application key when the .env does not already contain one.
if ! grep -Eq '^APP_KEY=.+' .env; then
    echo "[entrypoint] Generating APP_KEY"
    php artisan key:generate --ansi
fi

# 3. Install Composer dependencies when the vendor directory is missing
#    (for example after a volume wipe).
if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] Installing Composer dependencies"
    composer install --no-interaction --prefer-dist --no-progress
fi

# 4. Create the Laravel runtime directories and make them writable.
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# 5. Create the public/storage symlink so uploaded documents are served.
php artisan storage:link >/dev/null 2>&1 || true

# 6. Drop any stale compiled Blade views carried over from the host.
php artisan view:clear --ansi >/dev/null 2>&1 || true

# Execute the command passed to the container (e.g. php-fpm, queue:work).
exec "$@"