#!/bin/bash

source /mercury/setenv.sh

set_lock() {
    rm -f "/root/mercury-app-*.lock"
    touch "/root/mercury-app-$app_name.lock"
}

set_db_lock() {
    rm -f "/root/mercury-db-*.lock"
    touch "/root/mercury-db-$database_hash.lock"
}
