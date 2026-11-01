<?php
header("Content-Type: application/json");
include "db.php";

$user_id = intval($_GET['user_id'] ?? 0);
if (!$user_id) { echo json_encode(["success" => false, "message" => "Unauthorized"]); exit; }

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

$result = $conn->query("SELECT l.*, u.full_name, u.email FROM loans l JOIN users u ON u.id = l.user_id WHERE l.status = 'pending' ORDER BY l.created_at ASC");

$loans = [];
while ($row = $result->fetch_assoc()) {
    $loans[] = $row;
}
echo json_encode($loans);
?>
