FROM php:7.4.24-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update

RUN apt-get install -y git

RUN docker-php-ext-install pdo_mysql

RUN apt-get install -y librabbitmq-dev
RUN pecl install amqp
RUN docker-php-ext-enable amqp

RUN pecl install redis &&  docker-php-ext-enable redis

EXPOSE 9000