<?php
require_once "./core/Auth.php";
require_once   "./core/Request.php";
require_once "./core/Response.php";
require_once "./config/env.php";

$_ENV['JWT_SECRET'] = 'your_jwt_secret_key_here';   
try{
    //sign a token with usesr data
    $token=Auth::sign(['id' => 1, 'role' => 'admin']);
    echo "Generated JWT Token: " . $token . "\n";
} catch (Exception $e) {
    echo "Error generating token: " . $e->getMessage() . "\n";
}

    //Decode the token to verify it works
    $payload = Auth::decode($token);
    if ($payload) {
        echo "Decoded Payload: " . print_r($payload, true) . "\n";
    } else {
        echo "Failed to decode token.\n";
    }



?>