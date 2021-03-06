<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $email
 * @property integer $department_id
 * @property Department $department
 * @property Collection $leaves
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @inheritDoc
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @inheritDoc
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the department that owns the user.
     *
     * @return BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * @param $role
     * @return string
     */
    private function generateTopicForRole($role)
    {
        return "topics/" . $role . "/" . $this->name;
    }

    /**
     * Returns all topics that user can subscribe.
     *
     * @return array
     */
    public function getTopics()
    {
        $topics = [];
        $roles = $this->getRoleNames();

        foreach ($roles as $role) {
            /** @var Role $role */
            $topic = $this->generateTopicForRole($role);
            $topics[] = $topic;
        }

        return $topics;
    }

    /**
     * Returns private topic for user.
     *
     * @return string
     */
    public function getPrivateTopic()
    {
        $role = $this->getRoleNames()[0] ?? null;
        if (!empty($role)) {
            return $this->generateTopicForRole($role);
        } else {
            return "";
        }
    }
}
