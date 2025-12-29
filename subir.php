<?php
    include 'includes/header.php';

    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php");
        exit;
    }
?>

<main id="cuerpo-subir">

    <h2 id="sube">Crear subasta</h2>

    <section id="cosa">

        <form class="formulario-subir-subasta"
              action="/actions/subir_action.php"
              method="post"
              enctype="multipart/form-data">

            <label for="inputTitulo">Título:</label>
            <input type="text" id="inputTitulo" name="titulo" required>

            <label for="inputDescripcion">Descripción:</label>
            <textarea id="inputDescripcion" name="descripcion" required></textarea>

            <label for="inputImagen">Imagen:</label>
            <input type="file" id="inputImagen" name="imagen"
                   accept="image/jpg, image/png, image/jpeg, image/webp" required>

            <label for="inputPrecio">Precio inicial:</label>
            <input type="number" id="inputPrecio" name="precio" type="number"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                required>

            <label for="inputDuracion">Fecha de finalización:</label>
            <div id="contenedorDuracion">
                <select name="duracion" id="inputDuracion" required>
                    <option value="">Elija la duración</option>
                    <option value="3600">Una hora</option>
                    <option value="21600">Seis horas</option>
                    <option value="43200">Doce horas</option>
                    <option value="86400">Un día</option>
                    <option value="259200">Tres días</option>
                    <option value="604800">Una semana</option>
                </select>
            </div>

            <input type="submit" value="Publicar subasta">
        </form>

    </section>

    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <span><?php echo $_SESSION['mensaje_error']; ?></span>
        <?php unset($_SESSION['mensaje_error']); ?>
    <?php endif; ?>

</main>

<?php
    include 'includes/footer.php';
?>
