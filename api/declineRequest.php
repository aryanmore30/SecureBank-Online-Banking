<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$request_id = intval($data['request_id'] ?? 0);
$user_id    = intval($data['user_id'] ?? 0);

if (!$request_id || !$user_id) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE money_requests 
     SET status = 'declined' 
     WHERE id = ? AND to_user = ?"
);
$stmt->bind_param("ii", $request_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Request declined"]);
} else {
    echo json_encode(["success" => false, "message" => "DB error"]);
}
?>
