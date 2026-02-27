<!-- Routher index.php -->
<!-- Django urls.py  simulaiton-->



<?php
require 'controllers/UserController.php';

$controller = new UserController();

$path = $_SERVER['REQUEST_URI'];

if ($path === '/' || $path === '/home'){
    $controller->home();
} elseif ($path === '/about') {
    $controller->about();
} elseif ($path === '/message') {
    echo "Welcome to the Message Page!";
} else {
    http_response_code(404);
    echo "Page not found.";
}

