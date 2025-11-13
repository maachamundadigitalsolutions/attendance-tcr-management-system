<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash; 

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default roles
        $adminRole    = Role::firstOrCreate(['name' => 'admin']);
        $admin2Role   = Role::firstOrCreate(['name' => 'admin2']);
        $admin3Role   = Role::firstOrCreate(['name' => 'admin3']);
        $engineerRole = Role::firstOrCreate(['name' => 'engineer']);

        // Create permissions
        $viewAttendances   = Permission::firstOrCreate(['name' => 'view attendances']);
        $editAttendances   = Permission::firstOrCreate(['name' => 'edit attendances']);
        $deleteAttendances = Permission::firstOrCreate(['name' => 'delete attendances']);

        // Assign permissions to roles
        $adminRole->syncPermissions([$viewAttendances, $editAttendances, $deleteAttendances]);
        $admin2Role->syncPermissions([$viewAttendances, $editAttendances]);
        $admin3Role->syncPermissions([$viewAttendances, $editAttendances]);
        $engineerRole->syncPermissions([$viewAttendances]);

        // Create or update admin user
        $adminUser = User::updateOrCreate(
            ['user_id' => 'admin001'], // unique field condition
            [
                'name'     => 'Admin',
                'email'    => 'admin@example.com',
                'password' => Hash::make('password123'),
            ]
        );

        // Normal users
        $ramesh = User::updateOrCreate(
            ['user_id' => '8486168659'],
            [
                'name'     => 'Ramesh',
                'email'    => null,
                'password' => Hash::make('secret123'),
            ]
        );

        $suresh = User::updateOrCreate(
            ['user_id' => '8486168660'],
            [
                'name'     => 'Suresh',
                'email'    => null,
                'password' => Hash::make('secret123'),
            ]
        );

        // Assign roles
        $adminUser->assignRole($adminRole);
        $ramesh->assignRole($engineerRole);
        $suresh->assignRole($engineerRole);
    }
}
