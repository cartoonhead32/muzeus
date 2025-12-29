<?php    
    if (isset($_SESSION["user_id"])) {
        header("Location: index.php");
    }   
    include 'includes/header.php';
?>
<main id="cuerpo-registro">
    <section class="zona-titulo">
        <h2 id="titulo_detalles">Registro de usuario</h2>
    </section>
    <section id="conjunto-login">
        <section id="formulario-contenedor-login">
            <form id="formulario-registro">
                <label for="nombre">Ingresa tu nombre de usuario:
                </label>
                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    class="formulario-texto-login"
                    required="required">
                <label for="correo">Ingresa tu correo electronico: 
                </label>
                <section id="comprobacion-registro">

                <input
                    type="text"
                    name="correo"
                    id="correo"
                    class="formulario-texto-login"
                    required="required">
                <button onclick="envioCodigo()" type="button" id="formulario-envio-login">Comprobar</button>
                </section>
                <div id="contenedor-codigo">
                    <label for="codigo" id="label-verificacion">Codigo: </label>
                    <input
                        type="text"
                        name="codigo"
                        class="formulario-texto-login"
                        id="codigo">
                    <button onclick="verificacionCodigo()" type="button" id="formulario-envio-login">Verificar</button>
                </div>
                <label for="contrasena">Ingresa tu contraseña:
                </label>
                <input
                    type="password"
                    name="contrasena"
                    id="contrasena"
                    class="formulario-texto-login"
                    required="required">
                <section id="formulario-botones-login">
                    <input type="submit" id="formulario-envio-login" value="Enviar">
                </section>
            </form>
            <span id="mensaje"></span>
        </section>
    </section>
</main>
<?php
    include 'includes/footer.php';
?>