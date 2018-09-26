FROM php:7.2.10-cli-stretch
RUN apt-get update \
    && apt-get install -y autoconf pkg-config libssl-dev zlib1g-dev libpq-dev
RUN docker-php-ext-install bcmath zip iconv mbstring pcntl
RUN mkdir /.composer/ && chmod 777 /.composer/
ENV PATH /var/www/html/bin:/var/www/html/vendor/bin:$PATH