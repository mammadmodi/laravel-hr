<?php

namespace App\Services\Notify;

use App\Models\User;

interface NotifierInterface
{
    /**
     * Tries to send notification to user.
     *
     * @param User $user
     * @param string $message
     * @return boolean
     */
    public function send(User $user, string $message);
}
