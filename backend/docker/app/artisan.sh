#!/bin/bash
set -e
$(which php) -d xdebug.mode=off /srv/www/artisan $@
