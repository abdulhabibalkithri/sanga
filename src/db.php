<?php
// Debugging to check if environment variables are set
echo 'DB_HOST: ' . getenv('DB_HOST') . '<br>';
echo 'DB_USER: ' . getenv('DB_USER') . '<br>';
echo 'DB_PASSWORD: ' . getenv('DB_PASSWORD') . '<br>';
echo 'DB_NAME: ' . getenv('DB_NAME') . '<br>';

// Confirming if environment variables are being set correctly
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_NAME');

// Check if environment variables are null or empty
if (!$host || !$user || !$password || !$database) {
    die("Environment variables are not set properly.");
}

// Kujaribu kuungana na database
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully!";
?>

