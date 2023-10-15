<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(Request $request) {
        $request->validate([
            'username' => ['required', 'unique:Users', 'min:4', 'max:60'],
            'password' => ['required', 'min:8', 'max:65536']
        ]);

        $user = new User();
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->save();

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token
        ]);
    }

    public function signin(Request $request) {
        $request->validate([
            'username' => ['required', 'min:4', 'max:60'],
            'password' => ['required', 'min:8', 'max:65536']
        ]);

        $user = User::where('username', $request->username)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Wrong username or password'
            ], 401);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token
        ], 200);
    }

    public function signout() {
        auth('sanctum')->user()->tokens()->delete();

        return response()->json([
            'status' => 'success'
        ], 200);
    }
}
