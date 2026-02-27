<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all()
    {
        $stmt = $this->db->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($name, $email)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email) VALUES (:n, :e)"
        );
        $stmt->execute([
            'n' => $name,
            'e' => $email
        ]);
    }
}
