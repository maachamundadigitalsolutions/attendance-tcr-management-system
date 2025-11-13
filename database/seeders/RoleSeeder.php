<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $viewUsers = Permission::firstOrCreate(['name' => 'view users']);
        $editUsers = Permission::firstOrCreate(['name' => 'edit users']);

        // Create role
        $admin = Role::firstOrCreate(['name' => 'Admin']);

        // Assign permissions to role
        $admin->givePermissionTo([$viewUsers, $editUsers]);
    }
}
