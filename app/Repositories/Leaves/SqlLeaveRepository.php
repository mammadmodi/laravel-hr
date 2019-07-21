<?php

namespace App\Repositories\Leaves;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SqlLeaveRepository implements LeaveRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getUsersLeaves(User $user, $perPage = 10, $page = 1)
    {
        $leaves = DB::table('leaves')
            ->where('user_id', '=', $user->id)
            ->orderBy('id', 'DESC')
            ->paginate($perPage, ['*'], 'page', $page)
            ->items()
        ;

        return $leaves;
    }

    /**
     * @inheritDoc
     */
    public function getDepartmentManagers(Department $department)
    {
        $managers = User::whereHas(
            "roles",
            function($query) {
                $query->where("name", Role::ROLE_MANAGER);
            })
            ->where('department_id', '=', $department->id)
            ->get();

        return $managers;
    }
}
