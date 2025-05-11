<?php
// Confirming if environment variables are being set correctly
$host = getenv('DB_HOST');  // Database host
$user = getenv('DB_USER');  // Database username
$password = getenv('DB_PASSWORD');  // Database password
$database = getenv('DB_NAME');  // Database name

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
