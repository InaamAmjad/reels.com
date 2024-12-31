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
 
