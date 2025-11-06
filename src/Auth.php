<?php
declare(strict_types=1);

namespace App;

class Auth
{
    public static function login(string $username, string $password): bool
    {
        $users = Config::users();

        if (!isset($users[$username])) {
            return false;
        }

        $storedPassword = $users[$username];

        // Vérifier le type de mot de passe
        if (str_starts_with($storedPassword, 'phpass:')) {
            // Mot de passe hashé avec bcrypt
            $hash = str_replace('phpass:', '', $storedPassword);
            return password_verify($password, $hash);
        } elseif (str_starts_with($storedPassword, 'md5:')) {
            // Support MD5 legacy (format: md5:salt:hash)
            $parts = explode(':', $storedPassword, 3);
            if (count($parts) === 3) {
                $salt = $parts[1];
                $expectedHash = $parts[2];
                return md5($salt . $password) === $expectedHash;
            }
        } else {
            // Mot de passe en clair (depuis env, pas encore hashé)
            return $password === $storedPassword;
        }

        return false;
    }

    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION['user']);
    }

    public static function require(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }

    public static function setUser(string $username): void
    {
        self::startSession();
        $_SESSION['user'] = $username;
    }

    public static function logout(): void
    {
        self::startSession();
        session_destroy();
    }

    public static function getUser(): ?string
    {
        self::startSession();
        return $_SESSION['user'] ?? null;
    }

    public static function verifyApiToken(string $token): bool
    {
        $apiToken = Config::apiToken();
        return $apiToken !== null && $apiToken === $token;
    }
}
