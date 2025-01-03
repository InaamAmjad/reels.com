<?php
// Database connection settings
$serverName = "reels-server.mysql.database.azure.com";  // Full server name
$connectionOptions = array(
    "Database" => "reels_db", 
    "Uid" => "reelsmydb", 
    "PWD" => "Nomi4321",
    "Encrypt" => true,                  // Enable SSL encryption
    "TrustServerCertificate" => false,  // Ensure the server certificate is validated
);
$conn = sqlsrv_connect($serverName, $connectionOptions);
// Check connection
if ($conn->connect_error) {
  echo "<script>alert('dying');</script>";
    die("Connection failed: " . $conn->connect_error);
}else {
    // Show an alert for a successful connection
    echo "<script>alert('Connection successful');</script>";
}
?>
