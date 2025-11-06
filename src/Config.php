<?php
declare(strict_types=1);

namespace App;

class Config
{
    private static ?array $config = null;

    /**
     * Charge et retourne toute la configuration
     */
    public static function load(): array
    {
        if (self::$config !== null) {
            return self::$config;
        }

        // Charger les utilisateurs (hashés ou depuis env)
        $users = self::loadUsers();

        $config = [
            // Base de données
            'db' => [
                'host' => $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'postgres',
                'port' => (int) ($_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 5432),
                'name' => $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'nightbot',
                'user' => $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'nightbot_user',
                'password' => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '',
            ],

            // Utilisateurs
            'users' => $users,

            // API token (optionnel)
            'api_token' => $_ENV['API_TOKEN'] ?? getenv('API_TOKEN') ?: null,
        ];

        self::$config = $config;
        return self::$config;
    }

    /**
     * Charge les utilisateurs depuis les variables d'environnement
     * Les mots de passe seront hashés automatiquement s'ils sont en clair
     */
    private static function loadUsers(): array
    {
        // Charger depuis les variables d'environnement
        $users = [];
        $adminUsername = $_ENV['ADMIN_USERNAME'] ?? getenv('ADMIN_USERNAME');
        $adminPassword = $_ENV['ADMIN_PASSWORD'] ?? getenv('ADMIN_PASSWORD');

        // Ajouter l'utilisateur principal depuis l'env
        if ($adminUsername && $adminPassword) {
            $users[$adminUsername] = $adminPassword;
        }

        // Ajouter des utilisateurs supplémentaires ici (seront auto-hashés)
        $users['papy'] = 'papy';

        // Auto-hasher tous les mots de passe en clair
        $needsSave = false;
        foreach ($users as $username => $password) {
            if (!str_starts_with($password, 'phpass:') && !str_starts_with($password, 'md5:')) {
                // Mot de passe en clair, le hasher
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $users[$username] = 'phpass:' . $hash;
                $needsSave = true;
            }
        }

        // Sauvegarder si des mots de passe ont été hashés
        if ($needsSave) {
            self::saveUsers($users);
        }

        return $users;
    }

    /**
     * Récupère la configuration de la base de données
     */
    public static function database(): array
    {
        $config = self::load();
        return $config['db'];
    }

    /**
     * Récupère les utilisateurs (env + config.php fusionnés)
     */
    public static function users(): array
    {
        $config = self::load();
        return $config['users'];
    }

    /**
     * Récupère le token API
     */
    public static function apiToken(): ?string
    {
        $config = self::load();
        return $config['api_token'];
    }

    /**
     * Sauvegarde les utilisateurs hashés directement dans le code source
     * Remplace les mots de passe en clair par leur version hashée
     */
    public static function saveUsers(array $users): bool
    {
        // Invalider le cache de config pour forcer le rechargement
        self::$config = null;

        // Relire ce fichier
        $configFile = __FILE__;
        $content = file_get_contents($configFile);

        // Pour chaque utilisateur, remplacer le mot de passe en clair par le hash
        foreach ($users as $username => $hashedPassword) {
            // Échapper les caractères spéciaux pour la regex
            $escapedUsername = preg_quote($username, '/');

            // Pattern pour trouver: 'username' => 'plaintext_or_hash',
            // On remplace seulement si ce n'est pas déjà un hash (ne commence pas par phpass:)
            $pattern = "/(['\"]" . $escapedUsername . "['\"]\\s*=>\\s*['\"])(?!phpass:)([^'\"]+)(['\"])/";
            $replacement = '$1' . $hashedPassword . '$3';

            $content = preg_replace($pattern, $replacement, $content);
        }

        return file_put_contents($configFile, $content) !== false;
    }
}
