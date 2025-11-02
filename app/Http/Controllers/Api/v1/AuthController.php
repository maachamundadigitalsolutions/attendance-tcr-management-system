<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// namespace App\Http\Controllers\Api\V1;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\User;
// use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate input: one identifier + password
        $request->validate([
            'login'    => 'required', // can be email or user_id/phone
            'password' => 'required',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        // Detect type: email vs numeric ID
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            // Email login (admin or anyone with email)
            if (!Auth::attempt(['email' => $login, 'password' => $password])) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            $user = Auth::user();
        } else {
            // Numeric ID login (e.g., user_id or phone)
            // Choose the correct column name; examples: 'user_id' or 'phone'
            $user = User::where('user_id', $login)->first(); // or ->where('phone', $login)

            if (!$user || !Hash::check($password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            // Log the user into the current session context (optional for API)
            Auth::login($user);
        }

        // Issue Sanctum token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'        => $user,
            'roles'       => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'token'       => $token,
        ]);
    }
    
    public function register(Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }


    public function me(Request $request) {
        return response()->json([
            'user' => $request->user(),
            'roles' => $request->user()->getRoleNames(),
            'permissions' => $request->user()->getAllPermissions()->pluck('name'),
        ]);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
