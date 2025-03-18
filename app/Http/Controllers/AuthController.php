<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password;



class AuthController extends Controller
{



    public function sendResetPasswordEmail()
    {
        request()->validate(['email' => 'required|email', 'user_type' => ['required', new Enum(UserTypes::class)]]);
        $userType = request()->only('user_type')['user_type'] . 's';

        $response = Password::broker($userType)->sendResetLink(
            request()->only('email')
        );

        return $response == Password::ResetLinkSent
            ? response()->json(['message' => 'Password reset link sent!'], 200)
            : response()->json(['message' => 'Email could not be sent to this email address'], 400);
    }

    public function resetPassword()
    {
        request()->validate([
            'token' => 'required',
            'email' => 'required|email',
            'user_type' => ['required', new Enum(UserTypes::class)],
            'password' => 'required|confirmed',
        ]);
        Log::info('userType');
        $userType = request()->only('user_type')['user_type'] . 's';

        $response = Password::broker($userType)->reset(
            request()->only('token', 'email', 'password', 'password_confirmation'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $response == Password::PasswordReset
            ? response()->json(['message' => 'Password reset successful!'], 200)
            : response()->json(['message' => 'Invalid password reset token or email'], 400);
    }



    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        request()->validate([
            'email' => 'required',
            'password' => 'required',
            'user_type' => ['required', new Enum(UserTypes::class)]
        ]);
        $credentials = request()->only(['email', 'password']);
        $userType = request()->only('user_type')["user_type"];

        Log::info($userType);

        if (!$token = auth($userType)->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'message' => 'user successfully authenticated',
            'token' => $token,
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
