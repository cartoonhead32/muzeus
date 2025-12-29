<?php
    session_start();
    require_once('../includes/conexion.php');
    try {
        $nombre=$_POST['nombre'];
        $contra=$_POST['contrasena'];

        $sql="SELECT id, hash_contrasena FROM usuarios WHERE nombre='$nombre'";
        $query=mysqli_query($conexion,$sql);
        $respuesta= mysqli_fetch_array($query);
        
        if(password_verify($contra, $respuesta['hash_contrasena'])){
            $conexion->close();
            $_SESSION["user_id"] = $respuesta['id'];
            header("Location: ../index.php");
            exit;
        } else {
            $_SESSION['mensaje_error'] = "Credenciales incorrectas";
            $conexion->close();
            header("Location: ../login.php");
            exit;
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1146) {
            $_SESSION['mensaje_error'] = "Ocurrio un error al iniciar sesion";
        }
        else {
            $_SESSION['mensaje_error'] = $e->getMessage();          
        }
        $conexion->close();
        header("Location: ../login.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = $e->getMessage();
        $conexion->close();
        header("Location: ../login.php");
        exit;
    }
?>