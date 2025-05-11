<?php
$host = getenv('DB_HOST');  // Database host
$user = getenv('DB_USER');  // Database username
$password = getenv('DB_PASSWORD');  // Database password
$database = getenv('DB_NAME');  // Database name

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
