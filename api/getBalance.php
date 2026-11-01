<?php
header("Content-Type: application/json");
include "db.php";

$user_id = intval($_GET['user_id'] ?? 0);
if (!$user_id) { 
    echo json_encode(["success" => false, "message" => "Unauthorized"]); 
    exit; 
}

$stmt = $conn->prepare("SELECT id, full_name, email, account_number, balance, is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
}

$user = $result->fetch_assoc();
echo json_encode([
    "success"        => true,
    "balance"        => $user['balance'],
    "full_name"      => $user['full_name'],
    "email"          => $user['email'],
    "account_number" => $user['account_number'],
    "is_admin"       => (bool)$user['is_admin']
]);
$stmt->close();
?>