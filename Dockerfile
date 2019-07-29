FROM registry.snapp.tech/docker/php:7.2-cli-alpine3.8

WORKDIR /app
RUN apk update && \
    apk add php7-redis &&\
    apk add wget &&\

RUN wget https://github.com/spiral/roadrunner/releases/download/v1.3.5/roadrunner-1.3.5-linux-amd64.tar.gz\
    && tar -xzvf roadrunner-1.3.5-linux-amd64.tar.gz \
    && cp ./roadrunner-1.3.5-linux-amd64/rr /usr/local/bin/ \
    && cp ./roadrunner-1.3.5-linux-amd64/rr /app
COPY . /app
RUN rm -rf ./roadrunner-1.3.5-linux-amd64/ && rm ./roadrunner-1.3.5-linux-amd64.tar.gz
RUN php -m
CMD ["rr", "serve", "-vd"]
