#!/bin/bash

cd /data/hermes/

if [ "$(docker ps -q -f name=hermes_rod)" ]; then
    docker stop hermes_prod
    echo "Production container stopped."
else
    # If not running, do nothing
    echo "Production container is not running."
fi

cd ./backend/

echo "Installing composer dependencies..."
composer install
echo "Composer dependencies installed."

echo "Building production image..."
php artisan aurora:build-production --yes --export --directory=../
echo "Production build complete."

echo "Loading production image..."
cd ../
docker load -i hermes_*.docker
echo "Production image loaded."

echo "Cleaning up production image tarball..."
rm hermes_*.docker
echo "Production image tarball cleaned up."

echo "Starting production container..."
docker run -d -p 80:80 -p 443:443 hermes_prod
echo "Production container started."