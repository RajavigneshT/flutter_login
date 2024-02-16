<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Laravel\Sanctum;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Mail\ResetPasswordEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\ForgetPasswordRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Sanctum as SanctumSanctum;
use App\Notifications\PasswordResetNotification;
use Monolog\ResettableInterface;
use Laravel\Sanctum\PersonalAccessToken;

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

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user) {
                $token = $user->currentAccessToken();
                Log::info("Token ID to be deleted: " . optional($token)->id);
                if ($token) {
                    $token->delete();
                }
            }
    
            Auth::guard('web')->logout();
    
            return response()->json(['message' => 'User logged out successfully'], 200);
        } catch (Exception $e) {
            Log::error("Unexpected Exception: " . $e->getMessage());
            return response()->json(['error' => 'Unexpected error'], 500);
        }
    }
   
   
    public function forgot(ForgetPasswordRequest $request): JsonResponse
{
    $email = $request->input('email');

    // Retrieve user directly
    $user = User::where('email', $email)->first();

    Log::info($user);

    if ($user === null) {
        return response()->json(['message' => 'Incorrect Email Address provided'], 404);
    }

    // Generate a random 4-digit OTP
    $resetPasswordOTP = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

    // Check if a PasswordReset entry already exists
    $userPassReset = PasswordReset::where('email', $email)->first();

    if (!$userPassReset) {
        PasswordReset::create([
            'email' => $email,
            'token' => $resetPasswordOTP,
        ]);
    } else {
        // Update the existing entry with a new OTP
        $userPassReset->update([
            'token' => $resetPasswordOTP,
        ]);
    }

    // Notify the user with the password reset OTP
    $user->notify(new PasswordResetNotification($user,$resetPasswordOTP));

    Log::info('Password reset initiated for email: ' . $email);

    return new JsonResponse(['message' => 'A 4-digit code has been sent to your email address']);
}



public function resetpassword(Request $request): JsonResponse
{
    $request->validate([
        'email' => 'required|email',
        'token' => 'required|numeric|digits:4',
        'password' => 'required', // Adjust the minimum password length as needed
    ]);

    $email = $request->input('email');
    $token = $request->input('token');
    $password = $request->input('password');

    // Check if the OTP is valid
    $userPassReset = PasswordReset::where('email', $email)->where('token', $token)->first();

    if (!$userPassReset) {
        return response()->json(['message' => 'Invalid OTP'], 400);
    }

    // Update the user's password
    $user = User::where('email', $email)->first();
    $user->update(['password' => Hash::make($password)]);

    // Delete the used OTP record
    $userPassReset->delete();

    return response()->json(['message' => 'Password reset successfully']);
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
