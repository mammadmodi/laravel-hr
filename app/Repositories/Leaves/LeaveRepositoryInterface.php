<?php

namespace App\Repositories\Leaves;

use App\Models\Department;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * Returns all managers for department.
     *
     * @param Department $department
     * @return Collection
     */
    public function getDepartmentManagers(Department $department);
}
