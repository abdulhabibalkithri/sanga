<?php
session_start();
require_once 'db.php';

// Hakikisha mtumiaji ameingia
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Pata taarifa za sasa za mtumiaji
$stmt = $conn->prepare("SELECT username, email, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Chukua ujumbe wa mafanikio au makosa kutoka URL
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

// Chukua data kutoka kwa POST ikiwa imetumwa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'])) {
        // Badilisha jina la mtumiaji na barua pepe
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        if (empty($username) || empty($email)) {
            $error = "Tafadhali jaza sehemu zote zinazohitajika";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Barua pepe si sahihi";
        } else {
            $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $update_stmt->bind_param("ssi", $username, $email, $user_id);
            if ($update_stmt->execute()) {
                $success = "Taarifa zimebadilishwa kikamilifu!";
                $user['username'] = $username;
                $user['email'] = $email;
            } else {
                $error = "Kuna tatizo kuhifadhi mabadiliko. Tafadhali jaribu tena.";
            }
        }
    } elseif (isset($_FILES['profile_picture'])) {
        $target_dir = "uploads/";
        $imageFileType = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $new_filename = "profile_" . $user_id . "." . $imageFileType;
        $target_path = $target_dir . $new_filename;

        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check === false) {
            $error = "Faili sio picha.";
        } elseif ($_FILES["profile_picture"]["size"] > 5000000) {
            $error = "Picha ni kubwa mno. Tafadhali tumia picha chini ya 5MB.";
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $error = "Tafadhali tumia picha ya aina JPG, JPEG, PNG au GIF.";
        } else {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_path)) {
                $update_stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                $update_stmt->bind_param("si", $new_filename, $user_id);
                if ($update_stmt->execute()) {
                    $success = "Picha ya wasifu imebadilishwa kikamilifu!";
                    $user['profile_picture'] = $new_filename;
                } else {
                    $error = "Kuna tatizo kuhifadhi picha. Tafadhali jaribu tena.";
                }
            } else {
                $error = "Kuna tatizo kupakia picha. Tafadhali jaribu tena.";
            }
        }
    } elseif (isset($_POST['current_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $error = "Nenosiri jipya halilingani.";
        } else {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $db_user = $result->fetch_assoc();

            if (password_verify($current_password, $db_user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $user_id);
                if ($update_stmt->execute()) {
                    $success = "Nenosiri limebadilishwa kikamilifu!";
                } else {
                    $error = "Kuna tatizo kuhifadhi nenosiri jipya. Tafadhali jaribu tena.";
                }
            } else {
                $error = "Nenosiri la sasa si sahihi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Mpangilio wa Mtumiaji</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(120deg, #1e3c72, #2a5298);
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        h2 {
            text-align: center;
            color: #1e3c72;
            font-size: 32px;
            margin-bottom: 30px;
        }

        .profile-pic {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto 10px;
            border: 4px solid #2a5298;
        }

        .form-section {
            margin-bottom: 40px;
            padding: 25px;
            background: #f9f9f9;
            border-radius: 12px;
            border-left: 5px solid #2a5298;
        }

        .form-section h3 {
            color: #2a5298;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            color: #444;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        .btn {
            padding: 12px 25px;
            background: #2a5298;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #1e3c72;
        }

        .message, .error {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
            font-weight: 500;
        }

        .message {
            background: #e1fbe1;
            color: #207520;
        }

        .error {
            background: #ffe1e1;
            color: #c11c1c;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: #2a5298;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #1e3c72;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Mpangilio wa Mtumiaji</h2>

        <img src="uploads/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.jpg'); ?>" alt="Profile Picture" class="profile-pic">
        <p style="text-align:center; font-size: 18px;"><strong><?php echo htmlspecialchars($user['username']); ?></strong></p>

        <?php if (!empty($success)): ?>
            <div class="message">✅ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Badilisha Picha ya Profile -->
        <div class="form-section">
            <h3>Badilisha Picha ya Profile</h3>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profile_picture">Chagua Picha Mpya:</label>
                    <input type="file" name="profile_picture" id="profile_picture" required accept="image/*">
                </div>
                <button type="submit" class="btn">Pakia Picha Mpya</button>
            </form>
        </div>

        <!-- Badilisha Taarifa za Msingi -->
        <div class="form-section">
            <h3>Badilisha Taarifa za Msingi</h3>
            <form action="" method="post">
                <div class="form-group">
                    <label for="username">Jina la Mtumiaji:</label>
                    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Barua Pepe:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <button type="submit" class="btn">Hifadhi Mabadiliko</button>
            </form>
        </div>

        <!-- Badilisha Nenosiri -->
        <div class="form-section">
            <h3>Badilisha Nenosiri</h3>
            <form action="" method="post">
                <div class="form-group">
                    <label for="current_password">Nenosiri la Sasa:</label>
                    <input type="password" name="current_password" id="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Nenosiri Jipya:</label>
                    <input type="password" name="new_password" id="new_password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Rudia Nenosiri Jipya:</label>
                    <input type="password" name="confirm_password" id="confirm_password" required minlength="6">
                </div>
                <button type="submit" class="btn">Badilisha Nenosiri</button>
            </form>
        </div>

        <div class="back-link">
            <a href="dashboard.php">← Rudi kwenye Dashibodi</a>
        </div>
    </div>
</body>
</html>
