<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];

if (!isset($_GET['with']) || !is_numeric($_GET['with'])) {
    echo "Mtumiaji hayupo au ID si sahihi.";
    exit;
}

$chat_with_id = (int) $_GET['with'];

if ($chat_with_id === $current_user_id) {
    echo "Huwezi kuzungumza na wewe mwenyewe.";
    exit;
}

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $chat_with_id);
$stmt->execute();
$result = $stmt->get_result();
$chat_with = $result->fetch_assoc();

if (!$chat_with) {
    echo "Mtumiaji huyo hakupatikana.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
   $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $current_user_id, $chat_with_id, $message);
        $stmt->execute();
        header("Location: chat.php?with=" . $chat_with_id);
        exit;
    }
}

$stmt = $conn->prepare("SELECT sender_id, message, sent_at FROM messages 
    WHERE (sender_id = ? AND receiver_id = ?) 
    OR (sender_id = ? AND receiver_id = ?) ORDER BY sent_at ASC");
$stmt->bind_param("iiii", $current_user_id, $chat_with_id, $chat_with_id, $current_user_id);
$stmt->execute();
$messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Chat na <?php echo htmlspecialchars($chat_with['username']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(120deg, #dbeafe, #e0e7ff);
        color: #333;
    }

    header {
        background-color: #6366f1;
        color: white;
        padding: 20px 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    .chat-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 30px;
        background-color: #ffffff;
        border-radius: 16px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #4f46e5;
        text-align: center;
        margin-bottom: 30px;
    }

    .chat-box {
        background-color: #f9fafb;
        padding: 20px;
        border-radius: 12px;
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        scroll-behavior: smooth;
    }

    .message {
        padding: 12px 16px;
        border-radius: 20px;
        max-width: 70%;
        margin-bottom: 15px;
        position: relative;
        word-wrap: break-word;
        animation: fadeIn 0.3s ease-in;
    }

    .message.you {
        background-color: #4f46e5;
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 4px;
    }

    .message.other {
        background-color: #e0e7ff;
        color: #1f2937;
        margin-right: auto;
        border-bottom-left-radius: 4px;
    }

    .message small {
        font-size: 12px;
        display: block;
        margin-top: 6px;
        opacity: 0.7;
    }

    form textarea {
        width: 100%;
        padding: 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        resize: none;
        font-size: 15px;
        margin-top: 10px;
        margin-bottom: 14px;
        font-family: 'Poppins', sans-serif;
    }

    form button {
        width: 100%;
        padding: 14px;
        background-color: #6366f1;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    form button:hover {
        background-color: #4f46e5;
    }

    .back-link {
        text-align: center;
        margin-top: 25px;
    }

    .back-link a {
        color: #4f46e5;
        text-decoration: none;
        font-weight: 600;
    }

    .back-link a:hover {
        text-decoration: underline;
        color: #3b34c2;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-thumb {
        background: #c7d2fe;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }
</style>

</head>
<body>
    <header>
        <div><strong>Chat</strong> - <?php echo htmlspecialchars($current_username); ?></div>
        <div><a href="dashboard.php" style="color: white; text-decoration: none;"><i class="fas fa-arrow-left"></i> Dashibodi</a></div>
    </header>

    <div class="chat-container">
        <h2>Unazungumza na: <?php echo htmlspecialchars($chat_with['username']); ?></h2>

        <div class="chat-box">
            <?php foreach ($messages as $msg): ?>
                <div class="message <?php echo $msg['sender_id'] == $current_user_id ? 'you' : 'other'; ?>">
                    <strong><?php echo $msg['sender_id'] == $current_user_id ? 'Wewe' : htmlspecialchars($chat_with['username']); ?>:</strong><br>
                    <?php echo htmlspecialchars($msg['message']); ?>
                    <small><?php echo (new DateTime($msg['sent_at']))->format('Y-m-d H:i'); ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="post">
            <textarea name="message" rows="3" placeholder="Andika ujumbe wako..." required></textarea>
            <button type="submit">Tuma</button>
        </form>

        <div class="back-link">
            <a href="dashboard.php">⟵ Rudi kwenye Dashibodi</a>
        </div>
    </div>
</body>
</html>
