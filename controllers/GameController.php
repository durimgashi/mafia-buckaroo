<?php

namespace Controllers;

use \Services\AuthService;
use Services\GameService;


class GameController extends Controller
{
    public function __construct() {
        if (!$this->isLoggedIn()) {
            header('Location: /');
            die();
        }
    }

    public function game() {
        $this->render('game/game');
    }

    public function resetGame() {
        GameService::resetGameSession();
        $this->render('game/game');
    }

    public function startGame() {
        $this->jsonResponse(GameService::initRound(true));
    }

    public function pickPlayer() {
        $data = file_get_contents("php://input");

        $data = $this->secure_json_input($data);
        $data = json_decode($data, true);

        $this->jsonResponse(GameService::pickPlayer($data));
    }

    public function gameOver() {
        $this->render('game/game_over');
    }
}
