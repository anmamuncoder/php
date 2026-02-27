<?php

/*
 Database connection helper.
 Reads database credentials from environment variables (for development).
*/

class Database
{
    public static function connect()
    {
        $host = getenv('DB_HOST') ?: 'localhost';
        $name = getenv('DB_NAME') ?: 'pdo_project';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $name, $charset);

        try {
            return new PDO(
                $dsn,
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            echo '<h1>Database connection error</h1>';
            echo '<p>Could not connect to the database. Check your DB credentials and that the DB server is running.</p>';
            echo '<ul>';
            echo '<li>Host: ' . htmlspecialchars($host) . '</li>';
            echo '<li>Database: ' . htmlspecialchars($name) . '</li>';
            echo '<li>User: ' . htmlspecialchars($user) . '</li>';
            echo '</ul>';
            echo '<p>Exception message: ' . htmlspecialchars($e->getMessage()) . '</p>';
            exit;
        }
    }
}
