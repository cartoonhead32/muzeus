<?php
    session_start();
    require_once('enviar_correo.php');

    if(isset($_POST['correo'])) {
        $correo = trim($_POST['correo']);
    } else { 
        $correo = '';
    }
    
    if(isset($_POST['nombre'])) {
        $nombre = trim($_POST['nombre']);
    } else { 
        $nombre = '';
    }

    if(empty($correo) || empty($nombre)) {
        header("Content-Type: application/json");
        http_response_code(400);
        echo json_encode([
            'exito' => false,
            'mensaje' => "Datos incompletos (nombre y correo)",
        ]);
        exit;
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Content-Type: application/json");
        http_response_code(400);
        echo json_encode([
            'exito' => false,
            'mensaje' => "Ingrese un correo valido",
        ]);
        exit;
    }

    header("Content-Type: application/json");
    $_SESSION['codigo-verificacion'] = rand(1000, 9999);

    if (enviarCorreo($correo, $nombre, $_SESSION['codigo-verificacion'])) {
        http_response_code(200);
        echo json_encode([
            'exito' => true,
            'mensaje' => "Correo enviado correctamente"
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'exito' => false,
            'mensaje' => "Error al enviar el correo de verificacion"
        ]);
    }
    //
    exit;
?>