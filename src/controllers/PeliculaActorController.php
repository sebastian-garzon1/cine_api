<?php
declare(strict_types=1);

class PeliculaActorController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    // Listar todas las relaciones con nombres
    public function index(): void {
        $sql = "SELECT pa.id_pelicula_actor, p.titulo AS pelicula, a.nombre AS actor
                FROM pelicula_actor pa
                JOIN pelicula p ON pa.id_pelicula = p.id_pelicula
                JOIN actor a ON pa.id_actor = a.id_actor
                ORDER BY pa.id_pelicula_actor DESC";
        echo json_encode($this->pdo->query($sql)->fetchAll());
    }

    // Mostrar una relación específica
    public function show(int $id): void {
        $stmt = $this->pdo->prepare("
            SELECT pa.id_pelicula_actor, p.titulo AS pelicula, a.nombre AS actor
            FROM pelicula_actor pa
            JOIN pelicula p ON pa.id_pelicula = p.id_pelicula
            JOIN actor a ON pa.id_actor = a.id_actor
            WHERE pa.id_pelicula_actor = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
    }

    // Crear relación
    public function store(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['id_pelicula']) || empty($input['id_actor'])) {
            echo json_encode(['error'=>'Campos id_pelicula y id_actor requeridos']);
            return;
        }
        $stmt = $this->pdo->prepare("INSERT INTO pelicula_actor (id_pelicula, id_actor) VALUES (?, ?)");
        $stmt->execute([$input['id_pelicula'], $input['id_actor']]);
        echo json_encode(['message'=>'Relación creada','id'=>$this->pdo->lastInsertId()]);
    }

    // Eliminar relación
    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM pelicula_actor WHERE id_pelicula_actor=?");
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Relación eliminada']);
    }
}
