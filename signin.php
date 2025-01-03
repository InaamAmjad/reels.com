<?php include('includes/header.php'); ?>
<?php
// Start the session
session_start();
$serverName = "reels-server.mysql.database.azure.com";  // Full server name
$connectionOptions = array(
    "Database" => "reels_db", 
    "Uid" => "reelsmydb", 
    "PWD" => "Nomi4321",
    "Encrypt" => true,                  // Enable SSL encryption
    "TrustServerCertificate" => false,  // Ensure the server certificate is validated
);
echo "<script>alert('valuesadded');</script>";
$conn = sqlsrv_connect($serverName, $connectionOptions);

echo "<script>alert('values set in conn');</script>";
// Check connection
if ($conn->connect_error) {
  echo "<script>alert('dying');</script>";
    die("Connection failed: " . $conn->connect_error);
}else {
    // Show an alert for a successful connection
    echo "<script>alert('Connection successful');</script>";
}

 echo "<script>alert('endpoint');</script>";
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    // Validate that form inputs are set and not empty
    if (empty($_POST['username']) || empty($_POST['password'])) {
        echo "<script>alert('Please fill in both username and password.');</script>";
        exit;
    }

    // Retrieve username and password from form
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = trim($_POST['password']); 

    // Validate credentials
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user information in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'creator') {
                header("Location: video.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            echo "<script>alert('Incorrect password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('No user found with that username.');</script>";
    }

    // Close the database connection
    $conn->close();
}
?>
<main>
    <h1>Sign In</h1>
    <form action="signin.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" name="signin">Sign In</button>
    </form>
</main>

<?php include('includes/footer.php'); ?>
