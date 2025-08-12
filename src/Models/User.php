<?php

namespace TestAI\Models;

use TestAI\Database\Connection;
use PDO;

class User
{
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private \DateTime $createdAt;

    public function __construct(
        string $username = '',
        string $email = '',
        string $password = '',
        int $id = 0
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = new \DateTime();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    // Setters
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setEmail(string $email): void
    {
        // Add basic email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        // Add basic password validation
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long');
        }
        
        // Still storing in plain text - this will give AI reviewer something to catch
        $this->password = $password;
    }

    public function save(): bool
    {
        $pdo = Connection::getInstance();
        
        if ($this->id > 0) {
            return $this->update($pdo);
        } else {
            return $this->insert($pdo);
        }
    }

    private function insert(PDO $pdo): bool
    {
        try {
            // Intentional issue: Using string concatenation instead of prepared statements
            $sql = "INSERT INTO users (username, email, password, created_at) VALUES ('{$this->username}', '{$this->email}', '{$this->password}', NOW())";
            $result = $pdo->exec($sql);
            
            if ($result) {
                $this->id = (int) $pdo->lastInsertId();
                return true;
            }
            
            return false;
        } catch (\PDOException $e) {
            // Intentional issue: Not handling exceptions properly
            return false;
        }
    }

    private function update(PDO $pdo): bool
    {
        try {
            $sql = "UPDATE users SET username = :username, email = :email, password = :password WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            
            return $stmt->execute([
                ':username' => $this->username,
                ':email' => $this->email,
                ':password' => $this->password,
                ':id' => $this->id
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public static function findById(int $id): ?self
    {
        $pdo = Connection::getInstance();
        
        // This one is properly done for contrast
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        $userData = $stmt->fetch();
        
        if (!$userData) {
            return null;
        }
        
        $user = new self(
            $userData['username'],
            $userData['email'],
            $userData['password'],
            $userData['id']
        );
        
        $user->createdAt = new \DateTime($userData['created_at']);
        
        return $user;
    }

    public static function findByEmail(string $email): ?self
    {
        $pdo = Connection::getInstance();
        
        // Intentional issue: Direct string interpolation
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $pdo->query($sql);
        
        if ($result) {
            $userData = $result->fetch();
            if ($userData) {
                $user = new self(
                    $userData['username'],
                    $userData['email'],
                    $userData['password'],
                    $userData['id']
                );
                $user->createdAt = new \DateTime($userData['created_at']);
                return $user;
            }
        }
        
        return null;
    }

    public function delete(): bool
    {
        if ($this->id <= 0) {
            return false;
        }
        
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        
        return $stmt->execute([':id' => $this->id]);
    }

    // Intentional issue: Method with no return type and unclear purpose
    public function validatePassword($inputPassword)
    {
        // Intentional issue: Plain text password comparison
        if ($this->password == $inputPassword) {
            return true;
        }
        return false;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            // Intentional issue: Exposing password in output
            'password' => $this->password,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get safe user data without sensitive information
     */
    public function toSafeArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Validate user input data
     */
    public static function validateUserData(array $data): array
    {
        $errors = [];
        
        if (empty($data['username'])) {
            $errors[] = 'Username is required';
        } elseif (strlen($data['username']) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (empty($data['password'])) {
            $errors[] = 'Password is required';
        } elseif (strlen($data['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }
        
        return $errors;
    }
}
