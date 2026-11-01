<?php
header("Content-Type: application/json");
include "db.php";

$data       = json_decode(file_get_contents("php://input"), true);
$user_id    = intval($data['user_id'] ?? 0);    // logged in user (payer)
$request_id = intval($data['request_id'] ?? 0);

if (!$user_id || !$request_id) {
    echo json_encode(["success" => false, "message" => "Invalid data."]);
    exit;
}

// Get the request — payer_id must match logged in user
$stmt = $conn->prepare("
    SELECT mr.*, 
           u.full_name AS requester_name,
           u.account_number AS requester_account
    FROM money_requests mr
    JOIN users u ON u.id = mr.requester_id
    WHERE mr.id = ? AND mr.payer_id = ? AND mr.status = 'pending'
");
$stmt->bind_param("ii", $request_id, $user_id);
$stmt->execute();
$req = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$req) {
    echo json_encode(["success" => false, "message" => "Request not found."]);
    exit;
}

$amount = floatval($req['amount']);

// Check payer balance
$stmt = $conn->prepare("SELECT balance, full_name, account_number FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$payer = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($payer['balance'] < $amount) {
    echo json_encode(["success" => false, "message" => "Insufficient balance."]);
    exit;
}

$conn->begin_transaction();
try {
    // Deduct from payer
    $s = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ? AND balance >= ?");
    $s->bind_param("did", $amount, $user_id, $amount);
    $s->execute();
    if ($s->affected_rows === 0) throw new Exception("Balance check failed.");
    $s->close();

    // Credit requester
    $s = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $s->bind_param("di", $amount, $req['requester_id']);
    $s->execute();
    $s->close();

    // Mark request paid
    $s = $conn->prepare("UPDATE money_requests SET status = 'paid' WHERE id = ?");
    $s->bind_param("i", $request_id);
    $s->execute();
    $s->close();

    // Log debit for payer
    $desc1 = "Paid request to " . $req['requester_name'];
    $s = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, related_account) VALUES (?, 'debit', ?, ?, ?)");
    $s->bind_param("idss", $user_id, $amount, $desc1, $req['requester_account']);
    $s->execute(); $s->close();

    // Log credit for requester
    $desc2 = "Received from " . $payer['full_name'];
    $s = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, related_account) VALUES (?, 'credit', ?, ?, ?)");
    $s->bind_param("idss", $req['requester_id'], $amount, $desc2, $payer['account_number']);
    $s->execute(); $s->close();

    $conn->commit();
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
