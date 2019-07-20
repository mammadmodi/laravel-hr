<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employeeRole = Role::findByName(Role::ROLE_EMPLOYEE);
        $employeePermissionNames = [
            Permission::PERMISSION_VIEW_OWN_LEAVE,
            Permission::PERMISSION_INDEX_OWN_LEAVE,
            Permission::PERMISSION_CREATE_LEAVE,
            Permission::PERMISSION_UPDATE_OWN_LEAVE,
            Permission::PERMISSION_CANCEL_OWN_LEAVE,
        ];
        foreach ($employeePermissionNames as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }
        $employeeRole->syncPermissions($employeePermissionNames);

        $managerSpecificPermissionNames = [
            Permission::PERMISSION_INDEX_USER_LEAVE,
            Permission::PERMISSION_VIEW_USER_LEAVE,
            Permission::PERMISSION_UPDATE_USER_LEAVE,
            Permission::PERMISSION_APPROVE_USER_LEAVE,
            Permission::PERMISSION_REJECT_USER_LEAVE,
        ];
        foreach ($managerSpecificPermissionNames as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }
        $managerPermissionNames = array_merge($employeePermissionNames, $managerSpecificPermissionNames);
        $managerRole = Role::findByName(Role::ROLE_MANAGER);
        $managerRole->syncPermissions($managerPermissionNames);
    }
}
