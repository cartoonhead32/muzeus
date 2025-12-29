<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

try {
    $dotenv->load();
} catch (Exception $e) {
    die("Error cargando .env: " . $e->getMessage()); 
}

function enviarCorreo($destinatario, $nombre, $codigo) {
    $host = $_ENV['SMTP_HOST'];
    $user = $_ENV['SMTP_USER'];
    $pass = $_ENV['SMTP_PASS'];
    $port = $_ENV['SMTP_PORT'];

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $port;
        
        $mail->CharSet = 'UTF-8';

        $mail->setFrom($user, 'Muzeus Oficial');
        $mail->addAddress($destinatario, $nombre);

        $mail->isHTML(true);
        $mail->Subject = 'Verifica tu cuenta en Muzeus';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ccc;'>
                <h1 style='color: #333;'>Hola, $nombre</h1>
                <p>Tu código de verificación es:</p>
                <h2 style='color: #007bff; font-size: 24px; letter-spacing: 5px;'>$codigo</h2>
                <p>Si no solicitaste este registro, ignora este correo.</p>
            </div>
        ";
        $mail->AltBody = "Hola $nombre, tu código es: $codigo";
        $mail->AltBody = "Hola $nombre, tu código de verificación es: $codigo";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}