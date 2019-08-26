<?php

namespace App\Repositories\Users;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Finds user by id.
     *
     * @param $name
     * @return User
     */
    public function findByName($name);
}
