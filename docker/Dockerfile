FROM php:8.4-fpm
ENV DEBIAN_FRONTEND=noninteractive

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apt-get update \
    && apt-get -y install \
        unzip \
        sudo \
        procps \
        vim \
        git \
    && apt-get clean

RUN curl -sSk https://getcomposer.org/installer | php -- --install-dir /usr/local/bin --filename composer

RUN useradd -u 1000 -G sudo -s /bin/bash -m php \
    && echo root:php | chpasswd \
    && echo php:php | chpasswd
