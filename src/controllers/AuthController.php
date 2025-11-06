<?php
// src/controllers/AuthController.php
declare(strict_types=1);

class AuthController {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function login(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['usuario']) || empty($input['contrasena'])) json_response(['error'=>'usuario y contrasena requeridos'],400);

        $stmt = $this->pdo->prepare("SELECT l.*, p.nombre, p.apellido, r.nombre_rol 
                                     FROM login l
                                     JOIN persona p ON l.id_persona = p.id_persona
                                     JOIN rol r ON l.id_rol = r.id_rol
                                     WHERE l.usuario = ?");
        $stmt->execute([$input['usuario']]);
        $user = $stmt->fetch();
        if (!$user) json_response(['error'=>'Credenciales inválidas'],401);

        // Si tus contraseñas están hasheadas usar password_verify
        if ($user['contrasena'] !== $input['contrasena']) {
            json_response(['error'=>'Credenciales inválidas'],401);
        }

        // response (podrías emitir JWT aquí)
        unset($user['contrasena']);
        json_response(['message'=>'ok','user'=>$user]);
    }
}
