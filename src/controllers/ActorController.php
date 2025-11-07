<?php
declare(strict_types=1);

class ActorController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function index(): void {
        $sql = "SELECT * FROM actor ORDER BY id_actor DESC";
        echo json_encode($this->pdo->query($sql)->fetchAll());
    }

    public function show(int $id): void {
        $stmt = $this->pdo->prepare("SELECT * FROM actor WHERE id_actor = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
    }

    public function store(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("INSERT INTO actor (nombre) VALUES (?)");
        $stmt->execute([$input['nombre'] ?? null]);
        echo json_encode(['message'=>'Actor creado','id'=>$this->pdo->lastInsertId()]);
    }

    public function update(int $id): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("UPDATE actor SET nombre=? WHERE id_actor=?");
        $stmt->execute([$input['nombre'], $id]);
        echo json_encode(['message'=>'Actor actualizado']);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM actor WHERE id_actor=?");
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Actor eliminado']);
    }
}
