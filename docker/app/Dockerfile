FROM alpine:3.13

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json /app/
COPY assets /app/assets/
COPY bin /app/bin/
COPY src /app/src/

RUN apk --no-cache add \
        php8 \
        php8-mbstring \
        php8-openssl \
        php8-phar \
        php8-posix \
 && ln -s /usr/bin/php8 /usr/local/bin/php \
 && adduser user -D \
 && chown -R user /app

USER user

#
# Q: Why `--ignore-platform-reqs`?
# A: Composer will otherwise check the dependencies of the dev packages,
#    although we are not installing the dev packages …
#
RUN composer install --no-cache --no-dev --no-interaction --optimize-autoloader --ignore-platform-reqs

USER root

RUN ln -s /app/bin/ecoji /usr/local/bin \
 && apk del \
        php8-openssl \
        php8-phar

User user

ENTRYPOINT ["/app/bin/ecoji"]
