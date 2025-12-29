<?php
    session_start();
    header("Content-Type: application/json");
    if ($_POST['codigo'] == $_SESSION['codigo-verificacion']) {
        $_SESSION['correo-verificado'] = [
            'estado' => true,
            'correo' => $_POST['correo']
        ];
        unset($_SESSION['codigo-verificacion']);
        http_response_code(200);
        echo json_encode([
            'exito' => true,
            'mensaje' => "Codigo verificado"
        ]);
        exit;
    } else {
        unset($_SESSION['codigo-verificacion']);
        http_response_code(400);
        echo json_encode([
            'exito' => false,
            'mensaje' => "Codigo incorrecto"
        ]);
        exit;
    }
?>