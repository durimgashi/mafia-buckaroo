<?php

include 'routes.php';

$request_uri = $_SERVER['REQUEST_URI'];
$request_uri = trim($request_uri, '/');


if (array_key_exists($request_uri, $routes)) {
    $route = $routes[$request_uri];
 
    $allowedMethods = isset($route['http_method']) ? (array)$route['http_method'] : ['GET'];

    if (in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
        $controllerClassName = '\\Controllers\\' . $route['controller'];

        require_once __DIR__ . '/../controllers/' . $route['controller'] . '.php';

        $controller = new $controllerClassName();

        $method = $route['method'];
        $controller->$method();
    } else { 
        header('HTTP/1.0 405 Method Not Allowed');
        echo '405 Method Not Allowed';
    }
} else { 
    header('HTTP/1.0 404 Not Found');
    echo '404 Not Found';
}
