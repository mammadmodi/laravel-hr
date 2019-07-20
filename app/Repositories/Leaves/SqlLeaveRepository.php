<?php

namespace App\Repositories\Leaves;

use App\Models\Leave;
use App\Models\User;

class SqlLeaveRepository implements LeaveRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getUsersLeaves(User $user, $perPage = 10, $page = 1)
    {
        $leaves = Leave::query()
            ->where('user_id', '=', $user->id)
            ->orderBy('id', 'DESC')
            ->paginate($perPage, ['*'], 'page', $page)
            ->items()
        ;

        return $leaves;
    }
}
