<?php
declare(strict_types=1);

class SalaController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function index(): void {
        $sql = "SELECT s.id_sala, s.nombre, s.capacidad, c.nombre AS cine
                FROM sala s
                LEFT JOIN cine c ON s.id_cine = c.id_cine";
        echo json_encode($this->pdo->query($sql)->fetchAll());
    }

    public function show(int $id): void {
        $stmt = $this->pdo->prepare("
            SELECT s.*, c.nombre AS cine
            FROM sala s
            LEFT JOIN cine c ON s.id_cine = c.id_cine
            WHERE s.id_sala = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
    }

    public function store(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("INSERT INTO sala (nombre, capacidad, id_cine) VALUES (?, ?, ?)");
        $stmt->execute([$input['nombre'], $input['capacidad'], $input['id_cine']]);
        echo json_encode(['message'=>'Sala creada','id'=>$this->pdo->lastInsertId()]);
    }

    public function update(int $id): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("UPDATE sala SET nombre=?, capacidad=?, id_cine=? WHERE id_sala=?");
        $stmt->execute([$input['nombre'], $input['capacidad'], $input['id_cine'], $id]);
        echo json_encode(['message'=>'Sala actualizada']);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM sala WHERE id_sala=?");
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Sala eliminada']);
    }
}
