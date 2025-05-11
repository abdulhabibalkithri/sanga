<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = trim($_POST['username']);
$email = trim($_POST['email']);

// Hakiki kama hakuna tupu
if (empty($username) || empty($email)) {
    header("Location: user_settings.php?error=Tafadhali jaza sehemu zote.");
    exit;
}

// Hakiki kama barua pepe ni halali
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: user_settings.php?error=Barua pepe si sahihi.");
    exit;
}

// Hakiki kama barua pepe ipo kwa mtumiaji mwingine
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$stmt->bind_param("si", $email, $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    header("Location: user_settings.php?error=Barua pepe tayari inatumiwa.");
    exit;
}

// Sasisha taarifa
$stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
$stmt->bind_param("ssi", $username, $email, $user_id);

if ($stmt->execute()) {
    header("Location: user_settings.php?success=Taarifa zimehifadhiwa kikamilifu.");
} else {
    header("Location: user_settings.php?error=Hitilafu imetokea, jaribu tena.");
}
?>
