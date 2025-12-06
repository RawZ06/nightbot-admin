<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\NightbotController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\QuoteController;
use App\Controllers\QuoteApiController;
use App\Database;

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Initialize database
Database::init();

// Router
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    // Auth routes
    $r->get('/login', [AuthController::class, 'showLogin']);
    $r->post('/login', [AuthController::class, 'login']);
    $r->get('/logout', [AuthController::class, 'logout']);

    // Admin - Commands
    $r->get('/admin', [AdminController::class, 'index']);
    $r->get('/admin/commands', [AdminController::class, 'index']);
    $r->get('/admin/command/new', [AdminController::class, 'create']);
    $r->get('/admin/command/{id:\d+}', [AdminController::class, 'edit']);
    $r->post('/admin/command', [AdminController::class, 'store']);
    $r->post('/admin/command/{id:\d+}', [AdminController::class, 'update']);
    $r->post('/admin/command/{id:\d+}/delete', [AdminController::class, 'delete']);

    // Admin - Quotes
    $r->get('/admin/quotes', [QuoteController::class, 'index']);
    $r->get('/admin/quote/new', [QuoteController::class, 'create']);
    $r->get('/admin/quote/{id:\d+}', [QuoteController::class, 'edit']);
    $r->post('/admin/quote', [QuoteController::class, 'store']);
    $r->post('/admin/quote/{id:\d+}', [QuoteController::class, 'update']);
    $r->post('/admin/quote/{id:\d+}/delete', [QuoteController::class, 'delete']);
    $r->post('/admin/quote/{id:\d+}/item', [QuoteController::class, 'addItem']);
    $r->post('/admin/quote/{id:\d+}/item/{item_id:\d+}/delete', [QuoteController::class, 'removeItem']);
    $r->post('/admin/quote/{id:\d+}/item/{item_id:\d+}/update', [QuoteController::class, 'updateItem']);
    $r->post('/admin/quote/{id:\d+}/items/clear', [QuoteController::class, 'clearAllItems']);

    // Admin - Help
    $r->get('/admin/help', [AdminController::class, 'help']);

    // Nightbot API routes
    $r->get('/api/nightbot/{name}', [NightbotController::class, 'execute']);

    // Quote API routes (public avec token)
    $r->get('/api/quote/{name}/random', [QuoteApiController::class, 'getRandom']);
    $r->get('/api/quote/{name}/all', [QuoteApiController::class, 'getAll']);
    $r->post('/api/quote/{name}/add', [QuoteApiController::class, 'add']);
    $r->post('/api/quote/{name}/remove', [QuoteApiController::class, 'remove']);
});

// Fetch method and URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Remove trailing slash (except for root)
if ($uri !== '/' && str_ends_with($uri, '/')) {
    $uri = rtrim($uri, '/');
}

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 - Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 - Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        [$class, $method] = $handler;
        $controller = new $class();
        echo $controller->$method($vars);
        break;
}
