<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['with'])) {
    die("Unauthorized.");
}

$me = $_SESSION['user_id'];
$other = (int) $_GET['with'];

// Fetch messages
$stmt = $pdo->prepare("
    SELECT * FROM messages 
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");
$stmt->execute([$me, $other, $other, $me]);
$messages = $stmt->fetchAll();
?>

<h2>Conversation</h2>
<div style="border:1px solid #ccc;padding:10px;">
    <?php foreach ($messages as $msg): ?>
        <p><strong><?php echo $msg['sender_id'] == $me ? 'You' : 'Them'; ?>:</strong>
            <?php echo htmlspecialchars($msg['message']); ?>
            <br><small><?php echo $msg['created_at']; ?></small></p>
        <hr>
    <?php endforeach; ?>
</div>

<form method="post" action="send_message.php?to=<?php echo $other; ?>">
    <textarea name="message" rows="3" cols="50" placeholder="Type a reply..."></textarea><br>
    <button type="submit">Send</button>
</form>
