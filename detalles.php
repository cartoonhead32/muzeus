<?php    
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
include 'includes/header.php'; 

?>
<main id="cuerpo_detalles">
<?php try { 
$id = $_GET['id'];
$sql = "SELECT * FROM subastas WHERE id = $id";
$query = mysqli_query($conexion, $sql);
$respuesta = mysqli_fetch_assoc($query);

$vendedor_id = $respuesta['vendedor_id'];

if($respuesta):

    $id = $_GET['id'];
    $sql1 = "SELECT nombre , saldo FROM usuarios WHERE id = $vendedor_id;";
    $query1 = mysqli_query($conexion, $sql1);
    $respuesta1 = mysqli_fetch_assoc($query1);
    
    $id = $_GET['id'];
    $sql2 = "SELECT saldo FROM usuarios WHERE id = $id_sesion;";
    $query2 = mysqli_query($conexion, $sql2);
    $respuesta2 = mysqli_fetch_assoc($query2);
?> 
<section class="contenedor-pantalla-indi">
            <section class="zona-titulo">
                <h2 id="titulo_detalles"><?php echo $respuesta['titulo'] ?></h2>
            </section>

            <section class="zona-principal">
                <section class="zona-imagen">
                    <img src="<?php echo $respuesta['ruta_imagen'] ?>"  class="imagen-obra" id="pinturas_detalles">
                </section>

                <section class="zona-detalles">
                    <p><strong>Autor:</strong> <?php echo $respuesta1['nombre'] ?></p>
                    <p><strong>Precio actual: </strong> <span id='precio'>obteniendo...</span></p>
                    <p><strong>Ofertas realizadas:</strong> <span id='total-ofertas'>obteniendo...</span></p>
                   <p> <strong id='contador' class="contador" fecha-fin='<?php echo $respuesta['fecha_fin'] ?>'> Termina: calculando...</strong></p>
                </section>
            </section>

            <section class="zona-inferior">
                <section class="zona-inferior-izq">
                    <h3>Descripción</h3>                          
                    <p><?php echo $respuesta['descripcion'] ?></p>
                </section>
                <section class="zona-inferior-der">
                        <section id="formulario-oferta-contenedor">
                        <p class="texto-ofertar"> Ofertar:</p>
                        <form id="formulario-oferta">
                            <input type="hidden" name="subasta_id" id='input-subasta' value="<?php echo $_GET['id']; ?>">
                            <input
                                type="number"
                                id="input-oferta"
                                class="input-oferta"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                required>
                            <input type="submit" class="boton-ofertar" id="boton-ofertar" value="Hacer oferta">
                        </form>
                    <span id="mensaje"></span>
                        </section>
                </section>
            </section>
        </section>

    <?php else: ?>
        <span style="color: white;">
            Ese id no existe
        </span>
    <?php endif;?> 
<?php } catch (mysqli_sql_exception $e) {?>
    <span style="color: white;">
        <?php echo $e->getMessage();?>
    </span>
<?php } catch (Exception $e) {?>
    <span style="color: white;">
        <?php echo $e->getMessage();?>
    </span>
<?php } ?>

</main>

<script src="actions/contador.js" defer></script>
<script src="actions/precio.js" defer></script>
<?php
include 'includes/footer.php'; 
?>
