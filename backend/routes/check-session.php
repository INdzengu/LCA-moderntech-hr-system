<?php
// backend/routes/check-session.php

ini_set('session.cookie_httponly', 1);
session_start();

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://localhost:5173';
header("Access-Control-Allow-Origin: " . $origin);
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    http_response_code(200);
    echo json_encode([
        "authenticated" => true,
        "user" => [
            "id" => $_SESSION['user_id'],
            "email" => $_SESSION['user_email'],
            "role" => $_SESSION['user_role']
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        "authenticated" => false,
        "message" => "No active session found"
    ]);
}