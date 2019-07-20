<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => Role::ROLE_ADMIN]);
        Role::create(['name' => Role::ROLE_MANAGER]);
        Role::create(['name' => Role::ROLE_EMPLOYEE]);
    }
}
