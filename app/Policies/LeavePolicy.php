<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Leave;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeavePolicy
{
    use HandlesAuthorization;

    /**
     * Checks that user can see own leaves or not.
     *
     * @param User $user
     * @return bool
     */
    public function indexOwn(User $user)
    {
        return $user->hasPermissionTo(Permission::PERMISSION_INDEX_OWN_LEAVE);
    }

    /**
     * Checks that user can see another user's leaves or not.
     *
     * @param User $manager
     * @param User $employee
     * @return bool
     */
    public function index(User $manager, User $employee)
    {
        return $manager->hasPermissionTo(Permission::PERMISSION_INDEX_USER_LEAVE) &&
            $manager->department_id == $employee->department_id &&
            !$employee->hasAnyRole([Role::ROLE_MANAGER, Role::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can create leaves.
     *
     * @param User $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo(Permission::PERMISSION_CREATE_LEAVE);
    }

    /**
     * Determine whether the user can view another user's leave.
     *
     * @param User $manager
     * @param Leave $leave
     * @param User $employee
     * @return boolean
     */
    public function view(User $manager, Leave $leave, User $employee)
    {
        return $manager->hasPermissionTo(Permission::PERMISSION_VIEW_USER_LEAVE) &&
            $employee->id == $leave->user->id &&
            $manager->department_id == $employee->department_id &&
            !$employee->hasAnyRole([Role::ROLE_MANAGER, Role::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can view own leaves.
     *
     * @param User $user
     * @param Leave $leave
     * @return boolean
     */
    public function viewOwn(User $user, Leave $leave)
    {
        return $user->hasPermissionTo(Permission::PERMISSION_VIEW_OWN_LEAVE) &&
            $leave->user_id == $user->id;
    }

    /**
     * Determine whether the user can cancel the leave.
     *
     * @param User $user
     * @param Leave $leave
     * @return boolean
     */
    public function cancelOwn(User $user, Leave $leave)
    {
        return $user->hasPermissionTo(Permission::PERMISSION_CANCEL_OWN_LEAVE) &&
            $leave->user_id == $user->id;
    }

    /**
     * Determine whether the user can reject the leave.
     *
     * @param User $manager
     * @param Leave $leave
     * @param User $employee
     * @return boolean
     */
    public function reject(User $manager, Leave $leave, User $employee)
    {
        return $manager->hasPermissionTo(Permission::PERMISSION_REJECT_USER_LEAVE) &&
            $employee->id == $leave->user->id &&
            !$employee->hasAnyRole([Role::ROLE_MANAGER, Role::ROLE_ADMIN]) &&
            $leave->user->department_id == $manager->department_id;
    }

    /**
     * Determine whether the user can approve the leave.
     *
     * @param User $manager
     * @param Leave $leave
     * @param User $employee
     * @return boolean
     */
    public function approve(User $manager, Leave $leave, User $employee)
    {
        return $manager->hasPermissionTo(Permission::PERMISSION_APPROVE_USER_LEAVE) &&
            $employee->id == $leave->user->id &&
            !$employee->hasAnyRole([Role::ROLE_MANAGER, Role::ROLE_ADMIN]) &&
            $leave->user->department_id == $manager->department_id;
    }
}
