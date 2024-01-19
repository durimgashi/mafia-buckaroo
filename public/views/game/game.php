<?php include __DIR__ . "/../includes/header.php" ?>
<link rel="stylesheet" href="/public/styles/game.css">

<nav class="navigation-bar">
    <div class="nav-left">
        <div class="round"></div>
        <div class="role"></div>
    </div>

    <div class="nav-right">
        <div class="logged-in-as">User: <?= $_SESSION['user']['username'] ?></div>
        <a class="reset-game" style="margin-right: 5px" href="/">Exit</a>
        <a class="reset-game" href="/game">Reset</a>
    </div>
</nav>

<div class="main-container">
    <div class="players-container">
        <div class="circle-container"></div>
    </div>

    <div class="progress-container">
        <h1 class="first-message" style="color: white"></h1>
        <h2 class="second-message"></h2>
        <hr>

        <div class="progress_messages">

        </div>
    </div>
</div>

<?php include __DIR__ . "/../includes/footer.php" ?>

<script src="/public/js/game.js"></script>
