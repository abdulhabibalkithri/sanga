<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$searchResults = [];
$searchPerformed = false;
$searchValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchPerformed = true;
    $searchValue = trim($_POST['search']);
    $searchTerm = "%" . $searchValue . "%";
    $stmt = $conn->prepare("SELECT id, username, profile_picture, state FROM users WHERE username LIKE ? AND id != ?");
    $stmt->bind_param("si", $searchTerm, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $searchResults = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $stmt = $conn->prepare("SELECT id, username, profile_picture, state FROM users WHERE id != ?");
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $searchResults = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Dashibodi | <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f7fc;
            color: #333;
        }

        header {
            background-color: #4f46e5;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header-right a {
            color: white;
            margin-left: 15px;
            text-decoration: none;
            font-size: 16px;
        }

        .header-right a:hover {
            text-decoration: underline;
        }

        .container {
            flex: 1;
            max-width: 1100px;
            width: 100%;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            margin-bottom: 25px;
        }

        form input[type="text"] {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px 0 0 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        form input[type="text"]:focus {
            border-color: #4f46e5;
            outline: none;
        }

        form button {
            padding: 12px 20px;
            border: none;
            background-color: #4f46e5;
            color: white;
            font-size: 16px;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #3b3aeb;
        }

        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 25px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .user-grid li {
            background: #f9f9f9;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .user-grid li:hover {
            transform: scale(1.05);
        }

        .user-grid img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .state {
            display: inline-block;
            margin-top: 8px;
            padding: 5px 12px;
            border-radius: 18px;
            font-size: 14px;
            color: white;
            text-transform: capitalize;
        }

        .free { background-color: #10b981; }
        .busy { background-color: #f59e0b; }
        .booked { background-color: #ef4444; }

        .chat-btn {
            display: block;
            margin-top: 15px;
            padding: 10px 18px;
            background-color: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
            transition: background-color 0.3s;
        }

        .chat-btn i {
            margin-right: 8px;
        }

        .chat-btn:hover {
            background-color: #3b3aeb;
        }

        .no-results {
            text-align: center;
            font-size: 18px;
            color: #666;
        }
    </style>
</head>
<body>

<header>
    <h2>Karibu, <?php echo htmlspecialchars($username); ?></h2>
    <div class="header-right">
        <a href="user_profile.php"><i class="fas fa-user-edit"></i> Hariri Wasifu Wangu</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Ondoka</a>
    </div>
</header>

<div class="container">
    <form method="post">
        <input type="text" name="search" placeholder="Tafuta mtumiaji kwa jina..." value="<?php echo htmlspecialchars($searchValue); ?>">
        <button type="submit"><i class="fas fa-search"></i> Tafuta</button>
    </form>

    <h3><?php echo $searchPerformed ? "Matokeo ya Utafutaji:" : "Watumiaji Wote:"; ?></h3>

    <?php if (!empty($searchResults)): ?>
        <ul class="user-grid">
            <?php foreach ($searchResults as $user): ?>
                <li>
                    <a href="profile.php?id=<?php echo $user['id']; ?>">
                        <img src="uploads/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.jpg'); ?>" alt="Picha">
                        <div><?php echo htmlspecialchars($user['username']); ?></div>
                        <div class="state <?php echo htmlspecialchars($user['state']); ?>"><?php echo ucfirst($user['state']); ?></div>
                    </a>
                    <a href="chat.php?with=<?php echo $user['id']; ?>" class="chat-btn"><i class="fas fa-comment-dots"></i> Chat</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="no-results">Hakuna watumiaji waliopatikana.</p>
    <?php endif; ?>
</div>

</body>
</html>
<script>
    window.onload = function() {
        const chatBox = document.querySelector('.chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    };
</script>
