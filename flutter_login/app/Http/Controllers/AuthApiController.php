<?php

namespace App\Http\Controllers;

//use Dotenv\Validator;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Str;

class AuthApiController extends Controller

{
    //validations
    public function register(Request $request)
    {
        $vaidator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]
        );

        if ($vaidator->fails()) {
            return response()->json(['error' => $vaidator->errors()], 400);
        }
        //create a new user

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);


        return response()->json(['user' => $user, 'message' => 'Registration successfull'], 200);
    }

    public function login(Request $request)
    {
        //validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        //Attempt login
        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            //Authentication successfull
            $user = Auth::user();
            //$token =$user->createToken('authToken')->accessToken;
            $token = bcrypt(Str::random(60));

            // Save the token in the user record (optional)
            $user->api_token = $token;
            //$user->save();
            return response()->json(['user' => $user, 'access_token' => $token], 200);
        } else {
            // Authentication failed
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }
}
