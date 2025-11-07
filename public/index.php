<?php
require_once __DIR__ . '/../src/helpers.php';
allow_cors();

$path = $_GET['path'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/PeliculaController.php';

$segments = explode('/', trim($path, '/'));

switch ($segments[0]) {
    case '':
        json_response(['message' => 'API cine - endpoints: /peliculas, /reservas, /auth']);
        break;

    case 'peliculas':
        $controller = new PeliculaController($pdo);
        if ($method === 'GET' && empty($segments[1])) $controller->index();
        elseif ($method === 'GET' && is_numeric($segments[1])) $controller->show((int)$segments[1]);
        else json_response(['error' => 'Ruta no válida'], 404);
        break;

    case 'auth':
        $controller = new AuthController($pdo);
        if ($method === 'POST') $controller->login();
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    default:
        json_response(['error' => 'Ruta no encontrada'], 404);
}
