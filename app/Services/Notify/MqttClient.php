<?php

namespace App\Services\Notify;

use App\Models\User;

class MqttClient implements NotifierInterface
{
    /**
     * @inheritDoc
     */
    public function send(User $user, string $message)
    {
        echo "Message : { $message } sent successfully to $user->name.\n";
    }
}
