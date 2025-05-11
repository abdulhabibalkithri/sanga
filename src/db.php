<?php
$host = "sanga_db.onrender.com"; // copy kutoka Render
$user = "sanga_user";             // copy kutoka Render
$password = "sanga_pass";         // copy kutoka Render
$dbname = "sanga_db";             // copy kutoka Render
$port = 3306;                     // Render itakuambia port sahihi

$conn = new mysqli($host, $user, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


