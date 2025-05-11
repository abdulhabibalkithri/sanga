<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    die('Lazima uingie kwanza.');
}

$currentUserId = $_SESSION['user_id'];
$targetUserId = $_POST['user_id'] ?? null;

if (!$targetUserId || !is_numeric($targetUserId)) {
    die('Mtumiaji hajatajwa vizuri.');
}

$targetUserId = (int) $targetUserId;

// Check if already following
$check = $conn->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND following_id = ?");
$check->bind_param("ii", $currentUserId, $targetUserId);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Unfollow
    $unfollow = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
    $unfollow->bind_param("ii", $currentUserId, $targetUserId);
    $unfollow->execute();
} else {
    // Follow
    $follow = $conn->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
    $follow->bind_param("ii", $currentUserId, $targetUserId);
    $follow->execute();
}

header("Location: profile.php?id=$targetUserId");
exit;
?>
