<?php

namespace App\Repositories\Users;

use App\Models\User;

class SqlUserRepository implements UserRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function findByName($name)
    {
        $user = User::query()
            ->where('name', '=', $name)
            ->first()
        ;

        return $user;
    }
}
