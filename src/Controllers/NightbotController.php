<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Command;
use App\Executor\DenoExecutor;

class NightbotController
{
    public function execute(array $vars): string
    {
        $commandName = $vars['name'] ?? '';

        // Récupérer la commande depuis la DB
        $command = Command::findByName($commandName);

        if ($command === null) {
            http_response_code(404);
            return json_encode(['error' => 'Command not found']);
        }

        // Récupérer les paramètres passés dans l'URL
        $queryString = $_SERVER['QUERY_STRING'] ?? '';
        parse_str($queryString, $queryParams);

        // Les paramètres sont dans 'args' (ex: ?args=1,100)
        $argsString = $queryParams['args'] ?? '';
        $args = $argsString ? explode(',', $argsString) : [];

        // Exécuter le code avec Deno
        $executor = new DenoExecutor();
        $result = $executor->execute($command->code, $args);

        // Retourner le résultat en texte brut pour Nightbot
        header('Content-Type: text/plain; charset=utf-8');
        return $result;
    }
}
