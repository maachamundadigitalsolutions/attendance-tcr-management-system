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
        // Roles
        $adminRole    = Role::firstOrCreate(['name' => 'admin']);
        $admin2Role   = Role::firstOrCreate(['name' => 'admin2']);
        $admin3Role   = Role::firstOrCreate(['name' => 'admin3']);
        $engineerRole = Role::firstOrCreate(['name' => 'engineer']);

        // Attendance Permissions
        $markAttendance   = Permission::firstOrCreate(['name' => 'attendance-mark']);
        $viewAttendances  = Permission::firstOrCreate(['name' => 'attendance-view-all']);
        $deleteAttendance = Permission::firstOrCreate(['name' => 'attendance-delete']);
        

        // ✅ TCR Permissions
        $tcrAssign   = Permission::firstOrCreate(['name' => 'tcr-assign']);
        $tcrViewAll  = Permission::firstOrCreate(['name' => 'tcr-view-all']);
        $tcrUse      = Permission::firstOrCreate(['name' => 'tcr-use']);
        $tcrVerify   = Permission::firstOrCreate(['name' => 'tcr-verify']);
        $tcrDelete   = Permission::firstOrCreate(['name' => 'tcr-delete']);
        $tcrVerifyCase   = Permission::firstOrCreate(['name' => 'tcr-verify-case']);
        $tcrVerifyOnline = Permission::firstOrCreate(['name' => 'tcr-verify-online']);


        // Assign permissions
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
            $markAttendance,
            $tcrUse
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

        // Assign roles
        $adminUser->assignRole($adminRole);
        $adminUser2->assignRole($admin2Role);
        $adminUser3->assignRole($admin3Role);
        $ramesh->assignRole($engineerRole);
        $suresh->assignRole($engineerRole);

        // ✅ Dummy TCR range assign to Ramesh (13101 → 13199)
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
