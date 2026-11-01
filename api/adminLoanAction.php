<?php
header("Content-Type: application/json");
include "db.php";

$input   = json_decode(file_get_contents("php://input"), true);
$user_id = intval($input['user_id'] ?? 0);
$loan_id = intval($input['loan_id'] ?? 0);
$action  = trim($input['action'] ?? '');

if (!in_array($action, ['approve', 'reject'])) {
    echo json_encode(["success" => false, "message" => "Invalid action."]);
    exit;
}

// Verify admin
$chk = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$chk->bind_param("i", $user_id);
$chk->execute();
$admin = $chk->get_result()->fetch_assoc();
$chk->close();

if (!$admin || !$admin['is_admin']) {
    echo json_encode(["success" => false, "message" => "Access denied."]);
    exit;
}

$status = ($action === 'approve') ? 'approved' : 'rejected';

if ($action === 'approve') {
    // Get loan details
    $stmt = $conn->prepare("SELECT * FROM loans WHERE id = ? AND status = 'pending'");
    $stmt->bind_param("i", $loan_id);
    $stmt->execute();
    $loan = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$loan) {
        echo json_encode(["success" => false, "message" => "Loan not found or already processed."]);
        exit;
    }

    $conn->begin_transaction();
    try {
        // Credit loan amount to user account
        $s1 = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $s1->bind_param("di", $loan['amount'], $loan['user_id']);
        $s1->execute(); $s1->close();

        // Add transaction record
        $desc = "Loan Disbursed: " . $loan['loan_type'] . " #" . $loan['loan_id'];
        $s2 = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'credit', ?, ?)");
        $s2->bind_param("ids", $loan['user_id'], $loan['amount'], $desc);
        $s2->execute(); $s2->close();

        // Update loan status
        $s3 = $conn->prepare("UPDATE loans SET status = 'approved' WHERE id = ?");
        $s3->bind_param("i", $loan_id);
        $s3->execute(); $s3->close();

        $conn->commit();
        echo json_encode(["success" => true, "message" => "Loan approved and amount disbursed."]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    $stmt = $conn->prepare("UPDATE loans SET status = 'rejected' WHERE id = ? AND status = 'pending'");
    $stmt->bind_param("i", $loan_id);
    $stmt->execute();
    echo json_encode(["success" => $stmt->affected_rows > 0, "message" => "Loan rejected."]);
    $stmt->close();
}
?>
