<?php
header("Content-Type: application/json");
include "db.php";

$input       = json_decode(file_get_contents("php://input"), true);
$user_id     = intval($input['user_id'] ?? 0);
$name        = trim($input['name'] ?? '');
$old_pwd     = $input['old_password'] ?? '';
$new_pwd     = $input['new_password'] ?? '';

if (!$user_id || !$name) {
    echo json_encode(["success" => false, "message" => "Name is required."]);
    exit;
}

// Get current user
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(["success" => false, "message" => "User not found."]);
    exit;
}

if ($new_pwd) {
    if (!$old_pwd) {
        echo json_encode(["success" => false, "message" => "Enter current password to change it."]);
        exit;
    }
    if (!password_verify($old_pwd, $user['password'])) {
        echo json_encode(["success" => false, "message" => "Current password is incorrect."]);
        exit;
    }
    if (strlen($new_pwd) < 6) {
        echo json_encode(["success" => false, "message" => "New password must be at least 6 characters."]);
        exit;
    }
    $hashed = password_hash($new_pwd, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, password = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $hashed, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE users SET full_name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $user_id);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed."]);
}
$stmt->close();
?>
