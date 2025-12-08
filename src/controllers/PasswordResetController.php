<?php
declare(strict_types=1);
require_once __DIR__ . '/../email_helper.php';

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
        $stmt = $this->pdo->prepare("SELECT 
            p.id_persona,
            p.nombre,
            p.email,
            l.usuario
        FROM persona p
        LEFT JOIN login l 
            ON p.id_persona = l.id_persona
        WHERE p.email = ?;");
        $stmt->execute([$correo]);

        $persona = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$persona) {
            json_response(['error' => 'Usuario no registrado'], 404);
            return;
        }

        // Generar código de 6 dígitos
        $codigo = rand(100000, 999999);

        // Guardar el código en la BD
        $stmt = $this->pdo->prepare("UPDATE login SET code_reset = ? WHERE id_persona = ?");
        $stmt->execute([$codigo, $persona["id_persona"]]);

        $correo = enviarCorreo(
            $correo,
            "Codigo para restablecer contraseña",
            "Tu código de recuperación es: <b>$codigo</b>"
        );

        if($correo){
            json_response(['message' => 'Código enviado al correo'], 200);
        }else{
            json_response(['error' => 'No se ha podido enviar el correo'], 404);
        }
    }
    
    public function verificar(): void {
        $input = json_decode(file_get_contents('php://input'), true);

        $correo = $input['correo'] ?? '';
        $codigo = $input['codigo'] ?? '';

        if (empty($correo) || empty($codigo)) {
            json_response(['error' => 'Debe enviar correo y código'], 400);
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM persona p LEFT JOIN login l ON p.id_persona = l.id_persona WHERE p.email = ? and l.code_reset = ?;");
        $stmt->execute([$correo, $codigo]);

        $persona = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$persona) {
            json_response(['error' => 'Código incorrecto'], 400);
            return;
        }

        json_response(['message' => 'Código válido'], 200);
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
        $stmt = $this->pdo->prepare("SELECT * FROM persona p LEFT JOIN login l ON p.id_persona = l.id_persona WHERE p.email = ? and l.code_reset = ?;");
        $stmt->execute([$correo, $codigo]);
        $persona = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$persona) {
            json_response(['error' => 'Código incorrecto'], 400);
            return;
        }

        // Actualizar contraseña
        $stmt = $this->pdo->prepare("UPDATE login SET contrasena = ?, code_reset = NULL WHERE id_persona = ?");
        $stmt->execute([$nueva, $persona["id_persona"]]);

        json_response(['message' => 'Contraseña actualizada correctamente'], 200);
    }
}
