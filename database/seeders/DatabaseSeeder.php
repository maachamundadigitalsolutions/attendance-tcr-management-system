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
    // Roles
    $adminRole    = Role::firstOrCreate(['name' => 'admin']);
    $admin2Role   = Role::firstOrCreate(['name' => 'admin2']);
    $admin3Role   = Role::firstOrCreate(['name' => 'admin3']);
    $engineerRole = Role::firstOrCreate(['name' => 'engineer']);

    // Permissions
    $markAttendance   = Permission::firstOrCreate(['name' => 'attendance-mark']);
    $viewAttendances  = Permission::firstOrCreate(['name' => 'attendance-view-all']);
    $deleteAttendance = Permission::firstOrCreate(['name' => 'attendance-delete']);

    // Assign permissions
    $adminRole->syncPermissions([$markAttendance, $viewAttendances, $deleteAttendance]);
    $admin2Role->syncPermissions([$viewAttendances]);
    $admin3Role->syncPermissions([$viewAttendances]);
    $engineerRole->syncPermissions([$markAttendance]);

    // Users
    $adminUser = User::updateOrCreate(
        ['user_id' => 'admin001'],
        [
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]
    );

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
