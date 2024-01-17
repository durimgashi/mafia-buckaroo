<?php include __DIR__ . "/../includes/header.php" ?>

<div class="form">
    <h2>Login</h2>

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <a href="/register">Register</a>    
    <button type="button" class="submit login_button" >Login</button>
</div>

<script src="/public/js/auth.js"></script>


<?php include __DIR__ . "/../includes/footer.php" ?>