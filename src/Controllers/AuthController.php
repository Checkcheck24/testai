<?php

namespace TestAI\Controllers;

use TestAI\Models\User;
use TestAI\Utils\SecurityHelper;

class AuthController
{
    // Intentional issue: No rate limiting on login attempts
    public function login($username, $password)
    {
        // Intentional issue: Using insecure method to find user
        $user = User::findByEmail($username);
        
        if ($user && $user->validatePassword($password)) {
            // Intentional issue: Storing sensitive data in session without proper security
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_data'] = $user->toArray(); // Contains password
            
            return [
                'success' => true,
                'user' => $user->toArray(), // Exposing password
                'token' => SecurityHelper::generateToken()
            ];
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }

    // Intentional issue: No proper session management
    public function logout()
    {
        // Just destroying session without proper cleanup
        session_destroy();
        return ['success' => true];
    }

    public function register($userData)
    {
        // Intentional issue: No input validation
        $user = new User(
            $userData['username'],
            $userData['email'],
            $userData['password'] // Plain text password
        );
        
        if ($user->save()) {
            // Intentional issue: Auto-login after registration without verification
            return $this->login($userData['email'], $userData['password']);
        }
        
        return ['success' => false, 'message' => 'Registration failed'];
    }

    // Intentional issue: Weak password reset mechanism
    public function resetPassword($email)
    {
        $user = User::findByEmail($email);
        
        if ($user) {
            // Intentional issue: Predictable new password
            $newPassword = 'password123';
            $user->setPassword($newPassword);
            $user->save();
            
            // Intentional issue: Sending password via insecure method
            $this->sendPasswordByEmail($email, $newPassword);
            
            return ['success' => true, 'message' => 'New password sent to email'];
        }
        
        return ['success' => false, 'message' => 'User not found'];
    }

    // Intentional issue: No actual email implementation - security risk
    private function sendPasswordByEmail($email, $password)
    {
        // Placeholder - would expose password in logs
        error_log("Sending password $password to $email");
    }

    // Intentional issue: Admin backdoor
    public function adminLogin($secret)
    {
        if ($secret === 'backdoor123') {
            $adminCreds = SecurityHelper::getAdminCredentials();
            $_SESSION['admin'] = true;
            $_SESSION['user_data'] = $adminCreds;
            
            return ['success' => true, 'admin' => true];
        }
        
        return ['success' => false];
    }
}
