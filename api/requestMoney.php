<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$requester_id = intval($data['user_id'] ?? 0);
$from_account = trim($data['from_account'] ?? '');
$amount = floatval($data['amount'] ?? 0);
$reason = trim($data['reason'] ?? '');

if (!$requester_id || !$from_account || $amount <= 0 || !$reason) {
    echo json_encode(["success" => false, "message" => "All fields required."]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE account_number = ?");
$stmt->bind_param("s", $from_account);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Account not found."]);
    exit;
}

$payer_id = $res->fetch_assoc()['id'];

if ($payer_id === $requester_id) {
    echo json_encode(["success" => false, "message" => "Cannot request from yourself."]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO money_requests (requester_id, payer_id, amount, reason) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iids", $requester_id, $payer_id, $amount, $reason);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "DB error: " . $conn->error]);
}
?>