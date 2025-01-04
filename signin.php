<?php
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
   // Database connection settings
$host = "reels-server.mysql.database.azure.com"; // Hostname from Workbench
$username = "reelsmydb";                        // Username from Workbench
$password = "Nomi4321";                         // Password you set in Workbench
$dbname = "reels_db";                           // Name of your database
$port = 3306;                                   // Default MySQL port

// Enable SSL (based on the SSL enabled in your Workbench setup)
$options = array(
    PDO::MYSQL_ATTR_SSL_CA => '/home/site/ssl_certs/DigiCertGlobalRootCA.crt.pem', // SSL certificate
);

try {
    // Establish connection using PDO
    $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, $options);

    // Set error mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<script>alert('Connection successful!');</script>";
} catch (PDOException $e) {
    die("<script>alert('Connection failed: " . $e->getMessage() . "');</script>");
}

    // Validate inputs
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill in both username and password.');</script>";
        exit;
    }

    // Prepare and execute query
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

    sqlsrv_close($conn);
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
