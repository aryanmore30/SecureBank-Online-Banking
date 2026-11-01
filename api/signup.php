<?php
header("Content-Type: application/json");
include "db.php";

$input = json_decode(file_get_contents("php://input"), true);
$name     = trim($input['name'] ?? '');
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (!$name || !$email || !$password) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email address."]);
    exit;
}
if (strlen($password) < 6) {
    echo json_encode(["success" => false, "message" => "Password must be at least 6 characters."]);
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already registered."]);
    exit;
}
$stmt->close();

// Generate unique 8-digit account number
do {
    $account = (string)rand(10000000, 99999999);
    $check = $conn->prepare("SELECT id FROM users WHERE account_number = ?");
    $check->bind_param("s", $account);
    $check->execute();
    $check->store_result();
    $exists = $check->num_rows > 0;
    $check->close();
} while ($exists);

$hashed = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO users (full_name, email, password, account_number, balance) VALUES (?, ?, ?, ?, 1000.00)");
$stmt->bind_param("ssss", $name, $email, $hashed, $account);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Account created successfully. Your account number is: " . $account]);
} else {
    echo json_encode(["success" => false, "message" => "Registration failed. Please try again."]);
}
$stmt->close();
?>
