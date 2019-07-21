<?php

namespace App\Models;

use App\Events\Leave\Created;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

/**
 * Class Leave
 *
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property int $committer_id
 * @property $start
 * @property $end
 * @property string $status
 * @property $created_at
 * @property $updated_at
 * @property User $user
 * @property User $committer
 */
class Leave extends Model
{
    const STATUS_CREATED = 'created';
    const STATUS_WAIT_FOR_APPROVE = 'wait_for_approve';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELED = 'canceled';

    const TRANSITION_CREATE = 'create';
    const TRANSITION_CANCEL = 'cancel';
    const TRANSITION_APPROVE = 'approve';
    const TRANSITION_REJECT = 'reject';

    protected $fillable = [
        'start',
        'end'
    ];

    protected $hidden = [
        'user_id',
        'committer_id'
    ];

    /**
     * Returns all possible states for state finite machine.
     *
     * @return array
     */
    public static function getStates()
    {
        return [
            self::STATUS_CREATED,
            self::STATUS_WAIT_FOR_APPROVE,
            self::STATUS_APPROVED,
            self::STATUS_CANCELED,
            self::STATUS_REJECTED,
        ];
    }

    /**
     * Returns all possible transitions.
     *
     * @return Transition[]
     */
    public static function getTransitions()
    {
        return [
            new Transition(self::TRANSITION_CREATE, self::STATUS_CREATED, self::STATUS_WAIT_FOR_APPROVE),
            new Transition(self::TRANSITION_CANCEL, self::STATUS_WAIT_FOR_APPROVE, self::STATUS_CANCELED),
            new Transition(self::TRANSITION_APPROVE, self::STATUS_WAIT_FOR_APPROVE, self::STATUS_APPROVED),
            new Transition(self::TRANSITION_REJECT, self::STATUS_WAIT_FOR_APPROVE, self::STATUS_REJECTED),
        ];
    }

    /**
     * Returns marking object used for workflow
     *
     * @return MethodMarkingStore
     */
    public static function getMarking()
    {
        return new MethodMarkingStore(true, 'status');
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns related workflow object for a leave.
     *
     * @param Leave $leave
     * @return Workflow
     */
    public static function getWorkflow(Leave $leave)
    {
        /** @var Registry $registry */
        $registry = app(Registry::class);

        return $registry->get($leave);
    }

    /**
     * @inheritDoc
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function(Leave $leave){
            $leave->status = self::STATUS_CREATED;
        });

        self::created(function(Leave $leave){
            self::getWorkflow($leave)
                ->apply($leave, self::TRANSITION_CREATE);
            $leave->save();

            $createdEvent = new Created();
            $createdEvent->setLeave($leave);
            $createdEvent->setTransition(self::TRANSITION_CREATE);
            event($createdEvent);
        });
    }

    /**
     * Get the user that requested this leave.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user that has committed this leave.
     *
     * @return BelongsTo
     */
    public function committer()
    {
        return $this->belongsTo(User::class, 'committer_id');
    }
}
