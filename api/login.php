<?php
header("Content-Type: application/json");
session_start();
include "db.php";

$input = json_decode(file_get_contents("php://input"), true);
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Email and password are required."]);
    exit;
}

$stmt = $conn->prepare("SELECT id, full_name, email, password, account_number, is_admin FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No account found with this email."]);
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Incorrect password."]);
    exit;
}

// Set server-side session
$_SESSION['user_id']  = $user['id'];
$_SESSION['email']    = $user['email'];
$_SESSION['is_admin'] = $user['is_admin'];

echo json_encode([
    "success"  => true,
    "userId"   => $user['id'],
    "name"     => $user['full_name'],
    "email"    => $user['email'],
    "isAdmin"  => (bool)$user['is_admin']
]);
$stmt->close();
?>
