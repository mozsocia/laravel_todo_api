<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken
{
    public static function createToken($payload)
    {
        $key = env('JWT_SECRET');

        $payload = array_merge($payload, [
            'iss' => 'iss for jwt token',
            'iat' => time(),
            'exp' => time() + 60 * 60,
        ]);

        $token = JWT::encode($payload, $key, 'HS256');
        return $token;
    }

    public static function verifyToken($token)
    {
        try {
            $key = env('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {

            return "Unauthorize";
        }
    }
}
