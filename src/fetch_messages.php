<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['with'])) {
    exit();
}

$current_user_id = $_SESSION['user_id'];
$chat_with_id = intval($_GET['with']);

// Mark all messages from other user as read
$stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
$stmt->bind_param("ii", $chat_with_id, $current_user_id);
$stmt->execute();

// Fetch all messages
$stmt = $conn->prepare("SELECT sender_id, message, sent_at FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY sent_at ASC");
$stmt->bind_param("iiii", $current_user_id, $chat_with_id, $chat_with_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>
