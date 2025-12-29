<?php
    session_start();
    header("Content-Type: application/json");
    require_once('../includes/conexion.php');

    if (isset($_SESSION['correo-verificado']['estado']) && $_SESSION['correo-verificado']['estado'] && $_POST['correo'] == $_SESSION['correo-verificado']['correo']) {
        try {
            $usuario = $_POST["nombre"];
            $correo = $_POST["correo"];
            $hash = password_hash($_POST['contrasena'], PASSWORD_DEFAULT, ['cost' => 10]);

            $sql = "INSERT INTO usuarios (nombre, correo, hash_contrasena) VALUES ('$usuario', '$correo', '$hash')";
            $query = mysqli_query($conexion, $sql);

            if ($query) {
                $nuevo_id = mysqli_insert_id($conexion); 
                $_SESSION["user_id"] = $nuevo_id;
                $conexion->close();        
                http_response_code(200);
                echo json_encode([
                    'exito' => true,
                    'mensaje' => "Cuenta creada correctamente. Iniciando sesion..."
                ]);
                unset($_SESSION['correo-verificado']);
                exit;
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => "Nombre de usuario o correo ya existen"
                ]);
                exit;
            }
            elseif ($e->getCode() == 1146) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => "Ocurrio un error al registrarte"
                ]);
                exit;
            }
            else {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => $e->getMessage()
                ]);
                exit;
            }
            $conexion->close();
            exit;
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => $e->getMessage()
            ]);
            exit;
            $conexion->close();
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'exito' => false,
            'mensaje' => "Correo no verificado"
        ]);
        exit;
    }
?>