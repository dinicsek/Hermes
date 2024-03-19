FROM mercury
WORKDIR /srv/www/

RUN pecl uninstall xdebug

COPY ./mercury_horizon.sh /usr/local/bin/mercury_horizon.sh
RUN chmod +x /usr/local/bin/mercury_horizon.sh

CMD ["bash", "/usr/local/bin/mercury_horizon.sh"]
