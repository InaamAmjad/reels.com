<?php

session_start();
// Include database configuration
include('db_config.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];  // The plain password entered by the user

    // Prepare the SQL query to retrieve the user's password from the database
    $query = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $input_username);  // Bind username to query
    $stmt->execute();
    $stmt->bind_result($id, $username, $stored_password, $role);
    $stmt->fetch();  // Fetch the result

    // Check if the username exists in the database
    if ($username) {
        // Compare the entered password with the stored hashed password
        if (password_verify($input_password, $stored_password)) {
            // Password is correct, log the user in
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
        // Username doesn't exist
        echo "<script>alert('Username not found.'); window.location.href='signin.php';</script>";
        exit;
    }

    $stmt->close();
    $conn->close();
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
