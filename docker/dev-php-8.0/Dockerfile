FROM alpine:3.13

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk --no-cache add \
        php8 \
        php8-json \
        php8-mbstring \
        php8-openssl \
        php8-phar \
        php8-posix \
        php8-ctype \
        php8-curl \
        php8-dom \
        php8-tokenizer \
        php8-xml \
        php8-xmlwriter \
        php8-pecl-xdebug \
 && ln -s /usr/bin/php8 /usr/local/bin/php \
 && echo zend_extension=xdebug.so >> /etc/php8/conf.d/50_xdebug.ini \
 && echo xdebug.mode=coverage >> /etc/php8/conf.d/50_xdebug.ini \
 && adduser user -D

USER user
