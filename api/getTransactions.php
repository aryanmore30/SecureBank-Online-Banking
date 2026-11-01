<?php
header("Content-Type: application/json");
include "db.php";

$user_id = intval($_GET['user_id'] ?? 0);
if (!$user_id) { echo json_encode([]); exit; }

$stmt = $conn->prepare("SELECT id, type, amount, description, related_account, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 100");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}
echo json_encode($transactions);
$stmt->close();
?>
