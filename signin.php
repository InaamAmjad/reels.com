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
<?php
session_start();

// Database connection
$serverName = "reels-server.mysql.database.azure.com";
$connectionOptions = array(
    "Database" => "reels_db",
    "Uid" => "reelsmydb",
    "PWD" => "Nomi4321",
    "Encrypt" => true,
    "TrustServerCertificate" => false,
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("<script>alert('Connection failed: " . json_encode(sqlsrv_errors()) . "');</script>");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill in both username and password.');</script>";
        exit;
    }

    // Prepare query
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = sqlsrv_prepare($conn, $sql, array($username));
    if (!$stmt || !sqlsrv_execute($stmt)) {
        echo "<script>alert('Database query failed.');</script>";
        exit;
    }

    // Fetch results
    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        $redirect = ($user['role'] === 'creator') ? "video.php" : "index.php";
        header("Location: $redirect");
        exit;
    } else {
        echo "<script>alert('Invalid username or password.');</script>";
    }
}

sqlsrv_close($conn);
?>
