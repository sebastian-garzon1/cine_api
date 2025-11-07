<?php
declare(strict_types=1);

class RolController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function index(): void {
        echo json_encode($this->pdo->query("SELECT * FROM rol")->fetchAll());
    }

    public function show(int $id): void {
        $stmt = $this->pdo->prepare("SELECT * FROM rol WHERE id_rol=?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
    }

    public function store(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("INSERT INTO rol (nombre_rol) VALUES (?)");
        $stmt->execute([$input['nombre_rol']]);
        echo json_encode(['message'=>'Rol creado','id'=>$this->pdo->lastInsertId()]);
    }

    public function update(int $id): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("UPDATE rol SET nombre_rol=? WHERE id_rol=?");
        $stmt->execute([$input['nombre_rol'], $id]);
        echo json_encode(['message'=>'Rol actualizado']);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM rol WHERE id_rol=?");
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Rol eliminado']);
    }
}
