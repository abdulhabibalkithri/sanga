<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$current_user_id = $_SESSION['user_id'];
$unreadCounts = [];

$stmt = $conn->prepare("SELECT sender_id, COUNT(*) as unread_count FROM messages WHERE receiver_id = ? AND is_read = 0 GROUP BY sender_id");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $unreadCounts[$row['sender_id']] = $row['unread_count'];
}

echo json_encode($unreadCounts);
?>
