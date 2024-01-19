<?php

namespace Services;

use Database\DB;

class GameService extends DB {
    public static function resetGame() {
        Session::resetGameSession();
    }

    public static function pickPlayer($data) {
        $action = $data['action'];
        $player_id = $data['player_id'];

        Session::resetProgressMessages();

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
                self::handleVillageVote($player_id);
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
        Session::setProgressInfo("The doctor has saved <strong><u>" . $player['full_name'] . "</u></strong>");

        return true;
    }

    public static function handleInvestigation($player_id) {
        $investigate = self::investigatePlayer($player_id);
        $player = self::getPlayerById($player_id);

        if ($investigate) {
            Session::setProgressInfo($player['full_name'] . " <strong><u>is</u></strong> the mafia.");
        } else {
            Session::setProgressInfo($player['full_name'] . " <strong><u>is not</u></strong> the mafia.");
        }
    }

    public static function handleMafiaKill($player_id) {
        $vote = self::mafiaVoteToKill($player_id);

        if ($vote) {
            if ($player_id == null)
                $player_id = $vote;

            self::removePlayer($player_id);
            $removedPlayer = self::getPlayerById($player_id);

            Session::setProgressInfo(" The mafia chose to kill <strong><u>" . $removedPlayer['full_name'] . "</u></strong> who was a " . self::getPlayerRole($player_id) . ".");
        } else {
            Session::setProgressInfo(' No one was killed by the mafia.');
        }
    }

    public static function handleVillageVote($player_id) {
        $vote = self::votePlayer($player_id);

        if ($vote) {
            self::removePlayer($player_id);
            $jailedPlayer = self::getPlayerById($player_id);
            Session::setProgressInfo("The village has jailed <strong><u>" . $jailedPlayer['full_name'] . "</u></strong> who was a " . self::getPlayerRole($player_id) . ".");
        } else {
            Session::setProgressInfo('No one was jailed');
        }
    }

    public static function isDoctorDead(): bool {
        $allPlayers = Session::getPlayers();

        foreach ($allPlayers as $player) {
            if ($player['role'] === 'Doctor' && $player['status'] === 'alive') {
                return false;
            }
        }

        return true;
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
        $currentUserId = Session::getUserId();

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
            if ($player['player_id'] === Session::getUserId())
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
        $players = Session::getPlayers();

        foreach ($players as $player) {
            if ($player['player_id'] == $player_id) {
                return $player;
            }
        }
        return null;
    }

    private static function getPlayerRole($player_id) {
        $allPlayers = Session::getPlayers();

        foreach ($allPlayers as $player) {
            if ($player['player_id'] == $player_id) {
                return $player['role'];
            }
        }

        return null;
    }

    private static function updatePlayerStatus($player_id, $status) {
        $players = Session::getPlayers();

        foreach ($players as &$player) {
            if ($player['player_id'] == $player_id) {
                $player['status'] = $status;
                Session::setPlayers($players);
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
        return array_values(array_filter(Session::getPlayers(), function ($player) {
            return isset($player['status']) && $player['status'] === 'alive';
        }));
    }

    private static function getKillList(): array {
        return array_values(array_filter(Session::getPlayers(), function ($player) {
            return isset($player['status']) && $player['status'] === 'alive' && $player['role'] != 'Mafia';
        }));
    }

    public static function isPlayerDead($player_id): bool {
        $players = Session::getPlayers();

        foreach ($players as $player) {
            if ($player['player_id'] == $player_id) {
                return $player['status'] === 'dead';
            }
        }

        return false;
    }

    public static function initRound($new_game = false) {
        if ($new_game) 
            Session::resetGameSession();

        if (Session::isOngoingGame() && !$new_game) {
            Session::toggleGameCycle();
        } else {
            $new_game_id = DB::create('INSERT INTO games(start_date) VALUES (NOW());');
            $players = self::generateRoles();

            if($players) {
                Session::setGameSession($new_game_id, $players);
                // Not the most efficient way to insert data, but I am short on time
                foreach ($players AS $player) {
                    $insert_query = "INSERT INTO participants(game_id, player_id, role_id) VALUES (?, ?, ?);";
                    DB::create($insert_query, $new_game_id, $player['player_id'], $player['role_id']);
                }
            }
        }

        $my_role = Session::getMyRole();

        if(self::isNight()) {
            switch($my_role) {
                case ROLES['VILLAGER']:
                    Session::setSecondMessage('Keep sleeping...');
                    break;
                case ROLES['DOCTOR']:
                    Session::setSecondMessage('Save someone');
                    break;
                case ROLES['COP']:
                    Session::setSecondMessage('Investigate someone');
                    break;
                case ROLES['MAFIA']:
                    Session::setSecondMessage('Vote to kill someone');
                    break;
            }
        } else {
            Session::setSecondMessage('Cast your vote');
        }

        if (self::areVillagersOutnumbered()) {
            Session::setGameOver('The Mafia');
            Session::setProgressInfo('Mafia has outnumbered the villagers');
            Session::setProgressInfo('Mafia Wins');
        }

        if (self::isAllMafiaDead()) {
            Session::setGameOver('The Village');
            Session::setProgressInfo('All the mafia are dead');
            Session::setProgressInfo('The Village Wins');
        }

        if (self::isPlayerDead(Session::getUserId())) {
            Session::setGameOver('The Mafia');
            Session::setProgressInfo('You have been killed!');
        }

        return Session::getGame();
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

        return DB::fetchMany($query, Session::getUserId());
    }

    private static function countPlayers(): array {
        $mafiaCount = 0;
        $otherCount = 0;

        $players = Session::getPlayers();

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

    private static function areVillagersOutnumbered(): bool {
       [$mafiaCount, $otherCount] = self::countPlayers();
        return $otherCount < $mafiaCount;
    }

    private static function isAllMafiaDead(): bool {
        [$mafiaCount, $otherCount] = self::countPlayers();
        return $mafiaCount === 0;
    }

    private static function isNight(): bool {
        return Session::getCycle() == 'night';
    }
}