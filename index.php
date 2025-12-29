<?php
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    include 'includes/header.php';
    require_once 'actions/finalizar_subastas.php';
?>

<main id="cuerpo">
    <!--
    <section class="main_up">
        <section id="btn_clasico">
            <a href="?categoria=clasico"><img src="img/btClasico.png" class="carteles"></a>
        </section>
        <?php // if ($categoria_actual == "clasico"): ?>
        <section id="titulo_main">
            <i>
                <p class="parrafoPrueba">CLASICO
                </p>
            </i>
        </section>
    <?php // else: ?>
        <section id="titulo_main">
            <i>
                <p class="parrafoPrueba">RELAMPAGO
                </p>
            </i>
        </section>
        <?php // endif;?>
        <section id="btn_relampago">
            <a href="?categoria=relampago"><img src="img/btRelampago.png" class="carteles"></a>
        </section>
    </section> -->

    <section class="main_center">
            <section class="zona-titulo">
                <h2 id="titulo_detalles">Galería</h2>
            </section>
        <section id="contenido_main">
            <?php include('actions/get_subastas.php'); ?>
        </section>
    </section>

</main>

<script src="actions/precio_index.js" defer></script>
<script src="actions/contador.js" defer></script>
<?php
    include 'includes/footer.php'; 
?>