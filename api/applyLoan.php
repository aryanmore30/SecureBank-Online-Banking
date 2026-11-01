<?php
header("Content-Type: application/json");
include "db.php";

$input    = json_decode(file_get_contents("php://input"), true);
$user_id  = intval($input['user_id'] ?? 0);
$type     = trim($input['loan_type'] ?? '');
$amount   = floatval($input['amount'] ?? 0);
$duration = intval($input['duration'] ?? 0);
$income   = floatval($input['income'] ?? 0);
$purpose  = trim($input['purpose'] ?? '');

if (!$user_id || !$type || $amount < 1000 || $duration < 1 || $income < 1) {
    echo json_encode(["success" => false, "message" => "Please fill all required fields."]);
    exit;
}

// Basic eligibility: loan should not exceed 50x monthly income
if ($amount > ($income * 50)) {
    echo json_encode(["success" => false, "message" => "Loan amount exceeds eligibility based on income."]);
    exit;
}

// Check for existing pending loan
$chk = $conn->prepare("SELECT id FROM loans WHERE user_id = ? AND status = 'pending'");
$chk->bind_param("i", $user_id);
$chk->execute();
$chk->store_result();
if ($chk->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "You already have a pending loan application."]);
    exit;
}
$chk->close();

// Calculate EMI (at standard 9% p.a. for now; admin can adjust)
$rate = 9 / 12 / 100;
$emi  = ($amount * $rate * pow(1 + $rate, $duration)) / (pow(1 + $rate, $duration) - 1);

// Generate loan ID
$loan_id = strtoupper(substr($type, 0, 2)) . '-' . date('Y') . '-' . rand(1000, 9999);

$stmt = $conn->prepare("INSERT INTO loans (user_id, loan_id, loan_type, amount, remaining, duration, income, purpose, emi, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("issddiids", $user_id, $loan_id, $type, $amount, $amount, $duration, $income, $purpose, $emi);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "loan_id" => $loan_id]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to submit application."]);
}
$stmt->close();
?>
