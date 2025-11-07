<?php
declare(strict_types=1);

class PersonaController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    // Listar todas las personas
    public function index(): void {
        $sql = "SELECT p.id_persona, p.nombre, p.apellido, p.correo, r.nombre_rol AS rol
                FROM persona p
                LEFT JOIN rol r ON p.id_rol = r.id_rol";
        echo json_encode($this->pdo->query($sql)->fetchAll());
    }

    // Mostrar persona por ID
    public function show(int $id): void {
        $stmt = $this->pdo->prepare("
            SELECT p.*, r.nombre_rol AS rol
            FROM persona p
            LEFT JOIN rol r ON p.id_rol = r.id_rol
            WHERE id_persona = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
    }

    public function store(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("
            INSERT INTO persona (nombre, apellido, correo, id_rol)
            VALUES (?, ?, ?, ?)");
        $stmt->execute([$input['nombre'], $input['apellido'], $input['correo'], $input['id_rol'] ?? null]);
        echo json_encode(['message'=>'Persona creada','id'=>$this->pdo->lastInsertId()]);
    }

    public function update(int $id): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("
            UPDATE persona SET nombre=?, apellido=?, correo=?, id_rol=? WHERE id_persona=?");
        $stmt->execute([$input['nombre'], $input['apellido'], $input['correo'], $input['id_rol'], $id]);
        echo json_encode(['message'=>'Persona actualizada']);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM persona WHERE id_persona=?");
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Persona eliminada']);
    }
}
