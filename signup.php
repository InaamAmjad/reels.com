<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    // Database credentials
    $host = "reels-server.mysql.database.azure.com";
    $db_username = "reelsmydb";
    $db_password = "Nomi4321";
    $dbname = "reels_db";
    $port = 3306;
    $ssl_ca = '/home/site/ssl_certs/DigiCertGlobalRootCA.crt.pem';

    // PDO options
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => $ssl_ca,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        // Establish PDO connection
        $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $db_username, $db_password, $options);

        // Sanitize and validate input
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

        // Check if required fields are not empty
        if (empty($username) || empty($password) || empty($confirm_password) || empty($role)) {
            echo "<script>alert('All fields are required.');</script>";
            exit;
        }

        // Validate passwords match
        if ($password !== $confirm_password) {
            echo "<script>alert('Passwords do not match.');</script>";
            exit;
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the username already exists
        $check_query = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $pdo->prepare($check_query);
        $stmt->execute(['username' => $username]);
        if ($stmt->fetchColumn() > 0) {
            echo "<script>alert('Username already exists. Please choose another.');</script>";
            exit;
        }

        // Insert user into database
        $insert_query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
        $stmt = $pdo->prepare($insert_query);
        $stmt->execute([
            'username' => $username,
            'password' => $hashed_password,
            'role' => $role,
        ]);

        echo "<script>alert('Sign up successful! Redirecting to login page...'); window.location.href='login.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "');</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid request method. Please submit the form.');</script>";
    exit;
}

<?php include('includes/header.php'); ?>

<main>
    <h1>Sign Up</h1>
    <form action="signup_action.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <label for="role">Role:</label>
        <select id="role" name="role">
            <option value="creator">Creator</option>
            <option value="consumer">Consumer</option>
        </select>

        <button type="submit">Sign Up</button>
    </form>
</main>

<?php include('includes/footer.php'); ?>
 
