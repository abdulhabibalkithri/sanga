<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['to'])) {
    die("Unauthorized access.");
}

$sender_id = $_SESSION['user_id'];
$receiver_id = (int) $_GET['to'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$sender_id, $receiver_id, $message]);
        header("Location: conversation.php?with=$receiver_id");
        exit;
    } else {
        $error = "Message cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Message</title>
</head>
<body>
    <h2>Send Message</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        <textarea name="message" rows="5" cols="50" placeholder="Write your message..."></textarea><br><br>
        <button type="submit">Send</button>
    </form>
</body>
</html>
