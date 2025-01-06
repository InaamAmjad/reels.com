<?php
// Database connection settings
$servername = "reels-server.mysql.database.azure.com";
$username = "reelsmydb";
$password = "Nomi4321";
$dbname = "reels_db";

$conn = mysqli_init();  // Initialize the MySQL connection

// Set up SSL parameters
mysqli_ssl_set($conn, NULL, NULL, "/home/site/ssl_certs/DigiCertGlobalRootCA.crt.pem", NULL, NULL);

// Establish a connection to the MySQL database
if (!mysqli_real_connect($conn, $servername, $username, $password, $dbname, 3306, NULL, MYSQLI_CLIENT_SSL)) {
    // Check connection and handle errors
    die("Connection failed: " . mysqli_connect_error());
}

?>
