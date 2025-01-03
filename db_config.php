<?php
// Database connection settings
$servername = "reels-server.mysql.database.azure.com";
$username = "reelsmydb";
$password = "CO$r2iaiKYUkU7Jv";
$dbname = "reels_db";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
