<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

function enviarCorreo($destino, $asunto, $mensaje) {
    $mail = new PHPMailer(true);

    try {
        // Config SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cinecom2025@gmail.com';
        $mail->Password = 'hnbiweascwgasqgo'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Remitente
        $mail->setFrom('cinecom2025@gmail.com', 'Sistema Cine Max');

        // Destinatario
        $mail->addAddress($destino);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = nl2br($mensaje);

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Error: " . $mail->ErrorInfo;
    }
}
