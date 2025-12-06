<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Quote;
use App\Auth;

class QuoteController
{
    public function index(array $vars = []): string
    {
        Auth::require();
        $quotes = Quote::all();
        return $this->render('admin/quotes', ['quotes' => $quotes, 'page' => 'quotes']);
    }

    public function create(array $vars = []): string
    {
        Auth::require();
        return $this->render('admin/quote_edit', ['quote' => null, 'page' => 'quotes']);
    }

    public function edit(array $vars): string
    {
        Auth::require();
        $id = (int) $vars['id'];
        $quote = Quote::find($id);

        if ($quote === null) {
            http_response_code(404);
            return '404 - Quote not found';
        }

        $items = $quote->getItems();
        return $this->render('admin/quote_edit', ['quote' => $quote, 'items' => $items, 'page' => 'quotes']);
    }

    public function store(array $vars = []): string
    {
        Auth::require();
        $quote = new Quote();
        $quote->name = $_POST['name'] ?? '';
        $quote->description = $_POST['description'] ?? null;
        $quote->token = bin2hex(random_bytes(16));

        $quote->save();

        header('Location: /admin/quotes');
        return '';
    }

    public function update(array $vars): string
    {
        Auth::require();
        $id = (int) $vars['id'];
        $quote = Quote::find($id);

        if ($quote === null) {
            http_response_code(404);
            return '404';
        }

        $quote->name = $_POST['name'] ?? $quote->name;
        $quote->description = $_POST['description'] ?? $quote->description;
        $quote->save();

        header('Location: /admin/quotes');
        return '';
    }

    public function delete(array $vars): string
    {
        Auth::require();
        $id = (int) $vars['id'];
        $quote = Quote::find($id);

        if ($quote !== null) {
            $quote->delete();
        }

        header('Location: /admin/quotes');
        return '';
    }

    public function addItem(array $vars): string
    {
        Auth::require();
        $id = (int) $vars['id'];
        $quote = Quote::find($id);

        if ($quote === null) {
            http_response_code(404);
            return '404';
        }

        $value = $_POST['value'] ?? '';
        if (!empty($value)) {
            $quote->addItem($value);
        }

        header('Location: /admin/quote/' . $id);
        return '';
    }

    public function removeItem(array $vars): string
    {
        Auth::require();
        $id = (int) $vars['id'];
        $itemId = (int) $vars['item_id'];
        $quote = Quote::find($id);

        if ($quote !== null) {
            $quote->removeItem($itemId);
        }

        header('Location: /admin/quote/' . $id);
        return '';
    }

    public function updateItem(array $vars): string
    {
        Auth::require();
        $id = (int) $vars['id'];
        $itemId = (int) $vars['item_id'];
        $quote = Quote::find($id);

        if ($quote === null) {
            http_response_code(404);
            return '404';
        }

        $value = $_POST['value'] ?? '';
        if (!empty($value)) {
            $quote->updateItem($itemId, $value);
        }

        header('Location: /admin/quote/' . $id);
        return '';
    }

    public function clearAllItems(array $vars): string
    {
        Auth::require();
        $id = (int) $vars['id'];
        $quote = Quote::find($id);

        if ($quote !== null) {
            $quote->clearAllItems();
        }

        header('Location: /admin/quote/' . $id);
        return '';
    }

    private function render(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../../views/layout.php';
        return ob_get_clean();
    }
}
