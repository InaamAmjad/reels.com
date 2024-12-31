<?php
// Database connection settings
$servername = "your_azure_sql_server";
$username = "your_username";
$password = "your_password";
$dbname = "reels_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
 
