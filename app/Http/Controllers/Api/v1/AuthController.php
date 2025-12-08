<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
        public function login(Request $request)
    {
        $request->validate([
            'loginField' => 'required',
            'password'   => 'required',
        ]);

        $login    = $request->input('loginField');
        $password = $request->input('password');

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            // Email login via web guard
            if (!Auth::guard('web')->attempt(['email' => $login, 'password' => $password])) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            $user = Auth::guard('web')->user();
        } else {
            // Numeric ID login
            $user = User::where('user_id', $login)->first();

            if (!$user || !Hash::check($password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            // Optional: Auth::login($user);
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
    $user = $request->user();

    $today = now()->toDateString();

    // Find today's attendance for this user
    $attendance = \App\Models\Attendance::where('user_id', $user->id)
        ->where('date', $today)
        ->first();

        return response()->json([
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->map(function($perm) {
                return [
                    'name'  => $perm->name,   // backend identifier
                    'label' => $perm->label ?? ucfirst(str_replace('-', ' ', $perm->name))
                ];
            }),
            'attendance_today' => [
            'status'      => $attendance?->status ?? null,
            'punched_in'  => (bool) $attendance?->time_in,
            'punched_out' => (bool) $attendance?->time_out,
            ]
        ]);
    }


    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
