FROM alpine:3.13

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk --no-cache add \
        php7 \
        php7-json \
        php7-mbstring \
        php7-openssl \
        php7-phar \
        php7-posix \
        php7-ctype \
        php7-curl \
        php7-dom \
        php7-tokenizer \
        php7-xml \
        php7-xmlwriter \
        php7-pecl-xdebug \
 && echo zend_extension=xdebug.so >> /etc/php7/conf.d/50_xdebug.ini \
 && echo xdebug.mode=coverage >> /etc/php7/conf.d/50_xdebug.ini \
 && adduser user -D

USER user
