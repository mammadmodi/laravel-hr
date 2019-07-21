<?php

namespace App\Events\Leave;

use App\Models\Leave;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class Created
{
    use Dispatchable, SerializesModels;

    /**
     * @var Leave
     */
    private $leave;

    /**
     * @var string
     */
    private $transition;

    /**
     * @return Leave
     */
    public function getLeave(): Leave
    {
        return $this->leave;
    }

    /**
     * @param Leave $leave
     */
    public function setLeave(Leave $leave): void
    {
        $this->leave = $leave;
    }

    /**
     * @return string
     */
    public function getTransition()
    {
        return $this->transition;
    }

    /**
     * @param string $transition
     */
    public function setTransition(string $transition): void
    {
        $this->transition = $transition;
    }
}
