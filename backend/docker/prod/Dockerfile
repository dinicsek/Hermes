FROM redis:7-alpine as redis

RUN apk add --no-cache rsync && \
    rsync -avz /usr/local/bin/redis* /redis && \
    apk del --no-network rsync

FROM postgres:16-alpine as postgres

FROM mercury
WORKDIR /srv/www/

RUN apk del .mercury-build-deps

RUN apk add --no-cache rsync tzdata postgresql16 su-exec

COPY --from=redis /redis /redis
COPY --from=redis /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint_redis.sh

# From https://github.com/docker-library/redis/blob/b77450d34ae54354f41970fc44bf840353f59ef4/7.2/alpine/Dockerfile
RUN set -eux; \
    # alpine already has a gid 999, so we'll use the next id
    addgroup -S -g 1000 redis; \
    adduser -S -G redis -u 999 redis

# From https://github.com/docker-library/redis/blob/b77450d34ae54354f41970fc44bf840353f59ef4/7.2/alpine/Dockerfile
RUN set -eux; \
    # alpine already has a gid 999, so we'll use the next id
    addgroup -S -g 1001 aurora; \
    adduser -S -G aurora -u 1000 aurora

# 70 is the standard uid/gid for "postgres" in Alpine
# https://git.alpinelinux.org/aports/tree/main/postgresql/postgresql.pre-install?h=3.12-stable
RUN set -eux; \
    deluser postgres; \
    addgroup -g 70 -S postgres; \
    adduser -u 70 -S -D -G postgres -H -h /var/lib/postgresql -s /bin/sh postgres; \
    mkdir -p /var/lib/postgresql; \
    chown -R postgres:postgres /var/lib/postgresql


RUN rsync -avz /redis/* /usr/local/bin/ && \
    rm -rf /redis

RUN mkdir /redis-data && chown redis:redis /redis-data
VOLUME /redis-data

RUN mkdir /docker-entrypoint-initdb.d

COPY --from=postgres /usr/local/share/postgresql /usr/local/share/postgresql
COPY --from=postgres /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint_postgresql.sh
COPY --from=postgres /usr/local/bin/docker-ensure-initdb.sh /usr/local/bin/docker-ensure-initdb.sh
RUN ln -sT docker-ensure-initdb.sh /usr/local/bin/docker-enforce-initdb.sh

RUN mkdir -p /var/run/postgresql && chown -R postgres:postgres /var/run/postgresql && chmod 3777 /var/run/postgresql

ENV PGDATA /var/lib/postgresql/data
# this 1777 will be replaced by 0700 at runtime (allows semi-arbitrary "--user" values)
RUN mkdir -p "$PGDATA" && chown -R postgres:postgres "$PGDATA" && chmod 1777 "$PGDATA"
VOLUME /var/lib/postgresql/data

WORKDIR /srv/www/

COPY storage/aurora/temp/assets/git_commit /current_git_commit
COPY storage/aurora/temp/assets/nginx.conf /etc/nginx/nginx.conf
COPY storage/aurora/temp/assets/redis.conf /etc/redis.conf
COPY storage/aurora/temp/assets/supervisord.conf /etc/supervisord.conf
COPY storage/aurora/temp/assets/supervisord/* /etc/supervisor/conf.d/.
COPY storage/aurora/temp/assets/crontabs /var/spool/cron/crontabs/
COPY storage/aurora/temp/assets/entrypoint.sh /usr/local/bin/mercury_entrypoint.sh

COPY storage/aurora/temp/source /srv/www/
RUN rm -f Dockerfile Dockerfile.aurora.bak
RUN chown -R aurora:aurora /srv/www

RUN composer install --no-dev --no-interaction --no-suggest --optimize-autoloader --classmap-authoritative

COPY .env.production /srv/www/.env
RUN chown aurora:aurora /srv/www/.env

RUN su-exec aurora:1001 bash /mercury/prepare_prod.sh

RUN touch /is_prod

VOLUME /root
VOLUME /srv/www/storage
VOLUME /srv/www/bootstrap/cache

EXPOSE 80
EXPOSE 443
STOPSIGNAL SIGINT

CMD ["bash", "/usr/local/bin/mercury_entrypoint.sh"]
