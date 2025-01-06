<?php include('includes/header.php'); ?>
<?php
// Include database configuration
include('db_config.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.'); window.location.href='signup.php';</script>";
        exit;
    }
    // Prepare the SQL query to insert the new user
    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $password, $role);

    // Execute the query and check for errors
    if ($stmt->execute()) {
        echo "<script>alert('Sign up successful!'); window.location.href='signin.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='signup.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<main>
    <h1>Sign Up</h1>
    <form action="signup.php" method="post">
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

