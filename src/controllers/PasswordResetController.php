<?php
declare(strict_types=1);

class PasswordResetController {

    private PDO $pdo;
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function solicitar(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $correo = $input['correo'] ?? '';

        if (empty($correo)) {
            json_response(['error' => 'Debe enviar el correo'], 400);
            return;
        }

        // Verificar si el correo existe
        $stmt = $this->pdo->prepare("SELECT id_persona, nombre FROM persona WHERE correo = ?");
        $stmt->execute([$correo]);

        $persona = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$persona) {
            json_response(['error' => 'Correo no registrado'], 404);
            return;
        }

        // Generar código de 6 dígitos
        $codigo = rand(100000, 999999);

        // Guardar el código en la BD
        $stmt = $this->pdo->prepare("UPDATE persona SET codigo_reset = ? WHERE correo = ?");
        $stmt->execute([$codigo, $correo]);

        // Enviar correo
        $asunto = "Código de recuperación";
        $mensaje = "Hola {$persona['nombre']},\n\nTu código de recuperación es: $codigo";
        
        mail($correo, $asunto, $mensaje);

        json_response(['message' => 'Código enviado al correo']);
    }
    
    public function verificar(): void {
        $input = json_decode(file_get_contents('php://input'), true);

        $correo = $input['correo'] ?? '';
        $codigo = $input['codigo'] ?? '';

        if (empty($correo) || empty($codigo)) {
            json_response(['error' => 'Debe enviar correo y código'], 400);
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM persona WHERE correo = ? AND codigo_reset = ?");
        $stmt->execute([$correo, $codigo]);

        $persona = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$persona) {
            json_response(['error' => 'Código incorrecto'], 400);
            return;
        }

        json_response(['message' => 'Código válido']);
    }
    
    public function cambiar(): void {
        $input = json_decode(file_get_contents('php://input'), true);

        $correo = $input['correo'] ?? '';
        $codigo = $input['codigo'] ?? '';
        $nueva = $input['nueva_contrasena'] ?? '';

        if (empty($correo) || empty($codigo) || empty($nueva)) {
            json_response(['error' => 'Debe enviar correo, código y nueva contraseña'], 400);
            return;
        }

        // Validar código
        $stmt = $this->pdo->prepare("SELECT * FROM persona WHERE correo = ? AND codigo_reset = ?");
        $stmt->execute([$correo, $codigo]);

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            json_response(['error' => 'Código incorrecto'], 400);
            return;
        }

        // Actualizar contraseña
        $stmt = $this->pdo->prepare("UPDATE persona SET contrasena = ?, codigo_reset = NULL WHERE correo = ?");
        $stmt->execute([$nueva, $correo]);

        json_response(['message' => 'Contraseña actualizada correctamente']);
    }
}
