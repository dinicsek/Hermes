FROM mercury
WORKDIR /srv/www/

RUN pecl uninstall xdebug

CMD ["php", "artisan", "mercury:boot-horizon"]
