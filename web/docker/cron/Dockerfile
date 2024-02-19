FROM mercury
WORKDIR /srv/www/

RUN pecl uninstall xdebug

COPY crontabs /var/spool/cron/crontabs/

CMD ["crond", "-f", "-l", "8", "-L", "/dev/stdout"]
