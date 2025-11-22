<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tcr;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash; 

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles (api guard)
        $adminRole    = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $admin2Role   = Role::firstOrCreate(['name' => 'admin2', 'guard_name' => 'api']);
        $admin3Role   = Role::firstOrCreate(['name' => 'admin3', 'guard_name' => 'api']);
        $engineerRole = Role::firstOrCreate(['name' => 'engineer', 'guard_name' => 'api']);

        // Attendance Permissions (api guard)
        $markAttendance   = Permission::firstOrCreate(['name' => 'attendance-mark', 'guard_name' => 'api']);
        $viewAttendances  = Permission::firstOrCreate(['name' => 'attendance-view-all', 'guard_name' => 'api']);
        $deleteAttendance = Permission::firstOrCreate(['name' => 'attendance-delete', 'guard_name' => 'api']);
        
        // TCR Permissions (api guard)
        $tcrAssign        = Permission::firstOrCreate(['name' => 'tcr-assign', 'guard_name' => 'api']);
        $tcrViewAll       = Permission::firstOrCreate(['name' => 'tcr-view-all', 'guard_name' => 'api']);
        $tcrUse           = Permission::firstOrCreate(['name' => 'tcr-use', 'guard_name' => 'api']);
        $tcrVerify        = Permission::firstOrCreate(['name' => 'tcr-verify', 'guard_name' => 'api']);
        $tcrDelete        = Permission::firstOrCreate(['name' => 'tcr-delete', 'guard_name' => 'api']);
        $tcrVerifyCase    = Permission::firstOrCreate(['name' => 'tcr-verify-case', 'guard_name' => 'api']);
        $tcrVerifyOnline  = Permission::firstOrCreate(['name' => 'tcr-verify-online', 'guard_name' => 'api']);

        // Assign permissions to roles
        $adminRole->syncPermissions([
            $markAttendance, $viewAttendances, $deleteAttendance,
            $tcrAssign, $tcrViewAll, $tcrVerify, $tcrDelete
        ]);

        $admin2Role->syncPermissions([
            $viewAttendances, $tcrViewAll, $tcrVerifyCase
        ]);

        $admin3Role->syncPermissions([
            $viewAttendances, $tcrViewAll, $tcrVerifyOnline
        ]);

        $engineerRole->syncPermissions([
            $markAttendance, $tcrUse
        ]);

        // Users
        $adminUser = User::updateOrCreate(
            ['user_id' => 'admin001'],
            [
                'name'     => 'Admin',
                'email'    => 'admin@example.com',
                'password' => Hash::make('password123'),
            ]
        );
        $adminUser2 = User::updateOrCreate(
            ['user_id' => 'admin002'],
            [
                'name'     => 'Admin 2',
                'email'    => 'admin2@example.com',
                'password' => Hash::make('password123'),
            ]
        );
        $adminUser3 = User::updateOrCreate(
            ['user_id' => 'admin003'],
            [
                'name'     => 'Admin 3',
                'email'    => 'admin3@example.com',
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

        // Assign roles to users
        $adminUser->assignRole($adminRole);
        $adminUser2->assignRole($admin2Role);
        $adminUser3->assignRole($admin3Role);
        $ramesh->assignRole($engineerRole);
        $suresh->assignRole($engineerRole);

        // Dummy TCR range assign to Ramesh (13101 → 13199)
        $records = [];
        for ($tcrNo = 13101; $tcrNo <= 13199; $tcrNo++) {
            if (!Tcr::where('tcr_no', $tcrNo)->exists()) {
                $records[] = [
                    'tcr_no' => $tcrNo,
                    'user_id' => $ramesh->id,
                    'status' => 'assigned',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if (!empty($records)) {
            Tcr::insert($records);
        }
    }
}
