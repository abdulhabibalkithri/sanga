<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    die('User not found or not logged in.');
}

$followedUserId = (int) $_GET['id'];
$followingUserId = (int) $_SESSION['user_id'];

// Check if the user is already following
$sql_check = "SELECT * FROM follows WHERE follower_id = ? AND following_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $followingUserId, $followedUserId);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // User is already following, so unfollow them
    $sql_unfollow = "DELETE FROM follows WHERE follower_id = ? AND following_id = ?";
    $stmt_unfollow = $conn->prepare($sql_unfollow);
    $stmt_unfollow->bind_param("ii", $followingUserId, $followedUserId);
    $stmt_unfollow->execute();
    $action = 'unfollowed';
} else {
    // User is not following, so follow them
    $sql_follow = "INSERT INTO follows (follower_id, following_id) VALUES (?, ?)";
    $stmt_follow = $conn->prepare($sql_follow);
    $stmt_follow->bind_param("ii", $followingUserId, $followedUserId);
    $stmt_follow->execute();
    $action = 'followed';
}

// Redirect back to the profile page
header("Location: profile.php?id=" . $followedUserId);
exit();
?>
