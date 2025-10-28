<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'refresh', 'verifyEmail', 'resendVerificationEmail']]);
    }

    /**
     * 🟢 Prihlásenie používateľa
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        $token = auth('api')->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = auth('api')->user();

        if (!$user->hasVerifiedEmail()) {
            auth('api')->logout();
            return response()->json([
                'status' => 'error',
                'message' => 'Please verify your email before logging in.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * 🟢 Registrácia nového používateľa
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'date_birth' => 'required',
                'gender' => 'required',
                'location_country_id' => 'required',
                'location_continent_id' => 'required',
                'location_city_id' => 'required',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 406);
        }

        $profile_image = null;

        $user = User::create([
            'profile_picture' => $profile_image,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'date_birth' => $request->date_birth,
            'gender' => $request->gender,
            'location_country_id' => $request->location_country_id,
            'location_continent_id' => $request->location_continent_id,
            'location_city_id' => $request->location_city_id,
        ]);

        // 🔔 Pošle verifikačný email
        event(new Registered($user));

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully. Please verify your email.',
            'user' => $user
        ], 201);
    }

    /**
     * 🟢 Odhlásenie
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * 🟢 Refresh JWT tokenu
     */
    public function refresh()
    {
        try {
            // ✅ Získaj nový token zo starého
            $newToken = auth('api')->refresh();

            return response()->json([
                'status' => 'success',
                'authorization' => [
                    'token' => $newToken,
                    'type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                ],
            ], 200);

        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token has expired and cannot be refreshed.',
            ], 401);
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token.',
            ], 401);
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not provided or refresh failed.',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unexpected error during refresh.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🟢 Opätovné poslanie verifikačného emailu
     */
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'info',
                'message' => 'Email is already verified.',
            ]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'status' => 'success',
            'message' => 'Verification email has been resent.',
        ]);
    }
}
