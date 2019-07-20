<?php

namespace App\Models;

use \Spatie\Permission\Models\Role as BaseRole;

/**
 * Class Role
 * @package App\Models
 */
class Role extends BaseRole
{
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_EMPLOYEE = 'employee';
}
