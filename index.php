<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/utils/constants.php';
include __DIR__ . '/routes/Router.php';