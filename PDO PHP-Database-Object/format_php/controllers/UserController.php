<?php
require_once __DIR__ . '/../models/User.php';

class UserController
{
    public function index()
    {
        $user = new User();
        $users = $user->all();

        require __DIR__ . '/../views/users.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name  = trim($_POST['name']);
            $email = trim($_POST['email']);

            if ($name && $email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $user = new User();
                $user->create($name, $email);
            }

            header("Location: /");
            exit;
        }
    }
}
