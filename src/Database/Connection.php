<?php

namespace TestAI\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;
    private string $host;
    private string $database;
    private string $username;
    private string $password;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->database = $_ENV['DB_NAME'] ?? 'testai';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $connection = new self();
            self::$instance = $connection->connect();
        }
        
        return self::$instance;
    }

    private function connect(): PDO
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";
            $pdo = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            
            return $pdo;
        } catch (PDOException $e) {
            // This is intentionally problematic - exposing sensitive info
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    // Intentional issue: Method allows SQL injection
    public function rawQuery(string $sql)
    {
        $pdo = self::getInstance();
        return $pdo->query($sql);
    }
}
