<?php
    function respuestahttp($codigo, $exito, $mensaje, $data = null) {
        http_response_code($codigo);
        echo json_encode([
            'exito' => $exito,
            'mensaje' => $mensaje,
            'data' => $data
        ]);
        exit;
    }
    header("Content-Type: application/json");
    require_once ('../includes/conexion.php');
    
    try {
        if (!isset($_POST['subasta_id'])) {
             respuestahttp(400, false, "ID de subasta faltante");
        }
        $subasta_id = (int)$_POST['subasta_id'];

        $sql = "SELECT precio, precio_actual FROM subastas WHERE id = $subasta_id";
        $query = mysqli_query($conexion, $sql);
        $respuesta = mysqli_fetch_assoc($query);

        if (!$respuesta) {
            respuestahttp(404, false, "Subasta no encontrada");
        }
        
        $precio_alto = max($respuesta['precio'], $respuesta['precio_actual']);

        $sql_count = "SELECT COUNT(id) AS total_ofertas FROM ofertas WHERE subasta_id = $subasta_id";
        $query_count = mysqli_query($conexion, $sql_count);
        $respuesta_count = mysqli_fetch_assoc($query_count);
        
        $total_ofertas = $respuesta_count['total_ofertas'];
        respuestahttp(200, true, "Datos obtenidos", [
            'precio_actual' => $precio_alto,
            'total_ofertas' => $total_ofertas
        ]);

    } catch(mysqli_sql_exception $e) {
        respuestahttp(500, false, $e->getMessage());
    } catch(Exception $e) {
        respuestahttp(500, false, $e->getMessage());
    }
?>