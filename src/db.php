<?php
$host = getenv('DB_HOST');  // Database host, hapa ni 'db' au jina la container yako
$user = getenv('DB_USER');  // Database username
$password = getenv('DB_PASSWORD');  // Database password
$database = getenv('DB_NAME');  // Database name

// Kujaribu kuungana na database
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
