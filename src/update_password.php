<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Hakiki vigezo
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    header("Location: user_settings.php?error=Tafadhali jaza sehemu zote za nenosiri.");
    exit;
}

if ($new_password !== $confirm_password) {
    header("Location: user_settings.php?error=Nenosiri mpya hazifanani.");
    exit;
}

// Pata nenosiri la sasa
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!password_verify($current_password, $row['password'])) {
    header("Location: user_settings.php?error=Nenosiri la sasa si sahihi.");
    exit;
}

// Hakiki kama nenosiri jipya ni tofauti na la sasa
if (password_verify($new_password, $row['password'])) {
    header("Location: user_settings.php?error=Nenosiri jipya linapaswa kuwa tofauti na la sasa.");
    exit;
}

// Hash na sasisha nenosiri
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hashed_password, $user_id);

if ($stmt->execute()) {
    header("Location: user_settings.php?success=Nenosiri limebadilishwa kikamilifu.");
} else {
    header("Location: user_settings.php?error=Hitilafu imetokea, jaribu tena.");
}
?>
