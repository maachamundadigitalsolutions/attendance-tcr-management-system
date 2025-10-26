<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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

        // Create a test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Assign role and permission
        $user->assignRole($adminRole);
        $user->givePermissionTo($editArticles);
    }
}
