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
        // 1) película + director
        $sql = "
            SELECT p.id_pelicula, p.titulo, p.genero, p.clasificacion,
                   d.nombre AS director
            FROM pelicula p
            LEFT JOIN director d ON p.id_director = d.id_director
            WHERE p.id_pelicula = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $pelicula = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pelicula) {
            http_response_code(404);
            echo json_encode(['error' => 'Película no encontrada']);
            return;
        }

        // 2) Actores relacionados
        $sqlActores = "
            SELECT a.id_actor, a.nombre
            FROM actor a
            INNER JOIN pelicula_actor pa ON pa.id_actor = a.id_actor
            WHERE pa.id_pelicula = ?
        ";
        $stmt2 = $this->pdo->prepare($sqlActores);
        $stmt2->execute([$id]);
        $actores = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'pelicula' => $pelicula,
            'actores'  => $actores
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al obtener película']);
    }
}
public function cines(int $id): void {
    try {
        // 1) Información de la película
        $stmt = $this->pdo->prepare("
            SELECT p.id_pelicula, p.titulo, p.genero, p.clasificacion,
                   d.nombre AS director
            FROM pelicula p
            LEFT JOIN director d ON p.id_director = d.id_director
            WHERE p.id_pelicula = ?
        ");
        $stmt->execute([$id]);
        $pelicula = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pelicula) {
            http_response_code(404);
            echo json_encode(['error' => 'Película no encontrada']);
            return;
        }

        // 2) Cines donde proyectan esa película
        $sqlCines = "
            SELECT DISTINCT c.id_cine, c.nombre
            FROM horario h
            INNER JOIN sala s ON s.id_sala = h.id_sala
            INNER JOIN cine c ON c.id_cine = s.id_cine
            WHERE h.id_pelicula = ?
            ORDER BY c.nombre
        ";

        $stmt2 = $this->pdo->prepare($sqlCines);
        $stmt2->execute([$id]);
        $cines = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'pelicula' => $pelicula,
            'cines' => $cines
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al obtener cines']);
    }
}

// lo acabo de agregar para quede cine colombia sino se puede quitar
public function cinesPorPelicula(int $id): void {
    // Obtener cines que tienen esta película
    $sql = "
        SELECT c.id_cine, c.nombre, c.direccion, c.telefono
        FROM cine c
        INNER JOIN sala s ON s.id_cine = c.id_cine
        INNER JOIN horario h ON h.id_sala = s.id_sala
        WHERE h.id_pelicula = ?
        GROUP BY c.id_cine
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id]);
    $cines = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'cines' => $cines
    ]);
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
