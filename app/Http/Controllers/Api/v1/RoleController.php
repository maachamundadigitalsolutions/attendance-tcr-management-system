<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // List all roles (except main admin)
    public function index()
    {
        $roles = Role::with('permissions')
            ->where('name', '!=', 'admin')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->map(function ($perm) {
                        return [
                            'name'  => $perm->name,
                            'label' => $perm->label ?? ucfirst(str_replace('-', ' ', $perm->name)),
                        ];
                    })->toArray(),
                ];
            });

            // 👇 add all permissions list
        $allPermissions = Permission::all()->map(function ($perm) {
            return [
                'name'  => $perm->name,
                'label' => $perm->label ?? ucfirst(str_replace('-', ' ', $perm->name)),
            ];
        });

        return response()->json([
            'roles' => $roles,
            'all_permissions' => $allPermissions,
        ]);
    }

    // Show single role with permissions
    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return response()->json([
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->map(function ($perm) {
                    return [
                        'name'  => $perm->name,
                        'label' => $perm->label ?? ucfirst(str_replace('-', ' ', $perm->name)),
                    ];
                })->toArray(),
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

        if (strtolower($request->name) === 'admin') {
            return response()->json([
                'message' => 'The "admin" role cannot be created'
            ], 403);
        }

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
                'permissions' => $role->permissions->map(function ($perm) {
                    return [
                        'name'  => $perm->name,
                        'label' => $perm->label ?? ucfirst(str_replace('-', ' ', $perm->name)),
                    ];
                })->toArray(),
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
                'permissions' => $role->permissions->map(function ($perm) {
                    return [
                        'name'  => $perm->name,
                        'label' => $perm->label ?? ucfirst(str_replace('-', ' ', $perm->name)),
                    ];
                })->toArray(),
            ]
        ]);
    }

    // Delete role
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'admin') {
            return response()->json(['message' => 'Main admin role cannot be deleted'], 403);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }
}
