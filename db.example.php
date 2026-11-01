<?php
$conn = new mysqli(
    "YOUR_DATABASE_HOST",
    "YOUR_DATABASE_USERNAME",
    "YOUR_DATABASE_PASSWORD",
    "YOUR_DATABASE_NAME"
);

if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "message" => "DB connection failed"
    ]));
}

$conn->set_charset("utf8mb4");
?>