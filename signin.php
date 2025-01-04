
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    // Database connection settings
    $host = "reels-server.mysql.database.azure.com";
    $username = "reelsmydb";
    $password = "Nomi4321";
    $dbname = "reels_db";
    $port = 3306;

    $options = [
        PDO::MYSQL_ATTR_SSL_CA => '/home/site/ssl_certs/DigiCertGlobalRootCA.crt.pem',
    ];

    try {
        $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, $options);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Validate inputs
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        if (empty($username) || empty($password)) {
            echo "<script>alert('Please fill in both username and password.');</script>";
            exit;
        }

        // Prepare and execute query
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validate user and password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            $redirect = ($user['role'] == 'creator') ? "video.php" : "index.php";
            header("Location: $redirect");
            exit;
        } else {
             echo "<script>alert('Invalid username" . $user['username'] ." or password " . $user['password']."');</script>";
            echo "<script>alert('Invalid username or password.');</script>";
        }
    } catch (PDOException $e) {
        die("<script>alert('Connection or query failed: " . $e->getMessage() . "');</script>");
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
