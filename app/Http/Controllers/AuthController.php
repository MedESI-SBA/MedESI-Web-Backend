<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;


class AuthController extends Controller
{
    public function sendResetPasswordEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'user_type' => ['required', new Enum(UserTypes::class)]
        ]);

        $userTypeBroker = $validated['user_type'] . 's';

        if (!array_key_exists($userTypeBroker, config('auth.passwords'))) {
            return response()->json(['message' => 'Invalid user type specified for password reset.'], 400);
        }

        $response = Password::broker($userTypeBroker)->sendResetLink(
            ['email' => $validated['email']],
            function ($user, $token) use ($validated) {
                $type = $validated['user_type'];
                $resetUrl = env("FRONT_END_URL") . "/reset-password?user_type={$type}&token={$token}";
                Log::info("Password reset link for {$user->email}: {$resetUrl}");
                try {
                    Mail::raw("Here is your password reset link: " . $resetUrl, function ($message) use ($user) {
                        $message->to($user->email)->subject('Reset your password');
                    });
                 } catch (\Exception $e) {
                     Log::error("Failed to send password reset email to {$user->email}: " . $e->getMessage());
                 }
            }
        );

        return $response == Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Password reset link sent!'], 200)
            : response()->json(['message' => 'Email could not be sent to this email address.'], 400);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'user_type' => ['required', new Enum(UserTypes::class)],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $userTypeBroker = $validated['user_type'] . 's';

        if (!array_key_exists($userTypeBroker, config('auth.passwords'))) {
            return response()->json(['message' => 'Invalid user type specified for password reset.'], 400);
        }

        $response = Password::broker($userTypeBroker)->reset(
            [
                'token' => $validated['token'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'password_confirmation' => $request->password_confirmation
            ],
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $response == Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successful!'], 200)
            : response()->json(['message' => 'Invalid password reset token or email.'], 400);
    }

    public function login(Request $request)
    {
       $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'user_type' => ['required', new Enum(UserTypes::class)]
        ]);

        $credentials = ['email' => $validated['email'], 'password' => $validated['password']];
        $userTypeGuard = $validated['user_type'];

        Log::info("Attempting login for user type: {$userTypeGuard}");

        if (!array_key_exists($userTypeGuard, config('auth.guards'))) {
             return response()->json(['message' => 'Invalid user type specified for login.'], 400);
        }

        if (!$token = auth($userTypeGuard)->attempt($credentials)) {
            Log::warning("Login failed for email: {$validated['email']} with user type: {$userTypeGuard}");
            return response()->json(['error' => 'Unauthorized: Invalid credentials or user type.'], 401);
        }

        Log::info("Login successful for email: {$validated['email']} with user type: {$userTypeGuard}");
        return $this->respondWithToken($token);
    }

    public function me()
    {
        $user = auth()->user();

        if (!$user) {
             return response()->json(['error' => 'User not authenticated.'], 401);
        }

        return response()->json($user);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
             return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $validated = $request->validate([
            'firstName' => 'sometimes|string|max:255',
            'familyName' => 'sometimes|string|max:255',
            'phoneNumber' => 'sometimes|string|max:20',
        ]);

        $updateData = array_filter($validated, function($value) { return $value !== null; });

        if(empty($updateData)) {
             return response()->json(['message' => 'No valid fields provided for update.'], 400);
        }

        foreach ($updateData as $key => $value) {
            $user->{$key} = $value;
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
             return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->different('current_password')],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['error' => 'Current password does not match.'], 422);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function logout()
    {
        try {
             auth()->logout();
             return response()->json(['message' => 'Successfully logged out']);
        } catch (\Exception $e) {
             Log::error('Logout failed: ' . $e->getMessage());
             return response()->json(['message' => 'Logout processed.'], 500);
        }
    }

    public function refresh()
    {
       try {
           $newToken = auth()->refresh();
           return $this->respondWithToken($newToken);
       } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid.'], 401);
       } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Could not refresh token.'], 500);
       }
    }

    protected function respondWithToken($token)
    {
        $ttl = auth()->factory()->getTTL() * 60;

        return response()->json([
            'message' => 'User successfully authenticated.',
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttl
        ]);
    }
}