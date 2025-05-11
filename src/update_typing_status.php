<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit();
}

$current_user_id = $_SESSION['user_id'];

// Check typing status
if (isset($_GET['check_typing']) && isset($_GET['sender_id'])) {
    $sender_id = intval($_GET['sender_id']);
    $receiver_id = $current_user_id;

    $stmt = $conn->prepare("SELECT is_typing FROM typing_status WHERE sender_id = ? AND receiver_id = ?");
    $stmt->bind_param("ii", $sender_id, $receiver_id);
    $stmt->execute();
    $stmt->bind_result($is_typing);
    if ($stmt->fetch()) {
        echo $is_typing;
    } else {
        echo '0';
    }
    exit();
}

// Update typing status
if (isset($_POST['receiver_id']) && isset($_POST['is_typing'])) {
    $receiver_id = intval($_POST['receiver_id']);
    $is_typing = intval($_POST['is_typing']);

    $stmt = $conn->prepare("REPLACE INTO typing_status (sender_id, receiver_id, is_typing) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $current_user_id, $receiver_id, $is_typing);
    $stmt->execute();
}
?>
