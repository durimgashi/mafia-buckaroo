<?php

namespace Services;

use Utils\DatabaseConnection;

class AuthService extends DatabaseConnection {
    public static function registerPlayer($data) {
        $query = "SELECT COUNT(*) AS count FROM players WHERE username = ?";
        $exists = DatabaseConnection::fetchOne($query, $data['username']);

        $exists = (boolean)$exists['count'];

        if(!$exists) {
            $fullName = $data['fullName'];
            $username = $data['username'];

            $valid_password = self::validatePassword($data['password']);

            if ($valid_password != null) {
                return [
                    'error' => true,
                    'message' => $valid_password
                ];
            }

            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            $insert_query = "INSERT INTO players (full_name, username, password) VALUES (?, ?, ?);";

            $created = DatabaseConnection::create($insert_query, $fullName, $username, $hashedPassword);

            if($created) {
                $user_id = DatabaseConnection::fetchOne("SELECT id AS user_id FROM players WHERE username = ?", $username)['user_id'];

                self::setAuthSessions($user_id, $username, $fullName);

                return [
                    'error' => false,
                    'message' => "Player has been created"
                ];
            } else {
                return [
                    'error' => true,
                    'message' => "Failed to create user"
                ];
            }

        } else {
            return [
                'error' => true,
                'message' => "This username is not available"
            ];
        }
    }

    public static function loginPlayer($data) {
        $username = $data['username'];
        $password = $data['password'];
        
        // Can't login in as the default players, the Star Wars characters
        $query = "SELECT * FROM players WHERE username = ? AND is_bot = 0";
        $user_data = DatabaseConnection::fetchOne($query, $username);

        if ($user_data) {
            if (password_verify($password, $user_data['password'])) {
                self::setAuthSessions($user_data['id'], $username, $user_data['full_name']);
    
                return [
                    'error' => false,
                    'message' => "Login successful"
                ];
            } else {
                return [
                    'error' => true,
                    'message' => "Incorrect password"
                ];
            }
        } else {
            return [
                'error' => true,
                'message' => "User not found"
            ];
        }
    }

    private static function validatePassword($password): ?string {
        if (strlen($password) < 8) {
            return "Password must be at least 8 characters long.";
        }

        if (!preg_match('/\d/', $password)) {
            return "Password must contain at least one number.";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return "Password must contain at least one uppercase letter.";
        }

        return null;
    }

    public static function setAuthSessions($user_id, $fullName, $username) {
        session_regenerate_id(true);

        $_SESSION['user']['user_id'] = $user_id;
        $_SESSION['user']['full_name'] = $fullName;
        $_SESSION['user']['username'] = $username;
        $_SESSION['ongoing_game'] = false;
        $_SESSION['game'] = [];
    }

}