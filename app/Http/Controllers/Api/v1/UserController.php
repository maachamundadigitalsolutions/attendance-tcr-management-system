<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::paginate(10));
    }

  public function store(Request $request)
{
        $validated = $request->validate([
            'user_id'  => 'required|digits_between:5,15|unique:users,user_id',
            'name'     => 'required|string|min:3',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'user_id'  => $validated['user_id'],
            'email'    => null,
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json($user, 201);

}


    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'user_id'  => 'required|digits_between:5,15|unique:users,user_id,' . $id,
            'name'     => 'required|string|min:3',
            'password' => 'nullable|min:6',
        ]);

        $user->update([
            'name'     => $validated['name'],
            'user_id'  => $validated['user_id'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);

        return response()->json($user);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
