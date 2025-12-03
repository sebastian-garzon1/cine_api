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

   
    public function getPrecio(): void {
        try {
            $idHorario = $_GET['id_horario'] ?? null;

            if (!$idHorario) {
                http_response_code(400);
                echo json_encode(['error' => 'id_horario requerido']);
                return;
            }

            // Obtener el cine del horario
            $sql = "
                SELECT c.id_cine
                FROM horario h
                JOIN sala s ON s.id_sala = h.id_sala
                JOIN cine c ON c.id_cine = s.id_cine
                WHERE h.id_horario = ?
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idHorario]);
            $idCine = $stmt->fetchColumn();

            if (!$idCine) {
                http_response_code(404);
                echo json_encode(['error' => 'No se encontró el cine']);
                return;
            }

            // Obtener precios del cine
            $sql = "SELECT descripcion, valor FROM precio WHERE id_cine = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idCine]);
            $precios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $precioSemana = 0;
            $precioFinde = 0;

            foreach ($precios as $p) {
                $desc = strtolower($p['descripcion']);
                if (strpos($desc, 'entre semana') !== false) {
                    $precioSemana = $p['valor'];
                }
                if (strpos($desc, 'fin de semana') !== false) {
                    $precioFinde = $p['valor'];
                }
            }

            // Determinar precio según día
            $dia = date('N');
            $precioUnitario = ($dia >= 6) ? $precioFinde : $precioSemana;

            echo json_encode([
                'precio_unitario' => $precioUnitario,
                'es_fin_de_semana' => ($dia >= 6)
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener precio', 'mensaje' => $e->getMessage()]);
        }
    }

    public function store(): void {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['id_persona'], $input['id_horario'], $input['cantidad_boletos'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos incompletos']);
                return;
            }

            $idPersona = $input['id_persona'];
            $idHorario = $input['id_horario'];
            $cantidad = $input['cantidad_boletos'];

            $sql = "
                SELECT c.id_cine
                FROM horario h
                JOIN sala s ON s.id_sala = h.id_sala
                JOIN cine c ON c.id_cine = s.id_cine
                WHERE h.id_horario = ?
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idHorario]);
            $idCine = $stmt->fetchColumn();

            if (!$idCine) {
                http_response_code(404);
                echo json_encode(['error' => 'No se encontró el cine del horario']);
                return;
            }

            // Obtener precios del cine
            $sql = "SELECT descripcion, valor FROM precio WHERE id_cine = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idCine]);
            $precios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $precioSemana = 0;
            $precioFinde = 0;

            foreach ($precios as $p) {
                $desc = strtolower($p['descripcion']);

                if (strpos($desc, 'entre semana') !== false) {
                    $precioSemana = $p['valor'];
                }

                if (strpos($desc, 'fin de semana') !== false) {
                    $precioFinde = $p['valor'];
                }
            }
            $dia = date('N');

            if ($dia >= 6) { 
                $precioUnitario = $precioFinde;
            } else {
                $precioUnitario = $precioSemana;
            }

            // Insertar la reserva
            $sql = "
                INSERT INTO reserva (id_persona, id_horario, cantidad_boletos, precio_unitario)
                VALUES (?, ?, ?, ?)
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idPersona, $idHorario, $cantidad, $precioUnitario]);

            $idReserva = $this->pdo->lastInsertId();

 

            // Obtener nombre del usuario

            $sql = "SELECT nombre FROM persona WHERE id_persona = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idPersona]);
            $nombre = $stmt->fetchColumn();


            http_response_code(201);
          echo json_encode([
         'id_reserva' => $idReserva,
         'persona' => $nombre,
         'precio_unitario' => $precioUnitario
]);


        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear reserva', 'mensaje' => $e->getMessage()]);
        }
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM reserva WHERE id_reserva=?");
        $stmt->execute([$id]);
        echo json_encode(['message'=>'Reserva eliminada']);
    }
}