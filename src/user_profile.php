<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, bio, profile_picture, state FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = $_POST['bio'] ?? '';
    $state = $_POST['state'] ?? 'free';
    $picture = $user['profile_picture'];

    if (!empty($_FILES['profile_picture']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileExt = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $fileName = 'profile_' . $userId . '_' . time() . '.' . $fileExt;
        $targetFilePath = $targetDir . $fileName;

        // Ondoa ya zamani
        if (!empty($user['profile_picture']) && file_exists($targetDir . $user['profile_picture'])) {
            unlink($targetDir . $user['profile_picture']);
        }

        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath);
        $picture = $fileName;
    }

    $stmt = $conn->prepare("UPDATE users SET bio = ?, profile_picture = ?, state = ? WHERE id = ?");
    $stmt->bind_param("sssi", $bio, $picture, $state, $userId);
    $stmt->execute();

    header("Location: dashboard.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Hariri Wasifu Wangu</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <style>
    body {
    background: linear-gradient(to right, #f5f7fa, #c3cfe2);
    font-family: 'Segoe UI', sans-serif;
    color: #333;
    margin: 0;
    padding: 0;
}

.edit-container {
    max-width: 600px;
    margin: 60px auto;
    background: #ffffff;
    padding: 35px 30px;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

h2 {
    color: #3a3d5c;
    text-align: center;
    margin-bottom: 25px;
    font-size: 26px;
}

label {
    font-weight: 600;
    display: block;
    margin-top: 18px;
    color: #555;
}

textarea,
input[type="file"],
select {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
    border: 1px solid #d0d7e2;
    border-radius: 8px;
    background-color: #f9fafc;
    font-size: 15px;
    transition: border-color 0.3s;
}

textarea:focus,
select:focus,
input[type="file"]:focus {
    outline: none;
    border-color: #4f46e5;
}

button {
    margin-top: 25px;
    background: #4f46e5;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    transition: background 0.3s ease;
}

button:hover {
    background: #3730a3;
}

.profile-preview {
    text-align: center;
    margin-bottom: 25px;
}

.profile-preview img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 4px solid #4f46e5;
    object-fit: cover;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
</style>
</head>
<body>

<div class="edit-container">
    <h2>Hariri Wasifu Wako</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="profile-preview">
            <img src="uploads/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.jpg'); ?>" alt="Picha ya sasa">
        </div>

        <label for="bio">Bio:</label>
        <textarea name="bio" rows="4"><?php echo htmlspecialchars($user['bio']); ?></textarea>

        <label for="state">Hali:</label>
        <select name="state">
            <option value="free" <?php if ($user['state'] === 'free') echo 'selected'; ?>>Free</option>
            <option value="busy" <?php if ($user['state'] === 'busy') echo 'selected'; ?>>Busy</option>
            <option value="booked" <?php if ($user['state'] === 'booked') echo 'selected'; ?>>Booked</option>
        </select>

        <label for="profile_picture">Badilisha Picha ya Wasifu:</label>
        <input type="file" name="profile_picture">

        <button type="submit"><i class="fas fa-save"></i> Hifadhi Mabadiliko</button>
    </form>
</div>

</body>
</html>
