<?php
header("Content-Type: application/json");
include "db.php";

$input = json_decode(file_get_contents("php://input"), true);
$sender_id       = intval($input['user_id'] ?? 0);
$receiver_account = trim($input['receiver_account'] ?? '');
$amount          = floatval($input['amount'] ?? 0);
$note            = trim($input['note'] ?? 'Transfer');

if (!$sender_id || !$receiver_account || $amount <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}
if ($amount > 1000000) {
    echo json_encode(["success" => false, "message" => "Amount too large. Max ₹10,00,000 per transaction."]);
    exit;
}

// Get sender info
$stmt = $conn->prepare("SELECT id, account_number, balance FROM users WHERE id = ?");
$stmt->bind_param("i", $sender_id);
$stmt->execute();
$senderRow = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$senderRow) {
    echo json_encode(["success" => false, "message" => "Sender not found."]);
    exit;
}
if ($senderRow['account_number'] === $receiver_account) {
    echo json_encode(["success" => false, "message" => "Cannot send money to yourself."]);
    exit;
}
if ($senderRow['balance'] < $amount) {
    echo json_encode(["success" => false, "message" => "Insufficient balance. Your balance: ₹" . number_format($senderRow['balance'], 2)]);
    exit;
}

// Get receiver
$stmt = $conn->prepare("SELECT id, full_name FROM users WHERE account_number = ?");
$stmt->bind_param("s", $receiver_account);
$stmt->execute();
$receiverRow = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$receiverRow) {
    echo json_encode(["success" => false, "message" => "Receiver account not found. Check the account number."]);
    exit;
}

// Execute transaction
$conn->begin_transaction();
try {
    // Deduct from sender
    $s1 = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ? AND balance >= ?");
    $s1->bind_param("did", $amount, $sender_id, $amount);
    $s1->execute();
    if ($s1->affected_rows === 0) throw new Exception("Balance changed, please retry.");
    $s1->close();

    // Add to receiver
    $s2 = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $s2->bind_param("di", $amount, $receiverRow['id']);
    $s2->execute();
    $s2->close();

    // Record debit transaction for sender
    $desc1 = "Sent to " . $receiverRow['full_name'] . " — " . $note;
    $s3 = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, related_account) VALUES (?, 'debit', ?, ?, ?)");
    $s3->bind_param("idss", $sender_id, $amount, $desc1, $receiver_account);
    $s3->execute();
    $s3->close();

    // Record credit transaction for receiver
    $senderName = $senderRow['account_number'];
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->bind_param("i", $sender_id);
    $stmt->execute();
    $senderInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $desc2 = "Received from " . ($senderInfo['full_name'] ?? 'someone') . " — " . $note;
    $s4 = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, related_account) VALUES (?, 'credit', ?, ?, ?)");
    $s4->bind_param("idss", $receiverRow['id'], $amount, $desc2, $senderRow['account_number']);
    $s4->execute();
    $s4->close();

    $conn->commit();
    echo json_encode(["success" => true, "message" => "₹" . number_format($amount, 2) . " sent to " . $receiverRow['full_name']]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Transaction failed: " . $e->getMessage()]);
}
?>
