<?php

namespace App\Listeners;

use App\Events\Leave\Created;
use App\Repositories\Leaves\LeaveRepositoryInterface;
use App\Services\Notify\NotifierInterface;
use Illuminate\Events\Dispatcher;

class LeaveEventSubscriber
{
    /**
     * @var LeaveRepositoryInterface
     */
    private $leaveRepository;

    /**
     * LeaveEventSubscriber constructor.
     */
    public function __construct()
    {
        $this->leaveRepository = app(LeaveRepositoryInterface::class);
    }

    /**
     * @param Created $event
     */
    public function leaveCreated(Created $event) {
        $leave = $event->getLeave();
        $user = $leave->user;
        $leave->save();

        $userManagers = $this->leaveRepository->getDepartmentManagers($user->department);
        $messageBody = "$user->name has requested a leave from $leave->start to $leave->end";

        foreach ($userManagers as $userManager) {
            $mqttCli = app(NotifierInterface::class);
            $mqttCli->send($userManager, $messageBody);
        }
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
