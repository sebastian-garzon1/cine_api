<?php
declare(strict_types=1);

class PrecioController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function index(): void {
        echo json_encode($this->pdo->query("SELECT * FROM precio")->fetchAll());
    }

    public function show(int $id): void {
        $stmt = $this->pdo->prepare("SELECT * FROM precio WHERE id_precio=?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
    }

    public function store(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("INSERT INTO precio (valor, descripcion) VALUES (?, ?)");
        $stmt->execute([$input['valor'], $input['descripcion']]);
        echo json_encode(['message'=>'Precio creado','id'=>$this->pdo->lastInsertId()]);
    }

    public function update(int $id): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("UPDATE precio SET valor=?, descripcion=? WHERE id_precio=?");
        $stmt->execute([$input['valor'], $input['descripcion'], $id]);
        echo json_encode(['message'=>'Precio actualizado']);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM precio WHERE id_precio=?");
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Precio eliminado']);
    }
}
