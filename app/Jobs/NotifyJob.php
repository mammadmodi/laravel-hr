<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Notify\NotifierInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $message;

    /**
     * @var NotifierInterface
     */
    private $notifier;

    /**
     * NotifyJob constructor.
     *
     * @param User $user
     * @param string $message
     */
    public function __construct(User $user, string $message)
    {
        $this->user = $user;
        $this->message = $message;
        $this->notifier = app(NotifierInterface::class);
    }

    /**
     * Sends a notify to user.
     *
     * @return boolean
     */
    public function handle()
    {
        return $this->notifier->send($this->user, $this->message);
    }
}
