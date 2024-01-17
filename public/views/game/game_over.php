<?php include __DIR__ . "/../includes/header.php" ?>

<style>

    .game-over-box h1 {
        font-size: 40px;
    }

    .game-over-box h1,
    .game-over-box h3,
    .game-over-box h4,
    .game-over-box div {
        text-align: center;
        font-weight: 400;
    }

    #playAgainBtn {
        background: #CAD704;
        color: #000;
        padding: 10px 20px;
        border: none;
        text-decoration: none;
        font-size: 17px;
    }
</style>


<div class="game-over-box">
    <h1>Game Over</h1>
    <h3><?= $_SESSION['winners'] ?> Wins</h3>

    <?php
        $progress_messages = $_SESSION['game']['progress_messages'];
        $total_messages = count($progress_messages);

    ?>
    <h4><?= $progress_messages[$total_messages - 1] ?></h4>


    <div>
        <br>
        <a href="/game" id="playAgainBtn">Play Again</a>
    </div>

</div>




<?php include __DIR__ . "/../includes/footer.php" ?>
