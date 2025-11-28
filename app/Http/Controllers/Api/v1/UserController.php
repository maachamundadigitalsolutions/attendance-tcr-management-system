<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // List all users with roles
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return response()->json($users);
    }

    // Create new user (user_id + password required)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'  => 'required|digits_between:5,15|unique:users,user_id',
            'name'     => 'required|string|min:3',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'user_id'  => $validated['user_id'],
            'email'    => null,
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return response()->json($user->load('roles'), 201);
    }

    // Show single user
    public function show($id)
    {
        return response()->json(User::with('roles')->findOrFail($id));
    }

    // Update user (❌ user_id and password cannot be changed here)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

            // prevent role change for main admin
        if ($user->user_id === 'admin001') {
            return response()->json(['message' => 'Main admin role cannot be changed'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|min:3',
            'role' => 'required|exists:roles,id',
        ]);

        $user->update([
            'name' => $validated['name'],
        ]);

        // fetch role by ID
        $role = Role::find($validated['role']);
        if ($role) {
            $user->syncRoles([$role->name]); // pass role name
        }

        return response()->json($user->load('roles'));
    }

    // Delete user
   public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->user_id === 'admin001') {
            return response()->json(['message' => 'Main admin cannot be deleted'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }


    // Engineers list
    public function engineers()
    {
        $engineers = User::role('engineer')->get(['id','name','user_id']);
        return response()->json($engineers);
    }

    // ✅ Reset password (only admin can do this)
    public function resetPassword(Request $request, $id)
    {
        if (!auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['message' => 'Password reset successfully']);
    }
}
