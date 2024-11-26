<?php
date_default_timezone_set('Asia/Kolkata');

$servername = "localhost"; // or your server name
$username = "u580404085_root";
$password = "Babaagro#123";
$dbname = "u580404085_babaagrodb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";




