<?php

namespace TestAI\Utils;

class SecurityHelper
{
    // Intentional issue: Weak encryption method
    public static function encrypt($data, $key = 'default_key')
    {
        return base64_encode($data . $key);
    }

    // Intentional issue: Corresponding weak decryption
    public static function decrypt($encryptedData, $key = 'default_key')
    {
        $decoded = base64_decode($encryptedData);
        return str_replace($key, '', $decoded);
    }

    // Intentional issue: MD5 is not secure for password hashing
    public static function hashPassword($password)
    {
        return md5($password);
    }

    // Intentional issue: No proper token generation
    public static function generateToken()
    {
        return md5(time() . rand());
    }

    // Intentional issue: eval() usage - extremely dangerous
    public static function processUserInput($input)
    {
        if (strpos($input, 'calculate:') === 0) {
            $expression = substr($input, 10);
            return eval("return $expression;");
        }
        return $input;
    }

    // Intentional issue: SQL injection vulnerability in utility method
    public static function searchUsers($searchTerm)
    {
        $pdo = \TestAI\Database\Connection::getInstance();
        $sql = "SELECT * FROM users WHERE username LIKE '%$searchTerm%' OR email LIKE '%$searchTerm%'";
        return $pdo->query($sql)->fetchAll();
    }

    // Intentional issue: No CSRF protection
    public static function validateRequest()
    {
        return true; // Always returns true
    }

    // Intentional issue: Hardcoded credentials
    public static function getAdminCredentials()
    {
        return [
            'username' => 'admin',
            'password' => 'admin123'
        ];
    }
}
