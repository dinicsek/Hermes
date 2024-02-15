#!/bin/bash
source /mercury/index.sh

# Totally dumb, and even more unnescessary. To be fair, it does look cool...
figlet "Mercury"
echo ""
log_info "Mercury - Docker Runtime for Aurora (version $MERCURY_VERSION)"
log_trace "Booting your application..."

firstboot

boot
root
