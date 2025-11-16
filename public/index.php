<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../src/helpers.php';
allow_cors();

$path = $_GET['path'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

require_once __DIR__ . '/../src/db.php';

// === Cargar todos los controladores ===
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/PeliculaController.php';
require_once __DIR__ . '/../src/controllers/ActorController.php';
require_once __DIR__ . '/../src/controllers/DirectorController.php';
require_once __DIR__ . '/../src/controllers/PersonaController.php';
require_once __DIR__ . '/../src/controllers/CineController.php';
require_once __DIR__ . '/../src/controllers/SalaController.php';
require_once __DIR__ . '/../src/controllers/HorarioController.php';
require_once __DIR__ . '/../src/controllers/PrecioController.php';
require_once __DIR__ . '/../src/controllers/ReservaController.php';
require_once __DIR__ . '/../src/controllers/RolController.php';
require_once __DIR__ . '/../src/controllers/PeliculaActorController.php';

$segments = explode('/', trim($path, '/'));

switch ($segments[0]) {

    // ======================
    // Página principal
    // ======================
    case '':
        json_response([
            'message' => '🎬 API CINE funcionando correctamente',
            'endpoints' => [
                '/auth', '/peliculas', '/actores', '/directores', '/personas',
                '/cines', '/salas', '/horarios', '/precios',
                '/reservas', '/roles', '/pelicula_actor'
            ]
        ]);
        break;

    // ======================
    // Autenticación
    // ======================
    case 'auth':
        $controller = new AuthController($pdo);
        if ($method === 'POST') $controller->login();
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    // ======================
    // Películas
    // ======================
    case 'peliculas':
        $controller = new PeliculaController($pdo);
        if ($method === 'GET' && empty($segments[1])) $controller->index();
        elseif ($method === 'GET') $controller->show((int)$segments[1]);
        elseif ($method === 'POST') $controller->store();
        elseif ($method === 'PUT') $controller->update((int)$segments[1]);
        elseif ($method === 'DELETE') $controller->delete((int)$segments[1]);
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    // ======================
    // Actores
    // ======================
    case 'actores':
        $controller = new ActorController($pdo);
        if ($method === 'GET' && empty($segments[1])) $controller->index();
        elseif ($method === 'GET') $controller->show((int)$segments[1]);
        elseif ($method === 'POST') $controller->store();
        elseif ($method === 'PUT') $controller->update((int)$segments[1]);
        elseif ($method === 'DELETE') $controller->delete((int)$segments[1]);
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    // ======================
    // Directores
    // ======================
    case 'directores':
        $controller = new DirectorController($pdo);
        if ($method === 'GET' && empty($segments[1])) $controller->index();
        elseif ($method === 'GET') $controller->show((int)$segments[1]);
        elseif ($method === 'POST') $controller->store();
        elseif ($method === 'PUT') $controller->update((int)$segments[1]);
        elseif ($method === 'DELETE') $controller->delete((int)$segments[1]);
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    // ======================
    // Personas
    // ======================
    case 'personas':
        $controller = new PersonaController($pdo);
        if ($method === 'GET' && empty($segments[1])) $controller->index();
        elseif ($method === 'GET') $controller->show((int)$segments[1]);
        elseif ($method === 'POST') $controller->store();
        elseif ($method === 'PUT') $controller->update((int)$segments[1]);
        elseif ($method === 'DELETE') $controller->delete((int)$segments[1]);
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    // ======================
    // Cines
    // ======================
    case 'cines':
        $controller = new CineController($pdo);
        if ($method === 'GET' && empty($segments[1])) $controller->index();
        elseif ($method === 'GET') $controller->show((int)$segments[1]);
        elseif ($method === 'POST') $controller->store();
        elseif ($method === 'PUT') $controller->update((int)$segments[1]);
        elseif ($method === 'DELETE') $controller->delete((int)$segments[1]);
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    // ======================
    // Salas
    // ======================
    case 'salas':
        $controller = new SalaController($pdo);
        if ($method === 'GET' && empty($segments[1])) $controller->index();
        elseif ($method === 'GET') $controller->show((int)$segments[1]);
        elseif ($method === 'POST') $controller->store();
        elseif ($method === 'PUT') $controller->update((int)$segments[1]);
        elseif ($method === 'DELETE') $controller->delete((int)$segments[1]);
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    // ======================
    // Horarios
    // ======================
    case 'horarios':
        $controller = new HorarioController($pdo);
        if ($method === 'GET' && empty($segments[1])) $controller->index();
        elseif ($method === 'GET') $controller->show((int)$segments[1]);
        elseif ($method === 'POST') $controller->store();
        elseif ($method === 'PUT') $controller->update((int)$segments[1]);
        elseif ($method === 'DELETE') $controller->delete((int)$segments[1]);
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    // ======================
    // Precios
    // ======================
    case 'precios':
        $controller = new PrecioController($pdo);
        if ($method === 'GET' && empty($segments[1])) $controller->index();
        elseif ($method === 'GET') $controller->show((int)$segments[1]);
        elseif ($method === 'POST') $controller->store();
        elseif ($method === 'PUT') $controller->update((int)$segments[1]);
        elseif ($method === 'DELETE') $controller->delete((int)$segments[1]);
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    // ======================
    // Reservas
    // ======================
    case 'reservas':
        $controller = new ReservaController($pdo);
        if ($method === 'GET' && empty($segments[1])) $controller->index();
        elseif ($method === 'GET') $controller->show((int)$segments[1]);
        elseif ($method === 'POST') $controller->store();
        elseif ($method === 'DELETE') $controller->delete((int)$segments[1]);
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    // ======================
    // Roles
    // ======================
    case 'roles':
        $controller = new RolController($pdo);
        if ($method === 'GET' && empty($segments[1])) $controller->index();
        elseif ($method === 'GET') $controller->show((int)$segments[1]);
        elseif ($method === 'POST') $controller->store();
        elseif ($method === 'PUT') $controller->update((int)$segments[1]);
        elseif ($method === 'DELETE') $controller->delete((int)$segments[1]);
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    // ======================
    // Película_Actor
    // ======================
    case 'pelicula_actor':
        $controller = new PeliculaActorController($pdo);
        if ($method === 'GET' && empty($segments[1])) $controller->index();
        elseif ($method === 'GET') $controller->show((int)$segments[1]);
        elseif ($method === 'POST') $controller->store();
        elseif ($method === 'DELETE') $controller->delete((int)$segments[1]);
        else json_response(['error' => 'Método no permitido'], 405);
        break;

    // ======================
    // 404 Not Found
    // ======================
    default:
        json_response(['error' => 'Ruta no encontrada'], 404);
}
