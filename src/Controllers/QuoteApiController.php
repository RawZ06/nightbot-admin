<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Quote;
use App\Auth;

class QuoteApiController
{
    public function getRandom(array $vars): string
    {
        $quoteName = $vars['name'] ?? '';
        $quote = Quote::findByName($quoteName);

        if ($quote === null) {
            http_response_code(404);
            header('Content-Type: application/json');
            return json_encode(['error' => 'Quote not found']);
        }

        $item = $quote->getRandomItem();

        if ($item === null) {
            http_response_code(404);
            header('Content-Type: application/json');
            return json_encode(['error' => 'No items in quote']);
        }

        header('Content-Type: text/plain; charset=utf-8');
        return $item['value'];
    }

    public function getAll(array $vars): string
    {
        $quoteName = $vars['name'] ?? '';
        $quote = Quote::findByName($quoteName);

        if ($quote === null) {
            http_response_code(404);
            header('Content-Type: application/json');
            return json_encode(['error' => 'Quote not found']);
        }

        $items = $quote->getItems();

        header('Content-Type: application/json');
        return json_encode($items);
    }

    public function add(array $vars): string
    {
        $quoteName = $vars['name'] ?? '';
        $token = $_POST['token'] ?? '';
        $value = $_POST['value'] ?? '';

        $quote = Quote::findByName($quoteName);

        if ($quote === null) {
            http_response_code(404);
            header('Content-Type: application/json');
            return json_encode(['error' => 'Quote not found']);
        }

        if ($quote->token !== $token) {
            http_response_code(403);
            header('Content-Type: application/json');
            return json_encode(['error' => 'Invalid token']);
        }

        if (empty($value)) {
            http_response_code(400);
            header('Content-Type: application/json');
            return json_encode(['error' => 'Value is required']);
        }

        $itemId = $quote->addItem($value);

        header('Content-Type: application/json');
        return json_encode(['success' => true, 'item_id' => $itemId]);
    }

    public function remove(array $vars): string
    {
        $quoteName = $vars['name'] ?? '';
        $token = $_POST['token'] ?? '';
        $itemId = (int) ($_POST['item_id'] ?? 0);

        $quote = Quote::findByName($quoteName);

        if ($quote === null) {
            http_response_code(404);
            header('Content-Type: application/json');
            return json_encode(['error' => 'Quote not found']);
        }

        if ($quote->token !== $token) {
            http_response_code(403);
            header('Content-Type: application/json');
            return json_encode(['error' => 'Invalid token']);
        }

        $quote->removeItem($itemId);

        header('Content-Type: application/json');
        return json_encode(['success' => true]);
    }
}
