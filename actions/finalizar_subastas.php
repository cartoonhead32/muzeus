<?php
    $sql = "
        SELECT id, vendedor_id, usuario_ganador_temporal_id, precio_actual
        FROM subastas 
        WHERE fecha_fin < NOW() AND estado = 1
        ORDER BY fecha_fin ASC
        LIMIT 1
    ";
    $res = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($res) === 0) {
        return; 
    }

    while ($s = mysqli_fetch_assoc($res)) {
        
        mysqli_begin_transaction($conexion);

        $subasta_id = $s['id'];
        $vendedor = $s['vendedor_id'];
        $ganador = $s['usuario_ganador_temporal_id'];
        $precio_final = (float)$s['precio_actual'];

        try {
            if ($ganador != null) {
                
                mysqli_query($conexion, "
                    UPDATE usuarios
                    SET saldo = saldo + $precio_final
                    WHERE id = $vendedor
                ");
                
                mysqli_query($conexion, "
                    UPDATE usuarios
                    SET saldo_ofertado = saldo_ofertado - $precio_final
                    WHERE id = $ganador
                ");

                mysqli_query($conexion, "
                    UPDATE ofertas SET estado = 'Ganadora'
                    WHERE subasta_id = $subasta_id AND usuario_id = $ganador
                ");
            }
            
            mysqli_query($conexion, "
                UPDATE ofertas
                SET estado = 'Perdedora'
                WHERE subasta_id = $subasta_id AND estado = 'Activa'
            ");

            $sql_perdedores = "
                SELECT usuario_id, monto_oferta, id
                FROM ofertas
                WHERE subasta_id = $subasta_id AND estado = 'Perdedora'
            ";
            $res_perdedores = mysqli_query($conexion, $sql_perdedores);

            while ($p = mysqli_fetch_assoc($res_perdedores)) {
                $perdedor_id = $p['usuario_id'];
                $monto_reembolso = $p['monto_oferta'];
                $oferta_id = $p['id'];

                mysqli_query($conexion, "
                    UPDATE usuarios
                    SET saldo = saldo + $monto_reembolso,
                        saldo_ofertado = saldo_ofertado - $monto_reembolso
                    WHERE id = $perdedor_id
                ");

                mysqli_query($conexion, "
                    UPDATE ofertas
                    SET estado = 'Reembolsada'
                    WHERE id = $oferta_id
                ");
            }

            mysqli_query($conexion, "
                UPDATE subastas
                SET estado = 3
                WHERE id = $subasta_id
            ");

            mysqli_commit($conexion);
        } catch (Exception $e) {
            mysqli_rollback($conexion);
        }
    }

?>