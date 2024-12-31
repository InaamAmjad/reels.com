<?php include('includes/header.php'); ?>

<main>
    <h1>Sign In</h1>
    <form action="signin_action.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Sign In</button>
    </form>
</main>

<?php include('includes/footer.php'); ?>
 
