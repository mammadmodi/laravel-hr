<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $fillable = [
        'start',
        'end'
    ];

    protected $hidden = [
        'user_id',
        'committer_id'
    ];

    /**
     * @inheritDoc
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function(Leave $leave){
            $leave->status = self::STATUS_WAIT_FOR_APPROVE;
        });

        self::created(function(Leave $leave){
            //TODO dispatch event to notify manager.
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
