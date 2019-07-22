FROM php:7.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    build-essential \
    zip \
    vim \
    curl\
    supervisor

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

COPY . /var/www

RUN mkdir -p /var/log/supervisor

COPY docker/supervisor/supervisor.conf /etc/supervisor/supervisord.conf

RUN chown -R  1000:1000 /var/log/supervisor
RUN chown -R  1000:1000 /var/www/storage/logs

USER www

EXPOSE 9000
CMD ["php-fpm"]
