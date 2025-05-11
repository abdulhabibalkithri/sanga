<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Please log in.");
}

$user_id = $_SESSION['user_id'];

// Get unique user IDs you've exchanged messages with
$stmt = $pdo->prepare("
    SELECT DISTINCT 
        IF(sender_id = ?, receiver_id, sender_id) AS other_user
    FROM messages
    WHERE sender_id = ? OR receiver_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user_id, $user_id, $user_id]);
$users = $stmt->fetchAll();
?>

<h2>Your Inbox</h2>
<ul>
<?php foreach ($users as $row): 
    $other_id = $row['other_user'];
    $userStmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $userStmt->execute([$other_id]);
    $otherUser = $userStmt->fetch();
?>
    <li><a href="conversation.php?with=<?php echo $other_id; ?>">Chat with @<?php echo htmlspecialchars($otherUser['username']); ?></a></li>
<?php endforeach; ?>
</ul>
