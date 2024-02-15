#!/bin/bash

source /mercury/index.sh

export POSTGRES_USER=$database_user
export POSTGRES_PASSWORD=$database_password
export POSTGRES_DB=$database_name

$(which composer) dump-autoload
$PHP_PATH artisan optimize

$PHP_PATH artisan mercury:prepare-production

mkdir -p /var/log/supervisor
touch /var/log/supervisor/supervisord.log

mkdir -p /var/log/nginx
chown -R aurora:aurora /var/log/nginx

chown root:aurora /etc/nginx/nginx.conf
chmod 640 /etc/nginx/nginx.conf

/usr/bin/supervisord -c /etc/supervisord.conf
