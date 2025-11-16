<?php
declare(strict_types=1);

class CineController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function index(): void {
        echo json_encode($this->pdo->query("SELECT * FROM cine")->fetchAll());
    }

    public function show(int $id): void {
        $stmt = $this->pdo->prepare("SELECT * FROM cine WHERE id_cine=?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
    }

    public function buscarNombre(): void {
        $nombre = $_GET['nombre'] ?? '';
        $nombre = trim($nombre);
        $stmt = $this->pdo->prepare("
            SELECT * FROM cine 
            WHERE nombre LIKE ?
        ");

        $stmt->execute(["%$nombre%"]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result ?: []);
    }

    public function store(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("INSERT INTO cine (nombre, direccion) VALUES (?, ?)");
        $stmt->execute([$input['nombre'], $input['direccion']]);
        echo json_encode(['message'=>'Cine creado','id'=>$this->pdo->lastInsertId()]);
    }

    public function update(int $id): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("UPDATE cine SET nombre=?, direccion=? WHERE id_cine=?");
        $stmt->execute([$input['nombre'], $input['direccion'], $id]);
        echo json_encode(['message'=>'Cine actualizado']);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM cine WHERE id_cine=?");
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Cine eliminado']);
    }
}
