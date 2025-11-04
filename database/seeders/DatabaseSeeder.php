<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash; 

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $editorRole = Role::firstOrCreate(['name' => 'editor']);

        // Create permissions
        $editArticles = Permission::firstOrCreate(['name' => 'edit articles']);
        $deleteArticles = Permission::firstOrCreate(['name' => 'delete articles']);

        // Create a admin user
        $user = User::factory()->create([
            'name'     => 'Admin',
            'user_id'  => 'admin001',   // optional, admin ne pan user_id aapi shako
            'email'    => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 👤 Normal users (user_id thi login)
        User::create([
            'name'     => 'Ramesh',
            'user_id'  => '8486168659',
            'email'    => null,
            'password' => Hash::make('secret123'),
        ]);

        User::create([
            'name'     => 'Suresh',
            'user_id'  => '8486168660',
            'email'    => null,
            'password' => Hash::make('secret123'),
        ]);

        // Assign role and permission
        $user->assignRole($adminRole);
        $user->givePermissionTo($editArticles);
    }
}
