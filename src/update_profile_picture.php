<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"])) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES["profile_picture"]["name"]);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowed)) {
        $newFileName = 'profile_' . $user_id . '_' . time() . '.' . $fileExt;
        $targetFilePath = $targetDir . $newFileName;

        // Hakikisha directory ipo
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Ondoa picha ya zamani kama ipo
        $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && !empty($res['profile_picture'])) {
            $oldFile = $targetDir . $res['profile_picture'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $newFileName, $user_id);
            $stmt->execute();
            header("Location: user_settings.php?success=Picha ya akaunti imebadilishwa.");
            exit;
        } else {
            header("Location: user_settings.php?error=Imeshindwa kupakia picha.");
            exit;
        }
    } else {
        header("Location: user_settings.php?error=Aina ya picha hairuhusiwi. Tumia JPG, PNG au GIF.");
        exit;
    }
} else {
    header("Location: user_settings.php?error=Hakuna picha iliyochaguliwa.");
    exit;
}
?>
