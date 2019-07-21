<?php

namespace Tests;

use App\Models\Leave;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Generates a valid token.
     *
     * @param null $role
     * @return string
     */
    protected function getValidToken($role = null)
    {
        $user = $this->createUser($role);

        return $this->makeToken($user);
    }

    /**
     * Generates a not valid token.
     *
     * @return string
     */
    protected function getNotValidToken()
    {
        $user = factory(User::class)->make(['name' => 'not_exist_user']);

        return $this->makeToken($user);
    }

    /**
     * Generates a valid token for entry user.
     *
     * @param User $user
     * @return string
     */
    protected function makeToken(User $user)
    {
        return JWTAuth::fromUser($user);
    }

    /**
     * Creates a new user.
     *
     * @param null $role
     * @return User
     */
    protected function createUser($role = null)
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        if (is_string($role) && Role::findByName($role) != null) {
            $user->assignRole($role);
        }

        return $user;
    }

    /**
     * Adds leaves for an user.
     *
     * @param User $user
     * @param int $leavesNumber
     */
    protected function addLeavesToUser(User $user, $leavesNumber = 10)
    {
        factory(Leave::class, $leavesNumber)->create(['user_id' => $user->id]);
    }

    /**
     * Gets an user with number of leaves.
     *
     * @param int $leavesNumber
     * @param null $role
     * @return User
     */
    protected function getUserWithLeaves($leavesNumber = 10, $role = null)
    {
        $user = $this->createUser($role);
        $this->addLeavesToUser($user, $leavesNumber);

        return $user;
    }
}
