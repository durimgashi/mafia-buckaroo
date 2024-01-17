<?php

$routes = [
    '' => [
        'controller' => 'AuthController',
        'method' => 'index',
        'http_method' => 'GET'
    ],
    'register' => [
        'controller' => 'AuthController',
        'method' => 'register_view',
        'http_method' => 'GET'
    ],
    'register_player' => [
        'controller' => 'AuthController',
        'method' => 'register_player',
        'http_method' => 'POST'
    ],
    'login_player' => [
        'controller' => 'AuthController',
        'method' => 'login_player',
        'http_method' => 'POST'
    ],
    'logout' => [
        'controller' => 'AuthController',
        'method' => 'logout',
        'http_method' => 'GET'
    ],
    'game' => [
        'controller' => 'GameController',
        'method' => 'game',
        'http_method' => 'GET'
    ],
    'start_game' => [
        'controller' => 'GameController',
        'method' => 'startGame',
        'http_method' => 'GET'
    ],
    'pick_player' => [
        'controller' => 'GameController',
        'method' => 'pickPlayer',
        'http_method' => 'POST'
    ],
    'reset' => [
        'controller' => 'GameController',
        'method' => 'resetGame',
        'http_method' => 'GET'
    ],
];

