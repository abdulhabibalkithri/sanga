<?php
session_start();
require_once 'db.php'; // connection ya database
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Email na password ni lazima.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("location: dashboard.php");
            exit;
        } else {
            $errors[] = "Email au password si sahihi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Ingia - Chat App</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #90caf9);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h2 {
            text-align: center;
            color: #0d47a1;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 16px;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #1976d2;
            box-shadow: 0 0 8px rgba(25, 118, 210, 0.3);
            outline: none;
        }
        button {
            width: 100%;
            background-color: #1976d2;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #0d47a1;
        }
        .error {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        p {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        a {
            color: #1976d2;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                border-radius: 10px;
            }
            h2 {
                font-size: 22px;
            }
            input[type="email"],
            input[type="password"],
            button {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Karibu - Ingia</h2>
        <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
        </div>
        <?php endif; ?>
        <form method="post" action="">
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Ingia</button>
        </form>
        <p>Huna akaunti? <a href="register.php">Jisajili hapa</a></p>
    </div>
</body>
</html>
