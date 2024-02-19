#!/bin/bash

set -e

# Base env
WORKDIR="/srv/www"
env_file=".env"
env_example=".env.example"

# Application name, parsed from .env file
app_name=$(cat $env_file | grep -m 1 APP_NAME= | sed -E 's/^APP_NAME=//')

# Database configuration, parsed from .env file
database_name=$(cat $env_file | grep -m 1 DB_DATABASE= | sed -E 's/^DB_DATABASE=//')
database_host=$(cat $env_file | grep -m 1 DB_HOST= | sed -E 's/^DB_HOST=//')
database_port=$(cat $env_file | grep -m 1 DB_PORT= | sed -E 's/^DB_PORT=//')
database_user=$(cat $env_file | grep -m 1 DB_USERNAME= | sed -E 's/^DB_USERNAME=//')
database_password=$(cat $env_file | grep -m 1 DB_PASSWORD= | sed -E 's/^DB_PASSWORD=//')
database_driver=$(cat $env_file | grep -m 1 DB_CONNECTION= | sed -E 's/^DB_CONNECTION=//')

database_hash=$(echo -n "$database_name$database_host$database_port$database_user$database_password$database_driver" | md5sum | awk '{print $1}')

# Mercury runtime version
MERCURY_VERSION="0.2"

cd $WORKDIR

# Composer PATH
COMPOSER_PATH=$(which composer)

# Pnpm PATH
PNPM_PATH=$(which pnpm)

# PHP PATH
PHP_PATH="/usr/local/bin/php"

# Logger vars
Color_Off='\033[0m'
Red='\033[1;31m'
Green='\033[0;32m'
Yellow='\033[0;33m'
Blue='\033[0;34m'
Purple='\033[1;35m'
