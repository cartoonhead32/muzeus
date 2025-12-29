<?php
session_start();
if (isset($_SESSION['ultimo_envio']) && (time() - $_SESSION['ultimo_envio'] < 1)) {
    $_SESSION['mensaje_error'] = "Espere unos minutos antes de volver a intentar subir una subasta";
    header("Location: ../subir.php");
    exit;
}
$_SESSION['ultimo_envio'] = time();
require_once('../includes/conexion.php');

$vendedor_id = $_SESSION['user_id'];
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$imagen = $_FILES['imagen'];
$precio = $_POST['precio'];
$fecha_fin = date('Y-m-d H:i:s', $_POST['duracion'] + time());

if ($imagen['size'] > 0) {

    $hash = md5_file($imagen['tmp_name']);

    try {
        $sql_hash = "SELECT id FROM subastas WHERE hash_imagen = '$hash'";
        $query_hash = mysqli_query($conexion, $sql_hash);

        if (mysqli_num_rows($query_hash) > 0) {
            $_SESSION['mensaje_error'] = "Esta imagen ya esta registrada en otra subasta";
            header("Location: ../subir.php");
            exit;
        }
    } catch (mysqli_sql_exception $e) {
        echo "Excepcion de myslqi: ". $e->getMessage();
        header("Location: ../subir.php");
        exit;
    }

    $destino = "paintings/" . uniqid("img", true) . '.' . pathinfo($imagen['name'], PATHINFO_EXTENSION);

    if (move_uploaded_file($imagen['tmp_name'], '../' . $destino)) {
        try {
            $sql = "INSERT INTO subastas (vendedor_id, titulo, descripcion, ruta_imagen, hash_imagen, precio, fecha_fin) 
            VALUES ($vendedor_id, '$titulo', '$descripcion', '$destino', '$hash', $precio, '$fecha_fin')";
            $query = mysqli_query($conexion, $sql);
        } catch (mysqli_sql_exception $e) {
            unlink($destino);
            echo "Excepcion de myslqi: ". $e->getMessage();
            header("Location: ../subir.php");
            exit;
        }
        header("Location: ../index.php");
        exit;
    } else {
        header("Location: ../index.php");
        exit;
    }
} elseif ($imagen['size'] < 4194304) {
    $_SESSION['mensaje_error'] = "El limite de la imagen es de 4 MB";
    header("Location: ../subir.php");
    exit;
} else {
    $_SESSION['mensaje_error'] = "No se puede subir la imagen";
    header("Location: ../subir.php");
    exit;
}

?>