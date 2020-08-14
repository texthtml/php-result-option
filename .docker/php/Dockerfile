FROM php:8.0.0beta1-cli-alpine

RUN apk add --virtual .phpize-deps-configure $PHPIZE_DEPS git autoconf g++ libtool make && \
    git clone https://github.com/nikic/php-ast.git /tmp/php-ast && \
    cd /tmp/php-ast && phpize && ./configure && make install && \
    echo extension=ast.so > /usr/local/etc/php/conf.d/ast.ini && \
    rm -r /tmp/php-ast && \
    apk del .phpize-deps-configure && \
    docker-php-source delete

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer
