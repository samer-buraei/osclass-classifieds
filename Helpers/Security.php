<?php

namespace App\Helpers;

class Security
{
    /**
     * Generate CSRF token
     */
    public static function generateCSRF()
    {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Validate CSRF token
     */
    public static function validateCSRF($token)
    {
        return isset($_SESSION[CSRF_TOKEN_NAME]) && 
               hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }

    /**
     * Sanitize input
     */
    public static function sanitize($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate email
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Hash password
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Generate random string
     */
    public static function randomString($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Encrypt data
     */
    public static function encrypt($data, $key = null)
    {
        $key = $key ?? self::getEncryptionKey();
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * Decrypt data
     */
    public static function decrypt($data, $key = null)
    {
        $key = $key ?? self::getEncryptionKey();
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
    }

    /**
     * Get encryption key
     */
    private static function getEncryptionKey()
    {
        return hash('sha256', $_ENV['APP_KEY'] ?? 'default-encryption-key');
    }

    /**
     * Rate limiting check
     */
    public static function checkRateLimit($key, $maxAttempts = 5, $timeWindow = 300)
    {
        $storageKey = 'rate_limit_' . $key;
        $attempts = $_SESSION[$storageKey] ?? ['count' => 0, 'time' => time()];
        
        // Reset if time window passed
        if (time() - $attempts['time'] > $timeWindow) {
            $attempts = ['count' => 0, 'time' => time()];
        }
        
        $attempts['count']++;
        $_SESSION[$storageKey] = $attempts;
        
        return $attempts['count'] <= $maxAttempts;
    }
}

