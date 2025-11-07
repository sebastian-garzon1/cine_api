<?php
declare(strict_types=1);

class PeliculaController {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 📋 Listar todas las películas
     * GET /peliculas
     */
    public function index(): void {
        try {
            $sql = "SELECT p.id_pelicula, p.titulo, p.genero, p.clasificacion, d.nombre AS director
                    FROM pelicula p
                    LEFT JOIN director d ON p.id_director = d.id_director
                    ORDER BY p.id_pelicula DESC";
            $stmt = $this->pdo->query($sql);
            $peliculas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($peliculas);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener películas', 'message' => $e->getMessage()]);
        }
    }

    /**
     * 🎬 Mostrar una película por ID
     * GET /peliculas/{id}
     */
    public function show(int $id): void {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM pelicula WHERE id_pelicula = ?");
            $stmt->execute([$id]);
            $pelicula = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pelicula) {
                http_response_code(404);
                echo json_encode(['error' => 'Película no encontrada']);
                return;
            }

            header('Content-Type: application/json');
            echo json_encode($pelicula);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener película', 'message' => $e->getMessage()]);
        }
    }

    /**
     * ➕ Crear una nueva película
     * POST /peliculas
     */
    public function store(): void {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['titulo'])) {
                http_response_code(400);
                echo json_encode(['error' => 'El campo "titulo" es obligatorio']);
                return;
            }

            $sql = "INSERT INTO pelicula (titulo, genero, clasificacion, id_director)
                    VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $input['titulo'],
                $input['genero'] ?? null,
                $input['clasificacion'] ?? null,
                $input['id_director'] ?? null
            ]);

            $id = (int)$this->pdo->lastInsertId();

            http_response_code(201);
            echo json_encode(['message' => 'Película creada correctamente', 'id' => $id]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear película', 'message' => $e->getMessage()]);
        }
    }

    /**
     * ✏️ Actualizar una película existente
     * PUT /peliculas/{id}
     */
    public function update(int $id): void {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $campos = [];
            $valores = [];

            foreach (['titulo', 'genero', 'clasificacion', 'id_director'] as $campo) {
                if (isset($input[$campo])) {
                    $campos[] = "$campo = ?";
                    $valores[] = $input[$campo];
                }
            }

            if (empty($campos)) {
                http_response_code(400);
                echo json_encode(['error' => 'No se enviaron campos para actualizar']);
                return;
            }

            $valores[] = $id;
            $sql = "UPDATE pelicula SET " . implode(', ', $campos) . " WHERE id_pelicula = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($valores);

            http_response_code(200);
            echo json_encode(['message' => 'Película actualizada correctamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar película', 'message' => $e->getMessage()]);
        }
    }

    /**
     * ❌ Eliminar una película
     * DELETE /peliculas/{id}
     */
    public function delete(int $id): void {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM pelicula WHERE id_pelicula = ?");
            $stmt->execute([$id]);

            http_response_code(200);
            echo json_encode(['message' => 'Película eliminada correctamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar película', 'message' => $e->getMessage()]);
        }
    }
}
