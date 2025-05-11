<?php
$host = 'sanga-db-1'; // container name from docker-compose
$user = 'sanga_user';
$password = 'sanga_pass';
$database = 'chat';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
