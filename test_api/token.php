<?php

require_once __DIR__ . "/../src/api/jwt.php";

$time = time();

$payload = array(
    'exp' => $time + (60 * 60),
    'email' => "leduardo.mp@gmail.com"
);

echo encodeToken($payload);
