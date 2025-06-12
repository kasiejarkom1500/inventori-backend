<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generate_jwt($data)
{
    $key = getenv('JWT_SECRET') ?: 'your-secret-key';
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // 1 jam
    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'data' => $data
    ];

    return JWT::encode($payload, $key, 'HS256');
}

function validate_jwt($token)
{
    try {
        $key = getenv('JWT_SECRET') ?: 'your-secret-key';
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return (array) $decoded->data;
    } catch (Exception $e) {
        return false;
    }
}
