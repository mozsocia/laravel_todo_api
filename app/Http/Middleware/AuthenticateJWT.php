<?php

namespace App\Http\Middleware;

use App\Helpers\JWTToken;
use Closure;
use Illuminate\Http\Request;

class AuthenticateJWT
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $decodedToken = JWTToken::verifyToken($token);

        if ($decodedToken === "Unauthorize") {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        // Attach the authenticated user data to the request for easy access in the controller
        $request->authUser = $decodedToken;

        return $next($request);
    }
}
