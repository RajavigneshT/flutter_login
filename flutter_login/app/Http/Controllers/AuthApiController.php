<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Laravel\Sanctum;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\ForgetPasswordRequest;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Sanctum as SanctumSanctum;

class AuthApiController extends Controller
{
    private function generateToken(User $user)
    {
        return $user->createToken('MyApp')->plainTextToken;
    }

    public function register(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validations
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $request->all();
            $data['password'] = Hash::make($data['password']);

            // Create a new user
            $user = User::create($data);

            // Generate token using the private method
            $token = $this->generateToken($user);

            DB::commit();

            return response()->json(['user' => $user, 'token' => $token, 'message' => 'Registration successful'], 200);
        } catch (ValidationException $e) {
            // Handle validation errors and log the specific fields that caused the failure
            $errors = $e->errors();

            return response()->json(['message' => 'Validation failed', 'errors' => $errors], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json(['error' => 'Email must be unique'], 422);
            } else {
                Log::error("SQL Exception: " . $e->getMessage());
                return response()->json(['error' => 'Database error'], 500);
            }
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();

            // Handle other exceptions
            Log::error("Unexpected Exception: " . $e->getMessage());

            return response()->json(['error' => 'Error occurred. Transaction rolled back.'], 500);
        }
    }

    public  function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $this->generateToken($user);
                $response = ['token' => $token, 'name' => $user->name,];
                return response()->json(['token' => $token, 'message' => 'Logged in Successfully'], 200);
            } else {
                $userWithEmail = User::where('email', $credentials['email'])->first();
                $userWithName = User::where('name', $credentials['email'])->first();

                if (!$userWithEmail && !$userWithName) {
                    return response()->json(['email' => 'Email or name does not match with our records'], 422);
                }
                throw new \Exception('Unauthorized');
            }
        } catch (ValidationException $e) {
            //Handle validation errors
            $errors = $e->errors();
            return response()->json(['message' => 'Validation failed', 'error' => $errors], 422);
        } catch (AuthenticationException $e) {
            // Handle authentication errors
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (Exception $e) {
            Log::error("Unexpected Exception: " . $e->getMessage());
            return response()->json(['error' => 'Unexpected error'], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Revoke the user's token using Laravel Sanctum
            //Sanctum::revokeUserTokens($user);

            // Log the user out
            Auth::logout();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function forgot(ForgetPasswordRequest $request): JsonResponse
    {
        $user = ($query = User::query());
        $email = $request->input('email');
        $user = $user->where($query->qualifyColumn('email'), $email)->first();
        Log::info($user);

        if ($user == null) {
            return response()->json(['message' => 'Incorrect Email Address provided'], 484);
        }
        //random pin generate to mail
        $resetpasswordtoken = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        if (!$userPassReset = PasswordReset::where('email', $email)->first()) {
            PasswordReset::create([
                'email' => $email,
                'token' => $resetpasswordtoken,
            ]);
        } else {
            //store token in DB eg.1 hr
            $userPassReset->update([
                'email' => $email,
                'token' => $resetpasswordtoken,
            ]);
        }
        //$user->notify(new PasswordReset($user, $resetpasswordtoken));


        return new JsonResponse(['message' => 'A code has been sent to your email address']);
    }


    public function reset(PasswordReset $request)
    {

        $attributes = $request->validated();
        $user = User::where('email', $attributes['email'])->first();
        Password::sendResetLink(['email' => $attributes['email']]);


        if (!$user) {
            return response()->json(['No record found'], 400);
        }
        $resetrequest = PasswordReset::where('email', $user->email)->first();

        if (!$resetrequest || $resetrequest->token != $request->token) {
            return response()->json(['An Error occured , Token mismatch'], 400);
        }
        //update user password
        $user->fill([
            'password' => Hash::make($attributes['password']),
        ]);
        $user->save();

        //delete token
        $user->tokens()->delete();

        //get token for Auth
        $token = $user->createToken('authToken')->plainTextToken;

        //create response
        $loginresponse = [
            'user' => User::make($user),
            'token' => $token
        ];
        return response()->json([$loginresponse, 'Password Reset Success'], 200);
    }


    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer'
            ]
        ]);
    }
}
