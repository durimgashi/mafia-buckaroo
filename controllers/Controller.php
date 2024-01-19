<?php

namespace Controllers;

use Services\AuthService;

class Controller
{

    protected function sanitise($data) {
        if(is_string($data)) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
        }
        return $data;
    }

    protected function secure_json_input($jsonString) {
        if (is_string($jsonString)) {
            $decodedJson = json_decode($jsonString, true);

            if ($decodedJson !== null) {
                $securedJson = $this->recursive_secure_input($decodedJson);

                $securedJsonString = json_encode($securedJson);

                return $securedJsonString;
            }
        }

        return $jsonString;
    }

    protected function recursive_secure_input($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->recursive_secure_input($value);
            }
        } else if (is_string($data)) {
            $data = $this->sanitise($data);
        }

        return $data;
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user']['username']);
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header("Location: /");
            exit();
        }
    }

    protected function render($viewFile, $data = []) {
        extract($data);
        include_once(__DIR__ . '/../public/views/' . $viewFile . '.php');
    }

    protected function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        die();
    }

    protected function generalError($message) {
//        http_response_code(400);
        return [
            'error' => true,
            'message' => $message
        ];
    }
}
