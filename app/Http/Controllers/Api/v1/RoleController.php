<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
   public function index()
    {
        $roles = Role::with('permissions')
            ->where('name', '!=', 'admin') // exclude main admin role
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                ];
            });

        return response()->json(['roles' => $roles]);
    }


    // Show single role with permissions
    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return response()->json([
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ]
        ]);
    }

    // Create new role with selected permissions
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles',
            'permissions' => 'array'
        ]);

        // prevent creating role named "admin"
        if (strtolower($request->name) === 'admin') {
            return response()->json([
                'message' => 'The "admin" role cannot be created'
            ], 403);
        }

        // 👇 guard_name explicitly set to "api"
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'api',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ]
        ]);
    }

    // Update role permissions
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);
        // prevent creating role named "admin"
        if (strtolower($request->name) === 'admin') {
            return response()->json([
                'message' => 'The "admin" role cannot be created'
            ], 403);
        }

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ]
        ]);
    }

    // Delete role
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // prevent deleting main admin role
        if ($role->name === 'admin') {
            return response()->json(['message' => 'Main admin role cannot be deleted'], 403);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }

}
