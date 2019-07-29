FROM registry.snapp.tech/docker/php:7.2-cli-alpine3.8

WORKDIR /app
RUN apk update && \
    apk add php7-redis &&\
    apk add wget

RUN php -m
COPY . /app

CMD "php /app/artisan serve --port 8091"
