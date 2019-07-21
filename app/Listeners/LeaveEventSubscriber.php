<?php

namespace App\Listeners;

use App\Events\Leave\Created;
use Illuminate\Events\Dispatcher;

class LeaveEventSubscriber
{
    /**
     * @param Created $event
     */
    public function leaveCreated(Created $event) {
        //TODO queue for pushing message
    }

    /**
     * @param  Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            Created::class,
            'App\Listeners\LeaveEventSubscriber@leaveCreated'
        );
    }
}
