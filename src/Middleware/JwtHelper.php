<?php
declare(strict_types=1);

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    private static string $algorithm = 'HS256';

    // php-jwt 7: HS256 key must be at least 32 characters
    private static function secretKey(): string
    {
        $secret = $_ENV['JWT_SECRET'] ?? '';
        if (strlen($secret) < 32) {
            throw new \RuntimeException('JWT_SECRET missing or shorter than 32 characters (see .env.example)');
        }
        return $secret;
    }

    public static function generateToken(array $data, int $expiry = 3600): string
    {
        $issuedAt = time();
        $payload = [
            'iat'  => $issuedAt,
            'exp'  => $issuedAt + $expiry,
            'data' => $data,
        ];
        return JWT::encode($payload, self::secretKey(), self::$algorithm);
    }

    public static function validateToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key(self::secretKey(), self::$algorithm));
        } catch (\Exception $e) {   // the \ is required inside a namespace
            return null;            // invalid or expired token
        }
    }
}