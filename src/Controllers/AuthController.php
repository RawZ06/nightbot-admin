<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Auth;

class AuthController
{
    public function showLogin(array $vars = []): string
    {
        // Si déjà connecté, rediriger
        if (Auth::check()) {
            header('Location: /admin');
            exit;
        }

        // Démarrer la session pour récupérer l'erreur
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        return $this->render('auth/login', ['error' => $error]);
    }

    public function login(array $vars = []): string
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (Auth::login($username, $password)) {
            Auth::setUser($username);
            header('Location: /admin');
            exit;
        } else {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['login_error'] = 'Identifiants invalides';
            header('Location: /login');
            exit;
        }
    }

    public function logout(array $vars = []): string
    {
        Auth::logout();
        header('Location: /login');
        exit;
    }

    private function render(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
