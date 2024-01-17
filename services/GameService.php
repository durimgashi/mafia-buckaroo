<?php

namespace Services;

use Utils\DatabaseConnection;

class GameService extends DatabaseConnection {

    public static function resetGame() {
        self::resetGameSession();
    }

    public static function getThirdMessage(): string {
        return $_SESSION['game']['third_message'] . "<br>";
    }

    public static function pickPlayer($data) {
        $action = $data['action'];
        $player_id = $data['player_id'];

        $_SESSION['game']['progress_messages'] = [];

        if (self::isNight()) {
            $players_before_kill = self::getActivePlayers();

            if ($action == 'save') {
                self::handleMafiaKill(null);
                self::handleDoctorSave($players_before_kill, $player_id);
            } else if ($action == 'investigate') {
                self::handleInvestigation($player_id);
                self::handleMafiaKill(null);
            } else if ($action == 'kill') {
                self::handleMafiaKill($player_id);
                if (!self::isDoctorDead())
                    self::handleDoctorSave($players_before_kill, $player_id);
            }
        } else {
            if ($action == 'vote') {
                self::handleTownVote($player_id);
            }
        }

        return self::initRound();
    }

    public static function handleDoctorSave($activePlayers, $player_id = null): bool {
        if (empty($activePlayers))
            return false;

        if ($player_id === null) {
            $randomIndex = array_rand($activePlayers);
            $playerToSave = $activePlayers[$randomIndex];
        } else {
            $playerToSave = null;
            foreach ($activePlayers as $player) {
                if ($player['player_id'] == $player_id) {
                    $playerToSave = $player;
                    break;
                }
            }

            if ($playerToSave === null)
                return false;
        }

        $savedPlayerId = $playerToSave['player_id'];
        $player = self::getPlayerById($savedPlayerId);

        self::savePlayer($savedPlayerId);
        self::setProgressInfo("The doctor has saved <strong><u>" . $player['full_name'] . "</u></strong>");

        return true;
    }

    public static function handleInvestigation($player_id) {
        $investigate = self::investigatePlayer($player_id);
        $player = self::getPlayerById($player_id);

        if ($investigate) {
            self::setProgressInfo($player['full_name'] . " <strong><u>is</u></strong> the mafia.");
        } else {
            self::setProgressInfo($player['full_name'] . " <strong><u>is not</u></strong> the mafia.");
        }
    }

    public static function handleMafiaKill($player_id) {
        $vote = self::mafiaVoteToKill($player_id);

        if ($vote) {
            if ($player_id == null)
                $player_id = $vote;

            self::removePlayer($player_id);
            $removedPlayer = self::getPlayerById($player_id);

            self::setProgressInfo(" The mafia chose to kill <strong><u>" . $removedPlayer['full_name'] . "</u></strong> who was a " . self::getPlayerRole($player_id) . ".");
        } else {
            self::setProgressInfo(' No one was killed by the mafia.');
        }
    }

    public static function handleTownVote($player_id) {
        $vote = self::votePlayer($player_id);

        if ($vote) {
            self::removePlayer($player_id);
            $jailedPlayer = self::getPlayerById($player_id);
            self::setProgressInfo("The town has jailed <strong><u>" . $jailedPlayer['full_name'] . "</u></strong> who was a " . self::getPlayerRole($player_id) . ".");
        } else {
            self::setProgressInfo('No one was jailed');
        }
    }

    public static function isDoctorDead(): bool {
        $allPlayers = $_SESSION['game']['players'];

        foreach ($allPlayers as $player) {
            if ($player['role'] === 'Doctor' && $player['status'] === 'alive') {
                return false;
            }
        }

        return true;
    }

    private static function setProgressInfo($message) {
        $_SESSION['game']['progress_messages'][] = $message;
    }

    private static function setSecondMessage($message) {
        $_SESSION['game']['second_message'] = $message;
    }

