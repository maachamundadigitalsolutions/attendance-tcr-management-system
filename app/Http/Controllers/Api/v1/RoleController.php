<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ];
        });

        return response()->json([
            'roles' => $roles
        ]);
    }
}

