<?php
session_start();
require_once 'db.php';

if (!isset($_GET['id'])) {
    die('User not found.');
}

$userId = (int) $_GET['id'];

// Fetch user details
$stmt = $conn->prepare("SELECT id, username, email, bio, profile_picture, state FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die('User not found.');
}

$currentUserId = $_SESSION['user_id'] ?? null;

// Update state if owner of profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $currentUserId === $userId && isset($_POST['state'])) {
    $newState = $_POST['state'];
    $update = $conn->prepare("UPDATE users SET state = ? WHERE id = ?");
    $update->bind_param("si", $newState, $userId);
    $update->execute();
    header("Location: profile.php?id=$userId");
    exit;
}

// Count followers
$stmt = $conn->prepare("SELECT COUNT(*) AS follower_count FROM follows WHERE following_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$followerResult = $stmt->get_result()->fetch_assoc();
$followCount = $followerResult['follower_count'];

// Count following
$stmt = $conn->prepare("SELECT COUNT(*) AS following_count FROM follows WHERE follower_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$followingResult = $stmt->get_result()->fetch_assoc();
$followingCount = $followingResult['following_count'];

// Check if following
$isFollowing = false;
if ($currentUserId && $currentUserId !== $userId) {
    $check = $conn->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND following_id = ?");
    $check->bind_param("ii", $currentUserId, $userId);
    $check->execute();
    $isFollowing = $check->get_result()->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($user['username']); ?> | Wasifu</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #f4f7fa, #eef2f6);
            color: #1f2937;
        }

        .profile-container {
            max-width: 800px;
            margin: 80px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .profile-picture {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #4f46e5;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .profile-picture:hover {
            transform: scale(1.05);
        }

        h2 {
            font-size: 30px;
            margin: 10px 0;
            color: #111827;
        }

        p {
            font-size: 16px;
            color: #374151;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            padding: 14px;
            background: #f9fafb;
            border-radius: 12px;
            font-weight: 600;
            color: #333;
        }

        .follow-btn,
        .state-form button {
            padding: 10px 24px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 10px;
            border: none;
            background-color: #4f46e5;
            color: white;
            margin-top: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .follow-btn:hover,
        .state-form button:hover {
            background-color: #4338ca;
        }

        .state-form {
            margin-top: 20px;
        }

        .state-form select {
            padding: 8px 16px;
            font-size: 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }

        .state-badge {
            display: inline-block;
            padding: 8px 20px;
            margin-top: 12px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            color: white;
        }

        .free {
            background-color: #10b981;
        }

        .busy {
            background-color: #f59e0b;
        }

        .booked {
            background-color: #ef4444;
        }

        @media (max-width: 600px) {
            .stats {
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
</head>
<body>

<div class="profile-container">
    <img src="uploads/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.jpg'); ?>" alt="Picha ya wasifu" class="profile-picture">
    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
    <p class="state-badge <?php echo htmlspecialchars($user['state']); ?>">
        <?php echo ucfirst($user['state']); ?>
    </p>

    <?php if ($currentUserId && $currentUserId !== $userId): ?>
        <form method="post" action="follow_toggle.php">
            <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
            <button type="submit" class="follow-btn">
                <?php echo $isFollowing ? 'Acha Kumfuata' : 'Mfuate'; ?>
            </button>
        </form>
    <?php endif; ?>

    <?php if ($currentUserId === $userId): ?>
        <form method="post" class="state-form">
            <label for="state">Hali Yako:</label>
            <select name="state" required>
                <option value="free" <?php if ($user['state'] === 'free') echo 'selected'; ?>>Free</option>
                <option value="busy" <?php if ($user['state'] === 'busy') echo 'selected'; ?>>Busy</option>
                <option value="booked" <?php if ($user['state'] === 'booked') echo 'selected'; ?>>Booked</option>
            </select>
            <button type="submit">Sasisha</button>
        </form>
    <?php endif; ?>

    <div class="stats">
        <div><i class="fas fa-user-friends"></i> Followers: <?php echo $followCount; ?></div>
        <div><i class="fas fa-users"></i> Following: <?php echo $followingCount; ?></div>
    </div>
</div>

</body>
</html>
