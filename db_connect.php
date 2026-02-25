<?php
$servername = "localhost";  // Usually 'localhost'
$username = "root";         // Default phpMyAdmin username
$password = "";             // Default is empty for localhost
$dbname = "imsata_db";      // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>