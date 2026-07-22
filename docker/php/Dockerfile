FROM php:8.4-fpm-alpine

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

ARG UID=1000
ARG GID=1000

RUN set -ex && \
    apk update && \
    apk add --no-cache \
        postgresql-dev \
        oniguruma-dev \
        nodejs \
        npm

RUN docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        mbstring

RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/docker-php.ini

RUN addgroup -g ${GID} appgroup \
    && adduser -D -G appgroup -u ${UID} appuser

COPY docker/php/fpm/php-fpm-pool.conf /usr/local/etc/php-fpm.d/appuser.conf

WORKDIR /var/www

RUN chown -R appuser:appgroup /var/www

USER appuser

EXPOSE 9000

CMD ["php-fpm"]
