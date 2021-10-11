FROM php:7.4-fpm

ARG user
ARG uid

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libmcrypt-dev

RUN apt-get install -y nodejs npm

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install mcrypt &&  docker-php-ext-enable mcrypt
RUN docker-php-ext-configure gd --enable-gd --with-jpeg

RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

ENV TZ=UTC
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/docker-php-ext-phpini.ini
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN addgroup --gid $uid $user --force-badname && \
    adduser --home /home/laravel --uid $uid --gid $uid --force-badname $user

WORKDIR /

USER $user
