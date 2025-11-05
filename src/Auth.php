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

        // Vérifier si le mot de passe est déjà hashé
        if (str_starts_with($storedPassword, 'phpass:')) {
            // Vérifier avec le hash
            $hash = str_replace('phpass:', '', $storedPassword);
            return password_verify($password, $hash);
        } else {
            // Mot de passe en clair, vérifier et hasher
            if ($password === $storedPassword) {
                // Hasher et sauvegarder
                self::hashAndSavePassword($username, $password, $users);
                return true;
            }
            return false;
        }
    }

    private static function hashAndSavePassword(string $username, string $password, array $users): void
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $users[$username] = 'phpass:' . $hash;

        // Sauvegarder via Config
        Config::saveUsers($users);
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
