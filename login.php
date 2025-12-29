<?php
    if (isset($_SESSION["user_id"])) {
        header("Location: index.php");
    }   
    include 'includes/header.php';
?>
<main id="cuerpo-registro">
    <section class="zona-titulo">
        <h2 id="titulo_detalles">Inicio de sesion</h2>
    </section>
    <section id="conjunto-login">
        <section id="formulario-contenedor-login">
            <form action="actions/login_action.php" method="post" id="formulario-login">
                <label for="login">
                    Ingresa tu nombre de usuario:
                </label>
                <input
                    type="text"
                    name="nombre"
                    class="formulario-texto-login"
                    required="required">
                <label for="login">
                    Ingresa tu contraseña:</label>
                <input
                    type="password"
                    name="contrasena"
                    class="formulario-texto-login"
                    required="required">
                <section id="formulario-botones-login">
                    <input type="submit" id="formulario-envio-login" value="Enviar">
                    <a id="formulario-registro-login" href="registro.php">Registrarte</a>
                </section>
            </form>
            <?php if (isset($_SESSION['mensaje_error'])): ?>
            <span><?php echo $_SESSION['mensaje_error']?></span>
            <?php
                unset($_SESSION['mensaje_error']);
                endif; ?>
        </section>
    </section>
</main>
<?php
    include 'includes/footer.php';
?>