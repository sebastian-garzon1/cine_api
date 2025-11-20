<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../src/helpers.php';
allow_cors();
function require_admin(): void {
    $headers = getallheaders();

    // ACEPTAR "Rol", "rol" o "ROL"
    $rol = $headers['Rol'] ??
           $headers['rol'] ??
           $headers['ROL'] ??
           null;

    if ($rol === null) {
        http_response_code(403);
        echo json_encode(['error' => 'Falta el rol']);
        exit;
    }

    if ((int)$rol !== 1) {
        http_response_code(403);
        echo json_encode(['error' => 'No autorizado. Solo administradores.']);
        exit;
    }
}



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
require_once __DIR__ . '/../src/controllers/PasswordResetController.php';

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

    if ($method === 'GET' && empty($segments[1])) {
        $controller->index();
    }
    elseif ($method === 'GET' && is_numeric($segments[1])) {
        $controller->show((int)$segments[1]);
    }
    elseif ($method === 'GET' && $segments[1] === 'buscar') {
        $controller->buscarNombre();
    }
    elseif ($method === 'POST') {
        $controller->store();
    }
    elseif ($method === 'PUT' && is_numeric($segments[1])) {
        $controller->update((int)$segments[1]);
    }
    elseif ($method === 'DELETE' && is_numeric($segments[1])) {
        $controller->delete((int)$segments[1]);
    }
    else {
        json_response(['error' => 'Método no permitido'], 405);
    }
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

    if ($method === 'GET' && empty($segments[1])) {
        // Público: ver todos los precios
        $controller->index();
    }
    elseif ($method === 'GET') {
        // Público: ver un precio
        $controller->show((int)$segments[1]);
    }
    elseif ($method === 'POST') {
        // Solo admins
        require_admin();
        $controller->store();
    }
    elseif ($method === 'PUT') {
        // Solo admins
        require_admin();
        $controller->update((int)$segments[1]);
    }
    elseif ($method === 'DELETE') {
        // Solo admins
        require_admin();
        $controller->delete((int)$segments[1]);
    }
    else {
        json_response(['error' => 'Método no permitido'], 405);
    }
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
    // password_reset
    // ======================
    case 'password_reset':
        $controller = new PasswordResetController($pdo);

        if ($method === 'POST' && $segments[1] === 'solicitar') $controller->solicitar();
        elseif ($method === 'POST' && $segments[1] === 'verificar') $controller->verificar();
        elseif ($method === 'POST' && $segments[1] === 'cambiar') $controller->cambiar();
        else json_response(['error' => 'Ruta no válida'], 404);

        break;

    // ======================
    // 404 Not Found
    // ======================
    default:
        json_response(['error' => 'Ruta no encontrada'], 404);
}
