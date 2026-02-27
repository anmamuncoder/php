<!-- routers urls.py -->

<?php
require_once __DIR__ . '/controllers/UserController.php';

$controller = new UserController();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $controller->store();
} else {
    $controller->index();
}

?>
