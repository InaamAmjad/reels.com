<?php
// Database connection settings
$servername = "reels-server.mysql.database.azure.com";
$username = "reelsmydb";
$password = "CO$r2iaiKYUkU7Jv";
$dbname = "reels_db";

$con = mysqli_init();
mysqli_ssl_set($con,NULL,NULL, "Downloads", NULL, NULL);
mysqli_real_connect($conn, "reels-server.mysql.database.azure.com", "reelsmydb", "{your_password}", "{your_database}", 3306, MYSQLI_CLIENT_SSL);
// Create connection


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
