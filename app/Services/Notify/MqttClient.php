<?php

namespace App\Services\Notify;

use App\Models\User;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class MqttClient implements NotifierInterface
{
    /**
     * @var AMQPChannel
     */
    private $channel;

    public function __construct(AMQPChannel$channel)
    {
        $this->channel = $channel;
    }

    /**
     * @inheritDoc
     */
    public function send(User $user, string $message)
    {
        $body = json_encode([
            'message' => $message,
            'username' => $user->name
        ]);

        $msg = new AMQPMessage($body);
        $this->channel->basic_publish($msg, '', env('RABBITMQ_NOTIFICATION_QUEUE'));
    }
}
