FROM gliderlabs/alpine

LABEL maintainer="jefrancomix@gmail.com"

RUN apk update && \
    # NginX install
    apk add nginx && \
    # NginX permissions
    adduser -D -g 'www' www && \
    mkdir /www && \
    mkdir /run/nginx && \
    chown www:www /var/lib/nginx && \
    chown www:www /www && \
    chown www:www /run/nginx && \
    # backup original config
    cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.bak
    
    # PHP7 installation
RUN apk add \
    # php-cli
    php7 \
    php7-fpm \
    # php.ini
    php7-common \
    php7-opcache \
    # minimal deps for composer and many more things
    php7-openssl php7-curl php7-json php7-phar php7-iconv php7-zlib \
    # dependencies for phpunit
    php7-dom php7-mbstring php7-xml php7-xmlwriter php7-tokenizer php7-xdebug && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php -- --install-dir=/usr/bin/ --filename=composer && \
    php -r "unlink('composer-setup.php'); "

ADD deploy/nginx.conf /etc/nginx/nginx.conf

WORKDIR /www
ADD deploy/index.html index.html
ADD . php-app/

WORKDIR /www/php-app
RUN composer install
RUN echo zend_extension=xdebug.so > /etc/php7/conf.d/xdebug.ini
RUN vendor/bin/phpunit --exclude-group RequestHandlerRefactor

EXPOSE 80

ENTRYPOINT ["nginx"]
