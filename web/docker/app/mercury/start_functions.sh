#!/bin/bash

source /mercury/setenv.sh
source /mercury/kill_functions.sh
source /mercury/logger.sh
source /mercury/package_managers.sh
source /mercury/lock_functions.sh
source /mercury/laravel_init.sh

swoole_start() {
    log_info "Starting Aurora via OpenSwoole..."
    xdebug_kill
    if [ -f "/is_prod" ]; then
       $PHP_PATH artisan octane:start --host=0.0.0.0 --port=1018
    else
        $PHP_PATH artisan octane:start --watch --host=0.0.0.0 --port=80
    fi
}

debug_start() {
    log_info "Starting Aurora in debug mode..."
    log_trace "Enabling Xdebug..."
    echo "zend_extension=xdebug" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
    touch /var/log/xdebug.log

    log_trace "Setting up supervisord..."
    mkdir -p /var/log/supervisor
    touch /var/log/supervisor/supervisord.log

    log_trace "Boot PHP-FPM with Nginx..."
    /usr/bin/supervisord -c /etc/supervisord.conf
}

boot() {
    log_info "Handing over control to Aurora..."
    if [ -f "/is_prod" ]; then
        su-exec aurora:1001 bash -c "cd /srv/www && $PHP_PATH artisan mercury:boot"
    else
        while true; do $PHP_PATH artisan mercury:boot && break; done
    fi
}

boot_horizon() {
    log_info "Staring Horizon..."
    log_trace "Checking if this is the first start of Mercury..."
    while [ ! -f "/root/mercury-app-$app_name.lock" ]; do
        log_warning "Horizon is not ready yet, waiting for 10 seconds..."
        sleep 10
    done

    log_info "Horizon is ready, starting..."

    $PHP_PATH artisan mercury:boot-horizon
}

firstboot_project() {
   log_trace "Checking if this is the first start of Mercury..."
   if [ ! -f "/root/mercury-app-$app_name.lock" ]; then
       log_info "Preparing $app_name for first run..."
       composer
       npm
       laravel_env
       laravel_cache
       set_lock
   fi
}

READY=0
wait_for_db() {
    log_trace "Waiting for database to be ready..."
    if [ "$database_driver" == "pgsql" ]; then
        log_trace "Waiting for database to be ready..."
        while [ "$READY" -eq 0 ]; do
            if pg_isready -d "postgres://$database_host";
                then READY=1;
                else log_trace "Database not ready yet, waiting for 1 seconds...";
            fi
            sleep 1
        done
        log_info "Database is ready, migrating"
    fi
}

firstboot_db() {
    log_trace "Checking if this database has been prepared..."

    if [ ! -f "/root/mercury-db-$database_hash.lock" ]; then
        log_info "Preparing database for first run..."
        wait_for_db
        laravel_migrate
        set_db_lock
    fi
}

firstboot_db_prod() {
    log_trace "Checking if this database has been prepared..."

    if [ ! -f "/root/mercury-db-$database_hash.lock" ]; then
        log_info "Preparing database for first run..."
        wait_for_db
        laravel_migrate
        set_db_lock
    fi

    DEPLOYED_COMMIT=""
    if [ -f "/root/deployed_git_commit" ]; then
        DEPLOYED_COMMIT=$(cat /root/deployed_git_commit)
    fi

    CURRENT_COMMIT=$(cat /current_git_commit)

    if [ "$CURRENT_COMMIT" != "$DEPLOYED_COMMIT" ]; then
        log_info "New commit detected, running database migrations..."
        wait_for_db
        laravel_migrate
        echo "$CURRENT_COMMIT" > /root/deployed_git_commit
    fi
}

firstboot() {
    if [ -f "/is_prod" ]; then
        firstboot_db_prod
        log_success "Done! Starting $app_name..."
    else
        firstboot_project
        firstboot_db
        log_success "Done! Starting $app_name..."
    fi
}
