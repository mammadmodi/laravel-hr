<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;

require_once __DIR__."/vendor/autoload.php";

$connection = new AMQPStreamConnection(
    "rabbitmq",
    5672,
    "user35T",
    "kaq46srwL6awLna0"
);

$channel = $connection->channel();



$channel->queue_declare('notification', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('notification', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();