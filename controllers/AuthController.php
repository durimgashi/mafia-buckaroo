<?php

namespace Controllers;

use \Services\AuthService;


class AuthController extends Controller
{
    public function index() {
        if ($this->isLoggedIn()) {
            $this->render('game/lobby');
        } else {
            $this->render('auth/login');
        }
    }

    public function register_view() {        
        if ($this->isLoggedIn()) {
            $this->render('game/lobby');
        } else {
            $this->render('auth/register');
        }
    }

    public function login_view() {
        $this->render('auth/login');
    }

    public function register_player() {
        $data = file_get_contents("php://input");

        $data = $this->secure_json_input($data);
        $data = json_decode($data, true);

        $this->jsonResponse(AuthService::registerPlayer($data));
    }


    public function login_player() { 
        $data = file_get_contents("php://input");

        $data = $this->secure_json_input($data);
        $data = json_decode($data, true);

        $this->jsonResponse(AuthService::loginPlayer($data));
    }


    public function logout() {
        session_destroy();
        header('Location: /');
    }


    public function json()
    {   
        $this->jsonResponse([
            'test'=> 'What do you want from me tonight???'
        ]);
    }

    public function submit() {
        $this->jsonResponse("MUT ME LULA");
    }

}
