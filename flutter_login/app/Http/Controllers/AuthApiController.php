<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;


class AuthApiController extends Controller
{
    private function generateToken(User $user)
    {
        return $user->createToken('MyApp')->plainTextToken;
    }

    public function register(Request $request)
    {
        // Validations
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);

        // Create a new user
        $user = User::create($data);

        // Generate token using the private method
        $token = $this->generateToken($user);

        $response = [
            'token' => $token,
            'name' => $user->name,
        ];

        return response()->json(['user' => $user, 'token' => $token, 'message' => 'Registration successful'], 200);
    }

    public function login(Request $request)
    {
        
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Generate token using the private method
            $token = $this->generateToken($user);

            $response = [
                'token' => $token,
                'name' => $user->name,
            ];

            return response()->json(['user' => $user, 'token' => $token, 'message' => 'Login successful'], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

   

    // public function logout(Request $request)
    // {
    //     $user = $request->user();
    //     $user-> tokens()->delete();
    //     return response()->json(['message' => 'Logout successful'], 200);
    // }

    public function forgotPassword(Request $request)
    {
        // Validate input
        $request->validate(['email' => 'required|email']);

        // Send password reset link
        $response = Password::sendResetLink($request->only('email'));

        // Check the response and send appropriate message
        return $response == Password::RESET_LINK_SENT
                    ? response()->json(['message' => 'Reset link sent to your email'], 200)
                    : response()->json(['error' => 'Unable to send reset link'], 400);
    }
    

}