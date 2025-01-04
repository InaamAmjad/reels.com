<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    // Database connection settings
    $host = "reels-server.mysql.database.azure.com"; // Hostname
    $username = "reelsmydb";                         // Username
    $password = "Nomi4321";                          // Password
    $dbname = "reels_db";                            // Database name
    $port = 3306;                                    // MySQL port

    // SSL certificate path (adjust as needed for your environment)
    $ssl_ca = '/home/site/ssl_certs/DigiCertGlobalRootCA.crt.pem';

    // PDO connection options
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => $ssl_ca,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch results as associative arrays
    ];

    try {
        // Establish PDO connection
        $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, $options);
        echo "<script>alert('Connection successful!');</script>";
    } catch (PDOException $e) {
        die("<script>alert('Connection failed: " . $e->getMessage() . "');</script>");
    }

    // Validate form inputs
    $input_username = $_POST['username'] ?? '';
    $input_password = $_POST['password'] ?? '';

    if (empty($input_username) || empty($input_password)) {
        echo "<script>alert('Please fill in both username and password.');</script>";
        exit;
    }

    try {
        // Query to check if the username exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $input_username]);

        $user = $stmt->fetch();

        if ($user && password_verify($input_password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on the user's role
            $redirect = ($user['role'] === 'creator') ? "video.php" : "index.php";
            header("Location: $redirect");
            exit;
        } else {
            echo "<script>alert('Invalid username or password.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Database query failed: " . $e->getMessage() . "');</script>";
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
