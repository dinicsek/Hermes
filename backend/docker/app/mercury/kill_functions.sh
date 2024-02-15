#!/bin/bash

source /mercury/logger.sh

xdebug_kill() {
    log_info "Disabling Xdebug..."
    if [ -f /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini ]; then
        log_trace "Removing Xdebug configuration..."
        rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
    fi
}

fpm_kill() {
    log_trace "Killing FPM..."
    killall supervisord
}

octane_kill() {
    log_trace "Stopping Octane..."
    /usr/local/bin/php -d xdebug.mode=off /srv/www/artisan octane:stop
}
