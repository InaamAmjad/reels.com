<?php
session_start();
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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];  // The plain password entered by the user

    // Prepare the SQL query to retrieve the user's password from the database
    $query = "SELECT id, username, password, role FROM reels_db.users WHERE username = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $input_username);  // Bind username to query
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $username, $stored_password, $role);
    mysqli_stmt_fetch($stmt);  // Fetch the result

    // Check if the username exists in the database
    if ($username) {
        // Compare the entered password with the stored plain password
        if ($input_password === $stored_password) {
            // Password is correct, log the user in
            session_start();
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirect to dashboard or home page
            // Redirect based on role
        if ($role === 'creator') {
            echo "<script>alert('Login successful! Redirecting to video upload page.'); window.location.href='video.php';</script>";
        } else {
            echo "<script>alert('Login successful! Redirecting to default page.'); window.location.href='index.php';</script>";
        }
            
            exit;
        } else {
            // Incorrect password
            echo "<script>alert('Incorrect password. Please try again.'); window.location.href='signin.php';</script>";
            exit;
        }
    } else {
        // Username doesn't exist
        echo "<script>alert('Username not found.'); window.location.href='signin.php';</script>";
        exit;
    }
}
?>

<?php include('includes/header.php'); ?>
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
