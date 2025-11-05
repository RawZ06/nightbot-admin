<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Command;
use App\Auth;

class AdminController
{
    public function index(array $vars = []): string
    {
        Auth::require();
        $commands = Command::all();
        return $this->render('admin/index', ['commands' => $commands, 'page' => 'commands']);
    }

    public function create(array $vars = []): string
    {
        Auth::require();
        return $this->render('admin/edit', ['command' => null, 'page' => 'commands']);
    }

    public function edit(array $vars): string
    {
        Auth::require();
        $id = (int) $vars['id'];
        $command = Command::find($id);

        if ($command === null) {
            http_response_code(404);
            return '404 - Command not found';
        }

        return $this->render('admin/edit', ['command' => $command, 'page' => 'commands']);
    }

    public function store(array $vars = []): string
    {
        Auth::require();
        $command = new Command();
        $command->name = $_POST['name'] ?? '';
        $command->description = $_POST['description'] ?? null;
        $command->code = $_POST['code'] ?? '';

        if (empty($command->name) || empty($command->code)) {
            http_response_code(400);
            return $this->render('admin/edit', [
                'command' => $command,
                'error' => 'Name and code are required'
            ]);
        }

        $command->save();

        // Redirect to list
        header('Location: /admin');
        return '';
    }

    public function update(array $vars): string
    {
        Auth::require();
        $id = (int) $vars['id'];
        $command = Command::find($id);

        if ($command === null) {
            http_response_code(404);
            return '404 - Command not found';
        }

        $command->name = $_POST['name'] ?? $command->name;
        $command->description = $_POST['description'] ?? $command->description;
        $command->code = $_POST['code'] ?? $command->code;

        if (empty($command->name) || empty($command->code)) {
            http_response_code(400);
            return $this->render('admin/edit', [
                'command' => $command,
                'error' => 'Name and code are required'
            ]);
        }

        $command->save();

        // Redirect to list
        header('Location: /admin');
        return '';
    }

    public function delete(array $vars): string
    {
        Auth::require();
        $id = (int) $vars['id'];
        $command = Command::find($id);

        if ($command !== null) {
            $command->delete();
        }

        // Redirect to list
        header('Location: /admin');
        return '';
    }

    public function help(array $vars = []): string
    {
        Auth::require();
        return $this->render('admin/help', ['page' => 'help']);
    }

    private function render(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../../views/layout.php';
        return ob_get_clean();
    }
}
