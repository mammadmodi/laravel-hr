<?php

namespace App\Services\Notify;

use Illuminate\Support\ServiceProvider;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class NotifierServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('amqp_connection', function () {
            $connection = new AMQPStreamConnection(
                env('RABBITMQ_HOST', 'localhost'),
                env('RABBITMQ_PORT', 5672),
                env('RABBITMQ_USER', 'guest'),
                env('RABBITMQ_PASS', 'guest')
            );

            return $connection;
        });

        $this->app->singleton(MqttClient::class,function () {
            /** @var AMQPStreamConnection $connection */
            $connection = $this->app->get('amqp_connection');

            return new MqttClient($connection);
        });

        $this->app->singleton(NotifierInterface::class,function () {
            return $this->app->get(MqttClient::class);
        });
    }
}
