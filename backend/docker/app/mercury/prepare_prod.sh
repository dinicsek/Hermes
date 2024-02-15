#!/bin/bash
source /mercury/index.sh

npm
pnpm install @soketi/soketi

$PHP_PATH artisan storage:link
