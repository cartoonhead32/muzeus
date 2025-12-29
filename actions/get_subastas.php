<?php
    try {
        $sql = "SELECT id, titulo, ruta_imagen, precio, precio_actual, fecha_fin FROM subastas WHERE fecha_fin > NOW() ORDER BY fecha_fin ASC";
        $query = mysqli_query($conexion, $sql);
        while ($respuesta = mysqli_fetch_assoc($query)) : 
            $subasta_id = $respuesta['id'];
            $precio_inicial = $respuesta['precio'];
            $precio_actual = $respuesta['precio_actual'];
            $precio_a_mostrar = max($precio_inicial, $precio_actual);
        ?>
            <a class="link-productos" href="detalles.php?id=<?php echo $subasta_id ?>">
                <article class="marcos" data-subasta-id="<?php echo $subasta_id ?>">
                    <section class="producto-imagen">
                        <img src="<?php echo $respuesta['ruta_imagen'] ?>" class="pinturas">
                    </section>
                    <section class="producto-informacion">
                        <p>
                            Titulo:
                            <?php echo $respuesta['titulo'] ?>
                        </p>
                        <p>
                            Precio:
                            <span class="precio-subasta" id="precio-<?php echo $subasta_id; ?>">obteniendo...</span>
                        </p>
                        <p class="contador" id="contador-<?php echo $subasta_id; ?>" fecha-fin='<?php echo $respuesta['fecha_fin'] ?>'>Termina: calculando...</p>
                    </section>
                </article>
            </a>
        <?php endwhile;
    } catch (mysqli_sql_exception $e) {
        echo $e -> getMessage();
    } catch (Exception $e) {
        echo $e -> getMessage();
    }
?>