<?php include __DIR__ . "/../includes/header.php" ?>


<div class="form" id="register_form">
    <h2>Register</h2>
    <label for="fullName">Full Name:</label>
    <input type="text" id="fullName" name="fullName" required>

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <a href="/">Login</a>
    <button class="submit register_button" type="button">Register</button>
</div>



<script src="/public/js/auth.js"></script>


<?php include __DIR__ . "/../includes/footer.php" ?>


