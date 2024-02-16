#!/bin/bash

set -e

cd /data/hermes/

previous_container_id=$(docker ps -aqf "name=hermes_container")
if [ ! -z "$previous_container_id" ]; then
  echo "Stopping previous container with ID: $previous_container_id"
  docker stop "$previous_container_id"
  docker rm "$previous_container_id"
fi

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

echo "Starting production container..."
container_name="hermes_container_$(date +"%Y-%m-%d_%H-%M-%S")"
image_name=$(basename "$latest_tarball" .docker | cut -d ':' -f 1)
docker run -d --name "$container_name" "$image_name"
echo "Production container started."