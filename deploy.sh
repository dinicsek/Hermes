#!/bin/bash

set -e

cd /data/hermes/

docker compose down

cd ./backend/

echo "Installing composer dependencies..."
composer install
echo "Composer dependencies installed."

echo "Building production image..."
php artisan aurora:build
php artisan aurora:build-production --yes --export --directory=../
echo "Production build complete."

DOCKER_TARBALL_DIR="/data/hermes"
latest_tarball=$(ls -t "$DOCKER_TARBALL_DIR"/*.docker | head -n 1)

echo "Loading production image..."
cd ../
docker load -i "$latest_tarball"
echo "Production image loaded."

echo "Cleaning up production image tarball..."
rm "$latest_tarball"
echo "Production image tarball cleaned up."

image_name=$(basename "$latest_tarball" .docker | cut -d ':' -f 1)
sed -i "s/REPLACE_IMAGE_NAME_HERE/$image_name/g" docker-compose.yml

echo "Starting production container..."
docker compose up -d
echo "Production container started."

#echo "Starting production container..."
#container_name="hermes_container_$(date +"%Y-%m-%d_%H-%M-%S")"
#docker run -d --name "$container_name" -p 80:80 -p 443:443 "$image_name"
#echo "Production container started."

echo "Deleteing old images..."
docker image prune -a -f
echo "Old images deleted."