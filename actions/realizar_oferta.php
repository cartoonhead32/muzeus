<?php
    session_start();
    header('Content-Type: application/json');
    require_once('../includes/conexion.php');

    function respuestahttp($codigo, $exito, $mensaje) {
        http_response_code($codigo);
        echo json_encode([
            'exito' => $exito,
            'mensaje' => $mensaje
        ]);
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        respuestahttp(401, false, "Por favor, inicie sesion");
    }
    if (!isset($_POST['subasta_id']) || !isset($_POST['oferta'])) {
        respuestahttp(400, false, "Datos incompletos");
    }

    $usuario_id = $_SESSION['user_id'];
    $cantidad_oferta = (float)$_POST['oferta'];
    $subasta_id = (int)$_POST['subasta_id'];

    $sql = "SELECT saldo FROM usuarios WHERE id = $usuario_id";
    $query = mysqli_query($conexion, $sql);
    $respuesta = mysqli_fetch_assoc($query);
    
    $saldo_usuario = $respuesta['saldo'];

    if ($cantidad_oferta > $respuesta["saldo"]) {
        respuestahttp(401, false, "No tiene suficiente saldo");
    }
    if ($cantidad_oferta <= 0) {
        respuestahttp(400, false, "El monto debe ser positivo");
    }

    $sql = "SELECT fecha_fin, vendedor_id, precio_actual, usuario_ganador_temporal_id FROM subastas WHERE id = $subasta_id";
    $query = mysqli_query($conexion, $sql);
    $datos_subasta = mysqli_fetch_assoc($query);

    $vendedor_id = $datos_subasta['vendedor_id'];
    $precio_actual = $datos_subasta['precio_actual'];
    $ganador_anterior_id = $datos_subasta['usuario_ganador_temporal_id'];

    if ($usuario_id == $vendedor_id) {
        respuestahttp(401, false, "El vendedor no puede hacer ofertas");
    }
    
    if ($usuario_id == $ganador_anterior_id) {
        respuestahttp(401, false, "No puede ofertar dos veces seguidas");
    }
    
    if (strtotime($datos_subasta['fecha_fin']) < time()) {
        respuestahttp(400, false, "La subasta ha terminado. No puede realizar mas ofertas");
    }
    if ($cantidad_oferta <= $precio_actual) {
        respuestahttp(401, false, "La oferta debe ser mayor que el precio actual");
    }

    mysqli_begin_transaction($conexion);

    try {
        if ($ganador_anterior_id != null) {
            $sql = "
                UPDATE usuarios 
                SET saldo = saldo + $precio_actual,
                    saldo_ofertado = saldo_ofertado - $precio_actual
                WHERE id = $ganador_anterior_id
            ";
            mysqli_query($conexion, $sql);
        }

        $sql = "
            UPDATE usuarios
            SET saldo = saldo - $cantidad_oferta,
                saldo_ofertado = saldo_ofertado + $cantidad_oferta
            WHERE id = $usuario_id
        ";
        mysqli_query($conexion, $sql);

        $sql = "
            UPDATE subastas
            SET precio_actual = $cantidad_oferta,
                usuario_ganador_temporal_id = $usuario_id
            WHERE id = $subasta_id
        ";
        mysqli_query($conexion, $sql);

        $sql = "
            INSERT INTO ofertas (subasta_id, usuario_id, monto_oferta, estado)
            VALUES ($subasta_id, $usuario_id, $cantidad_oferta, 'Activa')
        ";
        mysqli_query($conexion, $sql);

        mysqli_commit($conexion);
        respuestahttp(200, true, "Oferta realizada correctamente");

    } catch (Exception $e) {
        mysqli_rollback($conexion);
        respuestahttp(500, false, "ERROR INTERNO DE TRANSACCIÓN");
    }
?>