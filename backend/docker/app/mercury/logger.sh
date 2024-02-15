#!/bin/bash

source /mercury/setenv.sh

log_header() {
	DATETIME="$(date '+%Y-%m-%d %H:%M:%S')"
	HEADER="$DATETIME [$1] @ Mercury / Bootstrap:"
	echo $HEADER
}

log_info() {
	HEADER="$(log_header 'info')"
	printf "${Blue}${HEADER} ${1}\n${Color_Off}"
}

log_success() {
	HEADER="$(log_header 'success')"
	printf "${Green}${HEADER} ${1}\n${Color_Off}"
}

log_warning() {
	HEADER="$(log_header 'warn')"
	printf "${Yellow}${HEADER} ${1}\n${Color_off}"
}

log_trace() {
	HEADER="$(log_header 'trace')"
	printf "${Purple}${HEADER} ${1}\n${Color_Off}"
}

log_danger() {
	HEADER="$(log_header 'err')"
	printf "${Red}${HEADER} ${1}\n${Color_Off}"
}
