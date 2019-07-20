<?php

namespace App\Repositories\Leaves;

use App\Models\Leave;
use App\Models\User;

interface LeaveRepositoryInterface
{
    /**
     * Returns all leaves for an user.
     *
     * @param User $user
     * @param int $perPage
     * @param int $page
     * @return Leave[]
     */
    public function getUsersLeaves(User $user, $perPage = 10, $page = 1);
}
