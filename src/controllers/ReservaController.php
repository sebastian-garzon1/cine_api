<?php
declare(strict_types=1);

class ReservaController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function index(): void {
        $sql = "SELECT r.id_reserva, r.fecha_reserva, r.estado_pago, 
                       p.nombre AS persona, h.hora, pel.titulo AS pelicula, s.nombre AS sala, c.nombre AS cine
                FROM reserva r
                JOIN persona p ON r.id_persona = p.id_persona
                JOIN horario h ON r.id_horario = h.id_horario
                JOIN pelicula pel ON h.id_pelicula = pel.id_pelicula
                JOIN sala s ON h.id_sala = s.id_sala
                JOIN cine c ON s.id_cine = c.id_cine
                ORDER BY r.id_reserva DESC";
        echo json_encode($this->pdo->query($sql)->fetchAll());
    }

    public function show(int $id): void {
        $stmt = $this->pdo->prepare("
            SELECT r.*, p.nombre AS persona, pel.titulo AS pelicula, s.nombre AS sala, h.hora
            FROM reserva r
            JOIN persona p ON r.id_persona = p.id_persona
            JOIN horario h ON r.id_horario = h.id_horario
            JOIN pelicula pel ON h.id_pelicula = pel.id_pelicula
            JOIN sala s ON h.id_sala = s.id_sala
            WHERE id_reserva = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
    }

    public function store(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->pdo->prepare("
            INSERT INTO reserva (id_persona, id_horario, cantidad_boletos, precio_unitario, estado_pago)
            VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $input['id_persona'],
            $input['id_horario'],
            $input['cantidad_boletos'],
            $input['precio_unitario'],
            $input['estado_pago'] ?? 'Pendiente'
        ]);
        echo json_encode(['message'=>'Reserva creada','id'=>$this->pdo->lastInsertId()]);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM reserva WHERE id_reserva=?");
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Reserva eliminada']);
    }
}
