<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Department
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property User[] $users
 */
class Department extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Returns users of this department.
     *
     * @return HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
