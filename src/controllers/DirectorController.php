<?php
declare(strict_types=1);

class DirectorController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function index(): void {
        $sql = "SELECT * FROM director ORDER BY id_director DESC";
        $stmt = $this->pdo->query($sql);
        echo json_encode($stmt->fetchAll());
    }

    public function show(int $id): void {
        $stmt = $this->pdo->prepare("SELECT * FROM director WHERE id_director = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
    }

    public function store(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("INSERT INTO director (nombre) VALUES (?)");
        $stmt->execute([$input['nombre'] ?? null]);
        echo json_encode(['message'=>'Director creado','id'=>$this->pdo->lastInsertId()]);
    }

    public function update(int $id): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("UPDATE director SET nombre=? WHERE id_director=?");
        $stmt->execute([$input['nombre'], $id]);
        echo json_encode(['message'=>'Director actualizado']);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM director WHERE id_director=?");
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Director eliminado']);
    }
}
