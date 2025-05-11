<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$results = [];
$search_query = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_query = trim($_POST["search"]);
    
    if (!empty($search_query)) {
        $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE username LIKE ? OR email LIKE ? AND id != ?");
        $like = "%$search_query%";
        $stmt->bind_param("ssi", $like, $like, $_SESSION['user_id']);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Tafuta Mtumiaji</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Tafuta Rafiki</h2>
        <form method="POST">
            <input type="text" name="search" placeholder="Jina au Email" value="<?php echo htmlspecialchars($search_query); ?>" required>
            <button type="submit">Tafuta</button>
        </form>

        <?php if (!empty($results)): ?>
            <h3>Matokeo:</h3>
            <ul>
                <?php foreach ($results as $user): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                        <br><small><?php echo htmlspecialchars($user['email']); ?></small><br>
                        <a href="chat.php?user_id=<?php echo $user['id']; ?>">Tuma Ujumbe</a>
                    </li>
                    <hr>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p>Hakuna mtumiaji aliyepatikana kwa jina au email hiyo.</p>
        <?php endif; ?>
    </div>
</body>
</html>
