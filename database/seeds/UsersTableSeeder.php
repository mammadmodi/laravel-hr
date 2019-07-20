<?php

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var Department $department */
        $department = Department::query()->first();

        //Create admin user
        /** @var User $adminUser */
        $adminUser = factory(User::class)->create([
            'name' => 'admin',
            'email' => 'admin@depart.com'
        ]);
        $adminUser->assignRole(Role::ROLE_ADMIN);

        //Create manager user
        /** @var User $managerUser */
        $managerUser = factory(User::class)->create([
            'name' => 'manager',
            'email' => 'manager@depart.com',
            'department_id' => $department->id
        ]);
        $managerUser->assignRole(Role::ROLE_MANAGER);

        //Create employee user
        /** @var User $employeeUser */
        $employeeUser = factory(User::class)->create([
            'name' => 'employee',
            'email' => 'employee@depart.com',
            'department_id' => $department->id
        ]);
        $employeeUser->assignRole(Role::ROLE_EMPLOYEE);
    }
}
