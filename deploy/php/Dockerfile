FROM php:8.4-fpm-alpine

# Installing essential dependencies
RUN apk add --no-cache \
    linux-headers \
    curl \
    git \
    build-base \
    autoconf \
    bash \
    tzdata

# Installing PostgreSQL dependencies
RUN apk add --no-cache postgresql-dev && docker-php-ext-install pdo_pgsql

ENV TZ=Asia/Almaty

# Setting system timezone
RUN cp /usr/share/zoneinfo/Asia/Almaty /etc/localtime \
    && echo "Asia/Almaty" > /etc/timezone

# Setting working directory
COPY ./ /var/www
WORKDIR /var/www

# Copying Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Running PHP-FPM
CMD ["php-fpm"]

# Exposing port 9000
EXPOSE 9000