#!/bin/bash

source /mercury/setenv.sh
source /mercury/logger.sh

laravel_cache() {
    echo "# Cleaning Laravel caches..."
    /usr/local/bin/php artisan config:cache
    /usr/local/bin/php artisan optimize:clear
}

laravel_env() {
    if [ ! -f $env_file ]; then
        log_warning "# No .env file found! Copying example..."
        cp $env_example $env_file
        # I shall not comment this one lol
        chmod 666 $env_file
        log_trace "# Generating app key..."
        /usr/local/bin/php artisan key:generate
    fi
}

laravel_migrate() {
    log_info "# Running migrations..."
    $PHP_PATH artisan migrate --force
}
