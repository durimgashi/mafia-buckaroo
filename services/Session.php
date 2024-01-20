<?php

namespace Services;
use Database\DB;


class Session extends DB {

    public static function getGame() {

        // Cheating prevention
        // Need to hide the roles of the players, so that they are not accessible from the front end
        // Only the current player's role will be returned, and the role of the other maifia if the player is the mafia

        $game = $_SESSION['game'];
        $players = $game['players'];

        $my_role = self::getMyRole();

        $filtered_players = [];

        if ($my_role === ROLES['MAFIA']) {
            foreach ($players AS $player) {

                if ($player['role'] === ROLES['MAFIA'] ) {
                    $sanitized = [
                        "player_id" => $player['player_id'],
                        "is_bot" => $player['is_bot'],
                        "full_name" => $player['full_name'],
                        "status" => $player['status'],
                        "role" => $player['role'],
                        "role_id" => $player['role_id']
                    ];
                } else {
                    $sanitized = [
                        "player_id" => $player['player_id'],
                        "is_bot" => $player['is_bot'],
                        "full_name" => $player['full_name'],
                        "status" => $player['status'],
                    ];
                }

                $filtered_players[] = $sanitized; 
            }
        } else {
            foreach ($players AS $player) { 
                if ($player['is_bot'] == '1') {
                    $sanitized = [
                        "player_id" => $player['player_id'],
                        "is_bot" => $player['is_bot'],
                        "full_name" => $player['full_name'],
                        "status" => $player['status'],
                    ];
                } else {
                    $sanitized = [
                        "player_id" => $player['player_id'],
                        "is_bot" => $player['is_bot'],
                        "full_name" => $player['full_name'],
                        "status" => $player['status'],
                        "role" => $player['role'],
                        "role_id" => $player['role_id']
                    ];
                }

                $filtered_players[] = $sanitized;
            }
        }

        $game['players'] = $filtered_players;

        return $game;
    }
    

    public static function getUserId() {
        return $_SESSION['user']['user_id'];
    }

    public static function getMyRole() {
        return $_SESSION['user']['role'];
    }

    public static function setGameOver($winners = '') {
        $_SESSION['game']['game_over'] = true;
        $_SESSION['winners'] = $winners;

        DB::raw("UPDATE games SET end_date = NOW(), winners = ? WHERE id = ?", $winners, self::getGameId());

        self::setSecondMessage('Game Over');
    }

    public static function isOngoingGame() {
        return $_SESSION['ongoing_game'];
    }


    public static function getGameId() {
        return $_SESSION['game']['game_id'];
    }

    public static function setGameId($id) {
        $_SESSION['game']['game_id'] = $id;
    }

    public static function getPlayers() {
        return $_SESSION['game']['players'];
    }

    public static function setPlayers($players) {
        $_SESSION['game']['players'] = $players;
    }

    public static function resetProgressMessages() {
        $_SESSION['game']['progress_messages'] = [];
    }

    public static function setProgressInfo($message) {
        $_SESSION['game']['progress_messages'][] = $message;
    }

    public static function setSecondMessage($message) {
        $_SESSION['game']['second_message'] = $message;
    }

    public static function getCycle() {
        return $_SESSION['game']['cycle'];
    }

    public static function toggleGameCycle() {
        if ($_SESSION['game']['cycle'] == "night")
            $_SESSION['game']['cycle'] = "day";
        else
            $_SESSION['game']['cycle'] = "night";

        $_SESSION['game']['round']++;
    }

    public static function setGameSession($new_game_id, $players) {
        $my_role = [];

        foreach ($players AS $player) {
            if($player['is_bot'] == "0") {
                $my_role['role'] = $player['role'];
            }
        }

        $_SESSION['ongoing_game'] = true;
        $_SESSION['winners'] = '';
        $_SESSION['user']['role'] = $my_role['role'];
        $_SESSION['game'] = [
            'game_id' => $new_game_id,
            'game_over' => false,
            'cycle' => 'night',
            'round' => 1,
            'players' => $players,
            'second_message' => '',
            'progress_messages' => [],
            'fellow_mafia' => []
        ];

        if ($my_role['role'] === 'Mafia') {
            $_SESSION['game']['fellow_mafia'] = array_values(array_filter($_SESSION['game']['players'], function ($player) {
                return $player['role'] === 'Mafia';
            }));
        }
    }

    public static function resetGameSession() {
        DB::raw('DELETE FROM games WHERE winners IS NULL AND end_date IS NULL');
        $_SESSION['ongoing_game'] = false;
        $_SESSION['game'] = [];
    }

}