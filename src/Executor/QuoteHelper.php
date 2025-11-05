<?php
declare(strict_types=1);

namespace App\Executor;

use App\Models\Quote;

class QuoteHelper
{
    public static function getQuoteFunctions(): string
    {
        return <<<'JS'
// Fonctions utilitaires pour gérer les quotes
const quote = {
    // Récupérer tous les éléments d'une quote
    getAll: function(quoteName) {
        // Cette fonction sera injectée par PHP
        return PHP_QUOTE_GET_ALL(quoteName);
    },

    // Récupérer un élément aléatoire
    getRandom: function(quoteName) {
        return PHP_QUOTE_GET_RANDOM(quoteName);
    },

    // Ajouter un élément (nécessite le token)
    add: function(quoteName, value, token) {
        return PHP_QUOTE_ADD(quoteName, value, token);
    },

    // Supprimer un élément par son ID (nécessite le token)
    remove: function(quoteName, itemId, token) {
        return PHP_QUOTE_REMOVE(quoteName, itemId, token);
    }
};
JS;
    }

    public static function injectQuoteData(string $quoteName): string
    {
        $quote = Quote::findByName($quoteName);

        if (!$quote) {
            return 'null';
        }

        $items = $quote->getItems();
        return json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function getRandomQuote(string $quoteName): ?string
    {
        $quote = Quote::findByName($quoteName);

        if (!$quote) {
            return null;
        }

        $item = $quote->getRandomItem();
        return $item ? $item['value'] : null;
    }
}
