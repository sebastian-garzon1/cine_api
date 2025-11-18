<?php
declare(strict_types=1);

class PrecioController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function index(): void {
        echo json_encode($this->pdo->query("SELECT * FROM precio")->fetchAll(PDO::FETCH_ASSOC));
    }

    public function show(int $id): void {
        $stmt = $this->pdo->prepare("SELECT * FROM precio WHERE id_precio=?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    }

    public function store(): void {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['descripcion'], $input['valor'], $input['id_cine'])) {
            echo json_encode(['error' => 'Datos incompletos']);
            return;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO precio (descripcion, valor, id_cine) VALUES (?, ?, ?)"
        );

        $stmt->execute([
            $input['descripcion'],
            $input['valor'],
            $input['id_cine']
        ]);

        echo json_encode([
            'message' => 'Precio creado correctamente',
            'id' => $this->pdo->lastInsertId()
        ]);
    }

    public function update(int $id): void {
        $input = json_decode(file_get_contents('php://input'), true);

        $stmt = $this->pdo->prepare(
            "UPDATE precio SET descripcion=?, valor=?, id_cine=? WHERE id_precio=?"
        );

        $stmt->execute([
            $input['descripcion'],
            $input['valor'],
            $input['id_cine'],
            $id
        ]);

        echo json_encode(['message' => 'Precio actualizado']);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM precio WHERE id_precio=?");
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Precio eliminado']);
    }
}