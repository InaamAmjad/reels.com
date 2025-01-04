<?php
// Database connection settings
$servername = "reels-server.mysql.database.azure.com";
$username = "reelsmydb";
$password = "Nomi4321";
$dbname = "reels_db";

$con = mysqli_init();  // Initialize the MySQL connection

// Set up SSL parameters
mysqli_ssl_set($con, NULL, NULL, "/home/site/ssl_certs/DigiCertGlobalRootCA.crt.pem", NULL, NULL);

// Establish a connection to the MySQL database
if (!mysqli_real_connect($con, $servername, $username, $password, $dbname, 3306, NULL, MYSQLI_CLIENT_SSL)) {
    // Check connection and handle errors
    die("Connection failed: " . mysqli_connect_error());
}

// If connection is successful, continue your logic here
echo "Connection successful!";
?>
