<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TestAI\Models\User;
use TestAI\Database\Connection;

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Set content type
header('Content-Type: application/json');

// Get HTTP method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Basic routing
switch ($path) {
    case '/users':
        handleUsersEndpoint($method);
        break;
    case '/health':
        handleHealthCheck();
        break;
    default:
        if (preg_match('/^\/users\/(\d+)$/', $path, $matches)) {
            handleUserByIdEndpoint($method, (int)$matches[1]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
        }
        break;
}

function handleUsersEndpoint(string $method): void
{
    switch ($method) {
        case 'GET':
            getAllUsers();
            break;
        case 'POST':
            createUser();
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

function handleUserByIdEndpoint(string $method, int $id): void
{
    switch ($method) {
        case 'GET':
            getUserById($id);
            break;
        case 'PUT':
            updateUser($id);
            break;
        case 'DELETE':
            deleteUser($id);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

function getAllUsers(): void
{
    try {
        $pdo = Connection::getInstance();
        
        // Intentional issue: No pagination, could return huge datasets
        $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll();
        
        // Intentional issue: Exposing all user data including passwords
        echo json_encode($users);
    } catch (Exception $e) {
        http_response_code(500);
        // Intentional issue: Exposing internal error details
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function getUserById(int $id): void
{
    try {
        $user = User::findById($id);
        
        if ($user) {
            // Intentional issue: Exposing password in response
            echo json_encode($user->toArray());
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

function createUser(): void
{
    // Intentional issue: No input validation
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }
    
    try {
        $user = new User(
            $input['username'] ?? '',
            $input['email'] ?? '',
            $input['password'] ?? ''
        );
        
        if ($user->save()) {
            http_response_code(201);
            echo json_encode($user->toArray());
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to create user']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function updateUser(int $id): void
{
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }
    
    try {
        $user = User::findById($id);
        
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        // Intentional issue: No validation before setting values
        if (isset($input['username'])) {
            $user->setUsername($input['username']);
        }
        
        if (isset($input['email'])) {
            $user->setEmail($input['email']);
        }
        
        if (isset($input['password'])) {
            $user->setPassword($input['password']);
        }
        
        if ($user->save()) {
            echo json_encode($user->toArray());
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to update user']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

function deleteUser(int $id): void
{
    try {
        $user = User::findById($id);
        
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }
        
        if ($user->delete()) {
            http_response_code(204);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to delete user']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

function handleHealthCheck(): void
{
    try {
        // Intentional issue: Database connection test in health check could be expensive
        $pdo = Connection::getInstance();
        $stmt = $pdo->query("SELECT 1");
        
        echo json_encode([
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'database' => 'connected'
        ]);
    } catch (Exception $e) {
        http_response_code(503);
        echo json_encode([
            'status' => 'unhealthy',
            'error' => $e->getMessage()
        ]);
    }
}