    private static function investigatePlayer($player_id): bool {
        $allPlayers = self::getActivePlayers();

        foreach ($allPlayers as $player) {
            if ($player['player_id'] === $player_id) {
                if ($player['role'] === 'Mafia' && $player['status'] === 'alive') {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    private static function mafiaVoteToKill($player_id = null) {
        $allPlayers = self::getKillList();
        $currentUserId = $_SESSION['user']['user_id'];

        $votes = [];

        if ($player_id !== null) {
            $votes[] = $player_id;

            $activeMafiaPlayers = array_filter($allPlayers, function ($player) use ($currentUserId) {
                return $player['role'] === 'Mafia' && $player['status'] === 'alive' && $player['player_id'] !== $currentUserId;
            });

            foreach ($activeMafiaPlayers as $mafiaPlayer) {
                $nonMafiaPlayers = array_filter($allPlayers, function ($player) {
                    return $player['role'] !== 'Mafia' && $player['status'] === 'alive';
                });

                $randomIndex = array_rand($nonMafiaPlayers);
                $votes[] = $nonMafiaPlayers[$randomIndex]['player_id'];
                break;
            }
        } else {
            if (rand(1, 100) < 70) {
                $randomIndex = array_rand($allPlayers);
                $votes[] = $allPlayers[$randomIndex]['player_id'];
            }
        }

        $voteCounts = array_count_values($votes);

        if (count($voteCounts) == 0) {
            return false;
        }

        $mostVotedPlayers = array_keys($voteCounts, max($voteCounts));

        if (count($mostVotedPlayers) === 1) {
            return $mostVotedPlayers[0];
        } else {
            return false;
        }
    }


    private static function votePlayer($player_id) {
        $active_players = self::getActivePlayers();

        if (empty($active_players))
            return false;

        $votes = [$player_id];
        foreach ($active_players AS $player) {
            if ($player['player_id'] === $_SESSION['user']['user_id'])
                continue;

            $randomIndex = array_rand($active_players);
            $votes[] = $active_players[$randomIndex]['player_id'];
        }

        $voteCounts = array_count_values($votes);
        $mostVotedNumbers = array_keys($voteCounts, max($voteCounts));

        if (count($mostVotedNumbers) === 1) {
            return $mostVotedNumbers[0];
        } else {
            return false;
        }
    }

    private static function getPlayerById($player_id) {
        $players = $_SESSION['game']['players'];

        foreach ($players as $player) {
            if ($player['player_id'] == $player_id) {
                return $player;
            }
        }
        return null;
    }

    private static function getPlayerRole($player_id) {
        $allPlayers = $_SESSION['game']['players'];

        foreach ($allPlayers as $player) {
            if ($player['player_id'] == $player_id) {
                return $player['role'];
            }
        }

        return null;
    }

    private static function updatePlayerStatus($player_id, $status) {
        $players = $_SESSION['game']['players'];

        foreach ($players as &$player) {
            if ($player['player_id'] == $player_id) {
                $player['status'] = $status;
                $_SESSION['game']['players'] = $players;
            }
        }
    }

    private static function removePlayer($player_id) {
        self::updatePlayerStatus($player_id, 'dead');
    }

    private static function savePlayer($player_id) {
        self::updatePlayerStatus($player_id, 'alive');
    }

    private static function getActivePlayers(): array {
        return array_values(array_filter($_SESSION['game']['players'], function ($player) {
            return isset($player['status']) && $player['status'] === 'alive';
        }));
    }

    private static function getKillList(): array {
        return array_values(array_filter($_SESSION['game']['players'], function ($player) {
            return isset($player['status']) && $player['status'] === 'alive' && $player['role'] != 'Mafia';
        }));
    }

    private static function getAllPlayers(): array {
        return $_SESSION['game']['players'];
    }

    public static function isPlayerDead($player_id): bool {
        $players = $_SESSION['game']['players'];

        foreach ($players as $player) {
            if ($player['player_id'] == $player_id) {
                return $player['status'] === 'dead';
            }
        }

        return false;
    }

    public static function initRound($new_game = false) {
        if ($_SESSION['ongoing_game'] && !$new_game) {
            self::toggleGameCycle();
        } else {
            $players = self::generateRoles();

            if($players) {
                self::setGameSession($players);
            }
        }

        $my_role = $_SESSION['user']['role'];

        if(self::isNight()) {
            switch($my_role) {
                case ROLES['TOWNSPERSON']:
                    self::setSecondMessage('Keep sleeping...');
                    break;
                case ROLES['DOCTOR']:
                    self::setSecondMessage('Save someone');
                    break;
                case ROLES['COP']:
                    self::setSecondMessage('Investigate someone');
                    break;
                case ROLES['MAFIA']:
                    self::setSecondMessage('Vote to kill someone');
                    break;
            }
        } else {
            self::setSecondMessage('Cast your vote');
        }

        if (self::isTownOutnumbered()) {
            $_SESSION['game']['game_over'] = true;
            $_SESSION['winners'] = 'Mafia';

            self::setSecondMessage('Game Over');
            self::setProgressInfo(' Mafia has outnumbered the townspeople');
            self::setProgressInfo(' Mafia Wins');
        }

        if (self::isMafiaOutnumbered()) {
            $_SESSION['game']['game_over'] = true;
            $_SESSION['winners'] = 'The Town';

            self::setSecondMessage('Game Over');
            self::setProgressInfo(' The Town has outnumbered the mafia');
            self::setProgressInfo(' The Town Wins');
        }

        if (self::isPlayerDead($_SESSION['user']['user_id'])) {
            $_SESSION['game']['game_over'] = true;

            self::setSecondMessage('Game Over');
            self::setProgressInfo('You have been killed!');
        }

        return $_SESSION['game'];
    }


    private static function generateRoles() {
        $query = 
            "WITH RECURSIVE Numbers AS (
                SELECT 1 AS n UNION ALL SELECT n + 1 FROM Numbers WHERE n < (SELECT MAX(num_in_10_player_game) FROM roles)
            ), 
            RolesQuery AS (
                SELECT r.id AS role_id, r.role_name AS role, ROW_NUMBER() OVER () AS row_num FROM roles r CROSS JOIN Numbers WHERE Numbers.n <= r.num_in_10_player_game
            ), 
            PlayersQuery AS (
                SELECT is_bot, id AS player_id, full_name, ROW_NUMBER() OVER () AS row_num FROM (
                    SELECT * FROM players WHERE `is_bot` = 1 OR id = ? ORDER BY RAND() LIMIT 10
                ) AS sub
            )
            SELECT 
                RolesQuery.role_id, 
                PlayersQuery.is_bot, 
                PlayersQuery.player_id, 
                PlayersQuery.full_name, 
                RolesQuery.role,
                'alive' AS status
            FROM RolesQuery JOIN PlayersQuery ON RolesQuery.row_num = PlayersQuery.row_num;";

        return DatabaseConnection::fetchMany($query, $_SESSION['user']['user_id']); 
    }

    private static function countPlayers(): array {
        $mafiaCount = 0;
        $otherCount = 0;

        $players = self::getAllPlayers();

        foreach ($players as $player) {
            if ($player['status'] === 'alive') {
                if ($player['role'] === 'Mafia') {
                    $mafiaCount++;
                } else {
                    $otherCount++;
                }
            }
        }

        return [$mafiaCount, $otherCount];
    }

    private static function isTownOutnumbered(): bool {
       [$mafiaCount, $otherCount] = self::countPlayers();
        return $otherCount < $mafiaCount;
    }

    private static function isMafiaOutnumbered(): bool {
        [$mafiaCount, $otherCount] = self::countPlayers();
        return $otherCount > $mafiaCount && $mafiaCount == 1;
    }


    private static function isNight(): bool {
        return $_SESSION['game']['cycle'] == 'night';
    }

    private static function isDay(): bool {
        return $_SESSION['game']['cycle'] == 'day';
    }

    private static function toggleGameCycle() {
        if ($_SESSION['game']['cycle'] == "night")
            $_SESSION['game']['cycle'] = "day";
        else 
            $_SESSION['game']['cycle'] = "night";

        $_SESSION['game']['round']++;
    }

    private static function setGameSession($players) {
        $my_role = [];

        foreach ($players AS $player) {
            if($player['is_bot'] == "0") {
                $my_role['role'] = $player['role'];
            }
        }

        $_SESSION['ongoing_game'] = true;
        $_SESSION['winners'] = '';
        $_SESSION['user']['role'] = $my_role['role'];
        $_SESSION['game']['game_over'] = false;
        $_SESSION['game']['cycle'] = 'day';
        $_SESSION['game']['round'] = 1;
        $_SESSION['game']['players'] = $players;
        $_SESSION['game']['second_message'] = '';
        $_SESSION['game']['progress_messages'] = [];

        if ($my_role['role'] === 'Mafia') {
            $_SESSION['game']['fellow_mafia'] = array_values(array_filter($_SESSION['game']['players'], function ($player) {
                return $player['role'] === 'Mafia';
            }));
        } else {
            $_SESSION['game']['fellow_mafia'] = [];
        }
    }

    public static function resetGameSession() {
        $_SESSION['ongoing_game'] = false;
        $_SESSION['game'] = [];
    }
}