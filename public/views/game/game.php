<?php include __DIR__ . "/../includes/header.php" ?>
<link rel="stylesheet" href="/public/styles/game.css">


<style>
    body .is_mafia {
        background: red !important;
    }

    nav {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        /*background: #000;*/
        padding: 15px;
    }
</style>



<nav class="navigation-bar">
    <div>
        <div class="round"></div>

    </div>

    <div class="d-flex">
        <div class="role"></div>
    </div>

    <div>
        <a class="reset-game" href="/reset">Reset</a>
    </div>
</nav>


<!---->
<!--<h1 class="first-message"></h1>-->
<!---->
<!--<div class="circle-container"></div>-->
<!---->
<!--<div class="space-between">-->
<!--    <br>-->
<!--    <div class="second-message"></div>-->
<!--    <ul class="progress_messages"></ul>-->
<!--</div>-->


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
