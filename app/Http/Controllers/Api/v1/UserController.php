<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

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
            'address'     => 'required|string|min:3',
            'email' => 'required|email|unique:users,email',
            'phone'     => 'required|string|min:10',
            'shirt_size'     => 'required|string|min:1',
            'tshirt_size'     => 'required|string|min:1',
            'trouser_size'     => 'required|string|min:1',
            'jeans_size'     => 'required|string|min:1',
            'dob'     => 'required|string|min:2',
            'doj'     => 'required|string|min:2',
            'education'     => 'required|string|min:2',
            'total_exp'     => 'required|string|min:2',
            'summary_exp'     => 'required|string|min:1',
            'emergency_contact'     => 'required|string|min:2',
            'product'     => 'required|string|min:2',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,id',
        ]);
        // dd($request);

        $user = User::create([
            'name'     => $validated['name'],
            'user_id'  => $validated['user_id'],
            'email'    =>  $validated['email'],
            'phone'    => $validated['phone'],
            'shirt_size'    => $validated['shirt_size'],
            'tshirt_size'    => $validated['tshirt_size'],
            'trouser_size'    => $validated['trouser_size'],
            'jeans_size'    => $validated['jeans_size'],
            'dob'    => $validated['dob'],
            'doj'    => $validated['doj'],
            'education'    => $validated['education'],
            'total_exp'    => $validated['total_exp'],
            'summary_exp'    => $validated['summary_exp'],
            'emergency_contact'    => $validated['emergency_contact'],
            'product'    => $validated['product'],
            'password' => Hash::make($validated['password']),
        ]);
        
        // ✅ fetch role by id and assign by name
        $role = Role::find($validated['role']);
        $user->assignRole($role->name);

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
                'name'              => 'required|string|min:3',
                'address'           => 'nullable|string|max:255',
                'phone'             => 'nullable|string|max:20',
                'email' => [ 'required', 'email', Rule::unique('users', 'email')->ignore($user->id), ],
                'dob'               => 'nullable|date',
                'doj'               => 'nullable|date',
                'education'         => 'nullable|string|max:255',
                'total_exp'         => 'nullable|string|max:50',
                'summary_exp'       => 'nullable|string|max:255',
                'emergency_contact' => 'nullable|string|max:50',
                'product'           => 'nullable|string|max:255',
                'role'              => 'required|exists:roles,id',
            ]);

            // update allowed fields
            $user->update([
                'name'              => $validated['name'],
                'address'           => $validated['address'] ?? $user->address,
                'phone'             => $validated['phone'] ?? $user->phone,
                'email'             => $validated['email'] ?? $user->email,
                'dob'               => $validated['dob'] ?? $user->dob,
                'doj'               => $validated['doj'] ?? $user->doj,
                'shirt_size'        => $validated['shirt_size'] ?? $user->shirt_size,
                'tshirt_size'       => $validated['tshirt_size'] ?? $user->tshirt_size,
                'trouser_size'      => $validated['trouser_size'] ?? $user->trouser_size,
                'jeans_size'        => $validated['jeans_size'] ?? $user->jeans_size,
                'education'         => $validated['education'] ?? $user->education,
                'total_exp'         => $validated['total_exp'] ?? $user->total_exp,
                'summary_exp'       => $validated['summary_exp'] ?? $user->summary_exp,
                'emergency_contact' => $validated['emergency_contact'] ?? $user->emergency_contact,
                'product'           => $validated['product'] ?? $user->product,
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
