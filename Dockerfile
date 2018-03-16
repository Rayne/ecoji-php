FROM alpine:3.7

WORKDIR /app

COPY composer.json /app/
COPY assets /app/assets/
COPY bin /app/bin/
COPY src /app/src/

RUN apk --no-cache add \
        php7 \
        php7-json \
        php7-mbstring \
        php7-openssl \
        php7-phar \
        php7-posix \
 && adduser user -D \
 && chown -R user /app

#
# Install Composer and let it generate the autoloader.
#
# Q: Why `--ignore-platform-reqs`?
# A: Composer will otherwise check the dependencies of the dev packages,
#    although we are not installing the dev packages …
#

USER user

RUN wget https://getcomposer.org/installer -O composer-setup.php \
 && php composer-setup.php --install-dir=/app --filename=composer \
 && php composer install --no-dev --no-interaction --optimize-autoloader --ignore-platform-reqs \
 && rm composer composer-setup.php

USER root

RUN rm -r /home/user \
 && ln -s /app/bin/ecoji /usr/local/bin \
 && apk del \
        php7-json \
        php7-openssl \
        php7-phar

User user

ENTRYPOINT ["/app/bin/ecoji"]
