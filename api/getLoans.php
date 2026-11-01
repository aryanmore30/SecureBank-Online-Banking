<?php
header("Content-Type: application/json");
include "db.php";

$user_id = intval($_GET['user_id'] ?? 0);
if (!$user_id) { echo json_encode([]); exit; }

$stmt = $conn->prepare("SELECT *, DATE_ADD(created_at, INTERVAL 30 DAY) AS next_due FROM loans WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$loans = [];
while ($row = $result->fetch_assoc()) {
    $row['next_due'] = date('d M Y', strtotime($row['next_due']));
    $loans[] = $row;
}
echo json_encode($loans);
$stmt->close();
?>
