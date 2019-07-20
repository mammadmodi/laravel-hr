<?php

namespace Tests;

use App\Models\Leave;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Gets an user with number of leaves.
     *
     * @param int $leavesNumber
     * @param null $role
     * @return User
     */
    protected function getUserWithLeaves($leavesNumber = 10, $role = null)
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        if (is_string($role) && Role::findByName($role) != null) {
            $user->assignRole($role);
        }

        factory(Leave::class, $leavesNumber)->create(['user_id' => $user->id]);

        return $user;
    }

}
