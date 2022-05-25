<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;

require_once("claves.php");

function tokenChech($token)
{
    global $secret_key, $encrypt;

    return JWT::decode(
        $token,
        $secret_key,
        $encrypt
    );
}

function encodeToken($payload)
{
    global $secret_key, $encrypt;

    $time = time();

    $token = array(
        'exp' => $time + (60 * 60),
        'data' => $payload
    );

    return JWT::encode($token, $secret_key);
}