<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    // Database credentials
    // Set up SSL parameters
       include('db_config.php');

    // Sanitize and validate input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    // Additional validation for username
    if (empty($username) || strlen($username) < 5 || strlen($username) > 20 || !preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        echo "<script>alert('Username must be between 5-20 characters and can only contain letters, numbers, and underscores.'); window.location.href='signup.php';</script>";
        exit;
    }

    // Validate password length
    if (empty($password) || strlen($password) < 8) {
        echo "<script>alert('Password must be at least 8 characters long.'); window.location.href='signup.php';</script>";
        exit;
    }

    // Check if required fields are not empty
    if (empty($username) || empty($password) || empty($confirm_password) || empty($role)) {
        echo "<script>alert('All fields are required.'); window.location.href='signup.php';</script>";
        exit;
    }

    // Validate passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.location.href='signup.php';</script>";
        exit;
    }

    // Check if the username already exists
    $check_query = "SELECT COUNT(*) FROM reels_db.users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $username);  // Bind the username as a string
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    
    if ($user_count > 0) {
        echo "<script>alert('Username already exists. Please choose another.'); window.location.href='signup.php';</script>";
        exit;
    }

    // Insert user into database
    $insert_query = "INSERT INTO reels_db.users (username, password, role) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "sss", $username, $password, $role);  // Bind username, password, and role as strings
    mysqli_stmt_execute($stmt);

    // Redirect after successful signup
    echo "<script>alert('Sign up successful! Redirecting to login page...'); window.location.href='index.php';</script>";
    exit;
}
?>

<?php include('includes/header.php'); ?>


<main>
    <h1>Sign Up</h1>
    <form action="signup.php" method="post" autocomplete="off">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required autocomplete="new-username">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required autocomplete="new-password">

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">

        <label for="role">Role:</label>
        <select id="role" name="role">
            <option value="creator">Creator</option>
            <option value="consumer">Consumer</option>
        </select>

        <button type="submit" name="signup">Sign Up</button>
    </form>
</main>

<?php include('includes/footer.php'); ?>
        
