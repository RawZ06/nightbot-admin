<?php
declare(strict_types=1);

namespace App\Executor;

class DenoExecutor
{
    private const TIMEOUT = 5; // seconds

    public function execute(string $code, array $params): string
    {
        // Préparer le wrapper JavaScript qui sera exécuté par Deno
        $wrapper = $this->createWrapper($code, $params);

        // Créer un fichier temporaire pour le code
        $tempFile = tempnam(sys_get_temp_dir(), 'deno_');
        file_put_contents($tempFile, $wrapper);

        try {
            // Exécuter Deno avec permission réseau pour fetch HTTPS
            $command = sprintf(
                '/usr/local/bin/deno run --no-prompt --allow-net %s 2>&1',
                escapeshellarg($tempFile)
            );

            $descriptorspec = [
                0 => ['pipe', 'r'],  // stdin
                1 => ['pipe', 'w'],  // stdout
                2 => ['pipe', 'w']   // stderr
            ];

            $process = proc_open($command, $descriptorspec, $pipes);

            if (!is_resource($process)) {
                return 'Error: Command execution failed';
            }

            // Fermer stdin
            fclose($pipes[0]);

            // Définir un timeout sur stdout et stderr
            stream_set_timeout($pipes[1], self::TIMEOUT);
            stream_set_timeout($pipes[2], self::TIMEOUT);

            // Lire la sortie
            $output = stream_get_contents($pipes[1]);
            $errors = stream_get_contents($pipes[2]);

            fclose($pipes[1]);
            fclose($pipes[2]);

            // Terminer le processus
            proc_terminate($process);
            proc_close($process);

            if ($output === false) {
                return 'Error: Timeout or execution failed';
            }

            return trim($output ?: $errors);
        } finally {
            // Nettoyer le fichier temporaire
            @unlink($tempFile);
        }
    }

    private function createWrapper(string $userCode, array $params): string
    {
        $paramsJson = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $userCodeJson = json_encode($userCode, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Charger toutes les quotes disponibles
        $quotesData = $this->loadAllQuotes();
        $quotesJson = json_encode($quotesData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return <<<JS
// Wrapper Deno pour exécuter le code utilisateur de manière sécurisée
(async function() {
    try {
        // Variables disponibles pour l'utilisateur
        const args = {$paramsJson};

        // Données des quotes chargées depuis la DB
        const quotesData = {$quotesJson};

        // Classe Quote statique (style Java avec chaînage)
        class QuoteClass {
            // Spécifier le nom de la quote
            // Usage: Quote.name('death_messages') => QuoteBuilder
            static name(quoteName) {
                return new QuoteBuilder(quoteName, quotesData[quoteName] || []);
            }
        }

        // Builder de quote (retourné par name())
        class QuoteBuilder {
            constructor(name, items) {
                this.quoteName = name;
                this.items = items;
            }

            // Charger un élément spécifique
            // Usage: Quote.name('death_messages').load(1) => string (index)
            // Usage: Quote.name('death_messages').load('enclume') => string (recherche partielle)
            load(selector) {
                if (typeof selector === 'number') {
                    // Charger par index
                    if (selector < 0 || selector >= this.items.length) {
                        return null;
                    }
                    return this.items[selector];
                } else if (typeof selector === 'string') {
                    // Charger par recherche de texte (contient)
                    const found = this.items.find(item => item.toLowerCase().includes(selector.toLowerCase()));
                    return found || null;
                }
                return null;
            }

            // Récupérer tous les éléments
            // Usage: Quote.name('death_messages').all() => string[]
            all() {
                return this.items;
            }

            // Récupérer un élément aléatoire
            // Usage: Quote.name('death_messages').random() => string
            random() {
                if (this.items.length === 0) {
                    return null;
                }
                const randomIndex = Math.floor(Math.random() * this.items.length);
                return this.items[randomIndex];
            }

            // Compter le nombre d'éléments
            // Usage: Quote.name('death_messages').count() => number
            count() {
                return this.items.length;
            }

            // Vérifier si la quote existe
            // Usage: Quote.name('death_messages').exists() => boolean
            exists() {
                return this.items.length > 0;
            }
        }

        // Exposer la classe Quote globalement
        const Quote = QuoteClass;

        // Code utilisateur (échappé en JSON)
        const userCodeString = {$userCodeJson};

        // Créer et exécuter la fonction (support async/await)
        const AsyncFunction = Object.getPrototypeOf(async function(){}).constructor;
        const userFunction = new AsyncFunction('args', 'Quote', userCodeString);
        const result = await userFunction(args, Quote);

        if (result !== undefined) {
            console.log(String(result));
        }
    } catch (error) {
        console.log('Error: ' + error.message);
    }
})();
JS;
    }

    private function loadAllQuotes(): array
    {
        try {
            $db = \App\Database::getConnection();
            $stmt = $db->query('
                SELECT q.name, qi.value
                FROM quotes q
                LEFT JOIN quote_items qi ON q.id = qi.quote_id
                ORDER BY q.name, qi.id
            ');

            $quotes = [];
            while ($row = $stmt->fetch()) {
                $quoteName = $row['name'];
                if (!isset($quotes[$quoteName])) {
                    $quotes[$quoteName] = [];
                }
                if ($row['value'] !== null) {
                    $quotes[$quoteName][] = $row['value'];
                }
            }

            return $quotes;
        } catch (\Exception $e) {
            // Si erreur DB, retourner un tableau vide
            return [];
        }
    }
}
