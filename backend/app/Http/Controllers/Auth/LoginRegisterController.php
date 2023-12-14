<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Validator;

class LoginRegisterController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'username' => 'required|string|max:250|unique:users,username', // Ubah validasi untuk username
            'password' => 'required|string|min:8|confirmed' // Menghapus validasi untuk email
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ]);

        $data['token'] = $user->createToken($request->username)->plainTextToken; // Gunakan username untuk membuat token
        $data['user'] = $user;

        $response = [
            'status' => 201,
            'message' => 'User is created successfully.',
            'data' => $data,
        ];

        return response()->json($response, 201);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => 'required|string', // Ubah validasi untuk username
            'password' => 'required|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 403,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        // Check username exist
        $user = User::where('username', $request->username)->first(); // Ubah pencarian berdasarkan username

        // Check password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid username or password'
            ], 401);
        }

        $data['token'] = $user->createToken($request->username)->plainTextToken; // Gunakan username untuk membuat token
        $data['user'] = $user;

        $response = [
            'status' => 200,
            'message' => 'User is logged in successfully.',
            'data' => $data,
        ];

        return response()->json($response, 200);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'User is logged out successfully'
            ], 200);
    }
}
