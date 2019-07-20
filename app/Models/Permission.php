<?php

namespace App\Models;

use \Spatie\Permission\Models\Permission as BasePermission;

/**
 * Class Permission
 * @package App\Models
 */
class Permission extends BasePermission
{
    //Global permissions.
    const PERMISSION_INDEX_OWN_LEAVE = 'index-own-leaves';
    const PERMISSION_VIEW_OWN_LEAVE = 'view-own-leave';
    const PERMISSION_CREATE_LEAVE = 'create-leave';
    const PERMISSION_CANCEL_OWN_LEAVE = 'cancel-own-leave';
    //Manager permissions.
    const PERMISSION_INDEX_USER_LEAVE = 'index-user-leave';
    const PERMISSION_VIEW_USER_LEAVE = 'view-user-leave';
    const PERMISSION_APPROVE_USER_LEAVE = 'approve-user-leave';
    const PERMISSION_REJECT_USER_LEAVE = 'reject-user-leave';
}
