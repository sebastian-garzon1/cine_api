<?php
declare(strict_types=1);

class HorarioController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function index(): void {
        $sql = "SELECT h.id_horario, h.hora, p.titulo AS pelicula, s.nombre AS sala
                FROM horario h
                JOIN pelicula p ON h.id_pelicula = p.id_pelicula
                JOIN sala s ON h.id_sala = s.id_sala";
        echo json_encode($this->pdo->query($sql)->fetchAll());
    }

    public function show(int $id): void {
        $stmt = $this->pdo->prepare("
            SELECT h.*, p.titulo AS pelicula, s.nombre AS sala
            FROM horario h
            JOIN pelicula p ON h.id_pelicula = p.id_pelicula
            JOIN sala s ON h.id_sala = s.id_sala
            WHERE h.id_horario = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
    }

    public function store(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("INSERT INTO horario (id_pelicula, id_sala, hora) VALUES (?, ?, ?)");
        $stmt->execute([$input['id_pelicula'], $input['id_sala'], $input['hora']]);
        echo json_encode(['message'=>'Horario creado','id'=>$this->pdo->lastInsertId()]);
    }

    public function update(int $id): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("UPDATE horario SET id_pelicula=?, id_sala=?, hora=? WHERE id_horario=?");
        $stmt->execute([$input['id_pelicula'], $input['id_sala'], $input['hora'], $id]);
        echo json_encode(['message'=>'Horario actualizado']);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM horario WHERE id_horario=?");
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Horario eliminado']);
    }
}
