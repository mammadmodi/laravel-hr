<?php

namespace App\Services\Notify;

use App\Models\User;
use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MqttClient implements NotifierInterface
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function send(User $user, string $message)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare(env('RABBITMQ_NOTIFICATION_QUEUE'), false, false, false, false);

        $body = json_encode([
            'message' => $message,
            'username' => $user->name,
            'topic' => $user->getPrivateTopic(),
        ]);

        $msg = new AMQPMessage($body);
        $channel->basic_publish($msg, '', env('RABBITMQ_NOTIFICATION_QUEUE'));

        $channel->close();
        $this->connection->close();
    }
}
