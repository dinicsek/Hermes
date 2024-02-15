FROM node:18-alpine3.19 as node

RUN rm /usr/local/bin/docker-entrypoint.sh

RUN apk add --no-cache rsync && \
    rsync -avz /usr/local/* /node_usr_local && \
    rsync -avz /opt/* /node_opt && \
    apk del --no-network rsync

FROM php:8.3.2-fpm-alpine3.19
WORKDIR /srv/www

RUN echo 'Installing NodeJS v18...'
COPY --from=node /node_usr_local /node_usr_local
COPY --from=node /node_opt /node_opt
RUN apk add --no-cache rsync && \
    rsync -avz /node_usr_local/* /usr/local/ && \
    rsync -avz /node_opt/* /opt/ && \
    apk del --no-network rsync

RUN echo 'Installing packages...'
RUN apk update && \
    apk add --no-cache --virtual .mercury-build-deps $PHPIZE_DEPS \
    freetype-dev \
    libpng-dev \
    libxml2-dev \
    libpq-dev \
    icu-dev \
    bzip2-dev \
    pcre-dev \
    linux-headers

RUN apk add --no-cache libstdc++ \
    gcompat \
    coreutils \
    postgresql-client \
    figlet \
    mc \
    vim \
    unzip \
    busybox-extras \
    supervisor \
    bash \
    git \
    ca-certificates \
    nginx \
    imagemagick-dev \
    libzip-dev \
    libjpeg-turbo-dev

RUN echo "export PROC_COUNT=`nproc`" >> /envfile
RUN . /envfile; echo "Processors: $PROC_COUNT --> -j$PROC_COUNT"

RUN echo 'Installing PHP extensions...'
ENV MAKEFLAGS="-j$(nproc)"
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN . /envfile; docker-php-ext-install -j$PROC_COUNT bcmath \
    bz2 \
    exif \
    opcache \
    pdo_pgsql \
    zip \
    pcntl \
    gd \
    dom \
    shmop \
    sysvmsg \
    sysvsem \
    sysvshm \
    posix


ENV MAKEFLAGS="-j$(nproc)"
# PECL extensions
RUN MAKEFLAGS="-j$(nproc)" pecl install redis
#RUN MAKEFLAGS="-j$(nproc)" pecl install imagick
RUN MAKEFLAGS="-j$(nproc)" pecl install xdebug
RUN MAKEFLAGS="-j$(nproc)" pecl install openswoole-22.1.2

RUN git clone https://github.com/Imagick/imagick && \
    cd imagick && \
    phpize && ./configure && \
    make -j$(nproc) && \
    make -j$(nproc) install && \
    cd .. && \
    rm -rf imagick

#RUN docker-php-ext-enable redis gd dom openswoole
RUN docker-php-ext-enable redis imagick gd dom openswoole

RUN . /envfile; docker-php-ext-configure intl && docker-php-ext-install -j$PROC_COUNT intl

RUN echo "export COMPOSER_ALLOW_SUPERUSER=1" > /etc/profile.d/composer.sh && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

RUN npm install -g pnpm@latest

RUN touch /mercury.container

# Config 'n stuff
COPY php.ini /usr/local/etc/php/
COPY nginx.conf /etc/nginx/
COPY xdebug.ini /usr/local/etc/php/conf.d/
COPY opcache.ini /usr/local/etc/php/conf.d/
COPY supervisord.conf /etc/
COPY mercury.sh /
COPY mercury_debug.sh /
COPY mercury_swoole.sh /
COPY mercury_kill.sh /
COPY mercury /mercury
COPY artisan.sh /usr/sbin/artisan
RUN chmod +x /usr/sbin/artisan
RUN chmod 777 /mercury.sh /mercury_debug.sh /mercury_swoole.sh /mercury_kill.sh

CMD ["bash", "/mercury.sh"]
