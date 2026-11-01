<?php
header("Content-Type: application/json");
include "db.php";

$user_id = intval($_GET['user_id'] ?? 0);
if (!$user_id) { echo json_encode([]); exit; }

// Show requests where THIS user is the PAYER (they need to pay)
$stmt = $conn->prepare("
    SELECT mr.id, mr.amount, mr.reason, mr.created_at,
           u.full_name AS requester_name,
           u.account_number AS requester_account
    FROM money_requests mr
    JOIN users u ON u.id = mr.requester_id
    WHERE mr.payer_id = ? AND mr.status = 'pending'
    ORDER BY mr.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}
echo json_encode($requests);
$stmt->close();
?>
