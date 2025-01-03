<?php
// Database connection settings
$servername = "reels-server.mysql.database.azure.com"; // Azure MySQL hostname
$username = "reelsmydb"; // Your username
$password = "CO$r2iaiKYUkU7Jv"; // Your password
$dbname = "reels_db"; // Your database name

// SSL certificate path (you need to download the CA certificate)
$ca_cert_path = "/home/site/ssl_certs/DigiCertGlobalRootCA.crt.pem"; // Replace with the actual path to the CA certificate

// Create connection
$conn = mysqli_init();

// Set SSL certificates
mysqli_ssl_set($conn, NULL, NULL, $ca_cert_path, NULL, NULL);

// Connect to MySQL with SSL
if (!mysqli_real_connect($conn, $servername, $username, $password, $dbname, 3306, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Connection successful!";
}
?>

 
