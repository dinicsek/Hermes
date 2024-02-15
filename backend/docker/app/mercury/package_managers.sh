#!/bin/bash

source /mercury/logger.sh
source /mercury/setenv.sh

composer() {
    log_info "Installing dependencies using Composer..."
    $COMPOSER_PATH update
}

npm() {
    log_info "Installing dependencies using pnpm..."
    log_trace "Removing node_modules..."
    rm -rf node_modules
    log_trace "Invoking pnpm..."
    pnpm update
    log_trace "Installing chokidar..."
    pnpm install chokidar
}
