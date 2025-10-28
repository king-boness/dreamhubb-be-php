<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class RefreshTokenMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            // Pokús sa autentifikovať používateľa
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            try {
                // 🟡 Token je expirovaný – pokús sa o refresh
                $newToken = JWTAuth::refresh(JWTAuth::getToken());

                // Nastav nový token do requestu, aby Auth guard mohol fungovať
                $request->headers->set('Authorization', 'Bearer ' . $newToken);

                // Načítaj používateľa z nového tokenu
                $user = JWTAuth::setToken($newToken)->toUser();
                Auth::setUser($user);

                // Spracuj požiadavku ďalej
                $response = $next($request);

                // Pridaj nový token do hlavičky odpovede
                $response->headers->set('Authorization', 'Bearer ' . $newToken);

                return $response;
            } catch (JWTException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token expired, please log in again.'
                ], 401);
            }
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token.'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not provided.'
            ], 401);
        }

        // ✅ Token je platný → pokračujeme ďalej
        return $next($request);
    }
}
