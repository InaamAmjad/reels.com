
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    // Database credentials
    $servername = "reels-server.mysql.database.azure.com";
    $username = "reelsmydb";
    $password = "Nomi4321";
    $dbname = "reels_db";

    // Initialize MySQL connection
    $con = mysqli_init();  // Initialize the MySQL connection

    // Set up SSL parameters
    mysqli_ssl_set($con, NULL, NULL, "/home/site/ssl_certs/DigiCertGlobalRootCA.crt.pem", NULL, NULL);

    // Establish a connection to the MySQL database
    if (!mysqli_real_connect($con, $servername, $username, $password, $dbname, 3306, NULL, MYSQLI_CLIENT_SSL)) {
        // Check connection and handle errors
        die("Connection failed: " . mysqli_connect_error());
    }

    // Sanitize and validate the inputs
    $input_username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $input_password = $_POST['password'];

    // Check if required fields are not empty
    if (empty($input_username) || empty($input_password)) {
        echo "<script>alert('Both fields are required.'); window.location.href='signin.php';</script>";
        exit;
    }

    // Query to check if the user exists
    $query = "SELECT id, username, password, role FROM reels_db.users WHERE username = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $input_username);  // Bind username to the query
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    // Check if user exists
    if (mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role);
        mysqli_stmt_fetch($stmt);

        // Verify the password
        if (password_verify($input_password, $hashed_password)) {
            // Start session and set session variables
            session_start();
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirect to dashboard or home page
            echo "<script>alert('Login successful!'); window.location.href='index.php';</script>";
            exit;
        } else {
            // Incorrect password
            echo "<script>alert('Incorrect password. Please try again.'); window.location.href='signin.php';</script>";
            exit;
        }
    } else {
        // Username does not exist
        echo "<script>alert('Username does not exist. Please check and try again.'); window.location.href='signin.php';</script>";
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
