<?php
session_start();
require_once ('includes/conexion.php');
include ('includes/header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}

$usuario_id = (int)$_SESSION['user_id'];

try {
    $sqlUsuario = "SELECT nombre, correo, saldo FROM usuarios WHERE id = ?";
    $stmt = $conexion->prepare($sqlUsuario);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if (!$usuario) {
        header('Location: login.php');
    }

    $sqlPublicadas = "
        SELECT id, titulo, descripcion, ruta_imagen,
            IFNULL(precio_actual, precio) AS precio_actual,
            fecha_fin
        FROM subastas
        WHERE vendedor_id = ?
        ORDER BY fecha_inicio DESC
    ";
    $stmt = $conexion->prepare($sqlPublicadas);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $publicadas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $sqlVendidas = "
        SELECT s.id, s.titulo, s.descripcion, s.ruta_imagen,
               IFNULL(s.precio_actual, s.precio) AS precio_actual,
               s.fecha_fin,
               u.nombre AS comprador_nombre
        FROM subastas s
        INNER JOIN usuarios u ON u.id = s.usuario_ganador_temporal_id
        WHERE s.vendedor_id = ?
          AND s.fecha_fin < NOW()
          AND s.usuario_ganador_temporal_id IS NOT NULL
        ORDER BY s.fecha_fin DESC
    ";
    $stmt = $conexion->prepare($sqlVendidas);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $vendidas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $sqlCompradas = "
        SELECT id, titulo, descripcion, ruta_imagen,
            IFNULL(precio_actual, precio) AS precio_actual,
            fecha_fin
        FROM subastas
        WHERE usuario_ganador_temporal_id = ?
        AND fecha_fin < NOW()
        ORDER BY fecha_fin DESC
    ";
    $stmt = $conexion->prepare($sqlCompradas);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $compradas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $sqlOfertados = "
        SELECT s.id, s.titulo, s.descripcion, s.ruta_imagen,
               IFNULL(s.precio_actual, s.precio) AS precio_actual,
               s.fecha_fin,
               s.usuario_ganador_temporal_id,
               MAX(o.monto_oferta) AS mi_oferta
        FROM subastas s
        INNER JOIN ofertas o ON o.subasta_id = s.id
        WHERE o.usuario_id = ?
          AND s.fecha_fin > NOW()
        GROUP BY s.id, s.titulo, s.descripcion, s.ruta_imagen, s.precio_actual, s.precio, s.fecha_fin, s.usuario_ganador_temporal_id
        ORDER BY s.fecha_fin DESC
    ";
    $stmt = $conexion->prepare($sqlOfertados);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $ofertados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch(mysqli_sql_exception $e) {
    die(var_dump($e->getMessage()));
}
?>
    
    <main class="cuerpo-perfil">
        <section class="zona-titulo">
            <h2 id="titulo_detalles">Perfil</h2>
        </section>
        <input type="hidden" id="perfil">
        <section class="panel panel-datos">
            <h3 class="titulo-seccion">Datos de cuenta</h3>
            <section class="fila-dato">
                <span class="etiqueta">Nombre de usuario</span>
                <span class="valor"><?php echo htmlspecialchars($usuario['nombre']); ?></span>
            </section>
            <section class="fila-dato">
                <span class="etiqueta">Correo de contacto</span>
                <span class="valor"><?php echo htmlspecialchars($usuario['correo']); ?></span>
            </section>
            <section class="fila-dato">
                <span class="etiqueta">Saldo disponible</span>
                <span class="valor">$<?php echo number_format((float)$usuario['saldo'], 2); ?></span>
            </section>

            <section class="acciones-cuenta">
            </section>
        </section>

        <section class="columna-productos">
            
            <section class="panel panel-productos">
                <section class="encabezado-productos">
                    <h3 class="titulo-seccion">Productos ofertados</h3>
                    <section class="carousel-nav">
                        <button class="carousel-btn prev" data-target="lista-ofertados">&#10094;</button>
                        <button class="carousel-btn next" data-target="lista-ofertados">&#10095;</button>
                    </section>
                </section>

                <section class="lista-productos" id="lista-ofertados">
                    <?php if (empty($ofertados)): ?>
                        <p class="mensaje-vacio">Aún no has ofertado en ningún producto.</p>
                    <?php else: ?>
                        <?php foreach ($ofertados as $obra): ?>
                            <?php
                                $miOferta = isset($obra['mi_oferta']) ? (float)$obra['mi_oferta'] : 0.0;
                                $precioActual = isset($obra['precio_actual']) ? (float)$obra['precio_actual'] : 0.0;
                                $ganadorTemporal = isset($obra['usuario_ganador_temporal_id']) ? (int)$obra['usuario_ganador_temporal_id'] : 0;
                                
                                $subastaActiva = strtotime($obra['fecha_fin']) > time(); 

                                $estadoClase = '';
                                $estadoMensaje = '';

                                if ($subastaActiva) {
                                    if ($ganadorTemporal === $usuario_id) {
                                        $estadoMensaje = '¡Vas ganando!';
                                        $estadoClase = 'estado-ganando';
                                    } elseif ($precioActual > $miOferta) {
                                        $estadoMensaje = '¡Oferta superada!';
                                        $estadoClase = 'estado-superado';
                                    } else {
                                        $estadoMensaje = 'Oferta activa';
                                        $estadoClase = 'estado-activo';
                                    }
                                } else {
                                    if ($ganadorTemporal === $usuario_id) {
                                        $estadoMensaje = '¡GANASTE!';
                                        $estadoClase = 'estado-ganador-final';
                                    } else {
                                        $estadoMensaje = 'Perdiste la subasta';
                                        $estadoClase = 'estado-perdedor-final';
                                    }
                                }
                            ?>
                            <a href="detalles.php?id=<?php echo (int)$obra['id']; ?>" class="link-productos">
                                <article class="tarjeta-producto <?php echo $estadoClase; ?>">
                                    <section class="producto-imagen">
                                        <img class="pinturas" src="<?php echo htmlspecialchars($obra['ruta_imagen']); ?>" alt="<?php echo htmlspecialchars($obra['titulo']); ?>">
                                    </section>
                                    <section class="producto-info">
                                        <h4><?php echo htmlspecialchars($obra['titulo']); ?></h4>
                                        <p>Mi oferta: $<?php echo number_format((float)$obra['mi_oferta'], 2); ?></p>
                                        <p>Precio actual: $<?php echo number_format((float)$obra['precio_actual'], 2); ?></p>
                                        
                                        <p class="estado-oferta <?php echo $estadoClase; ?>"><?php echo $estadoMensaje; ?></p>
                                        
                                        <p class="contador" fecha-fin='<?php echo htmlspecialchars($obra['fecha_fin']); ?>'>Termina: calculando...</p>
                                    </section>
                                </article>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </section>

            <section class="panel panel-productos">
                <section class="encabezado-productos">
                    <h3 class="titulo-seccion">Productos publicados</h3>
                    <section class="carousel-nav">
                        <button class="carousel-btn prev" data-target="lista-publicados">&#10094;</button>
                        <button class="carousel-btn next" data-target="lista-publicados">&#10095;</button>
                    </section>
                </section>

                <section class="lista-productos" id="lista-publicados">
                    <?php if (empty($publicadas)): ?>
                        <p class="mensaje-vacio">Aún no has publicado ningún producto.</p>
                    <?php else: ?>
                        <?php foreach ($publicadas as $obra): ?>
                            <a href="detalles.php?id=<?php echo (int)$obra['id']; ?>" class="link-productos">
                                <article class="tarjeta-producto">
                                    <section class="producto-imagen">
                                        <img class="pinturas"src="<?php echo htmlspecialchars($obra['ruta_imagen']); ?>" alt="<?php echo htmlspecialchars($obra['titulo']); ?>">
                                    </section>
                                    <section class="producto-info">
                                        <h4><?php echo htmlspecialchars($obra['titulo']); ?></h4>
                                        <p>Artista: <?php echo htmlspecialchars($usuario['nombre']); ?></p>
                                        <p>Precio actual: $<?php echo number_format((float)$obra['precio_actual'], 2); ?></p>
                                    </section>
                                </article>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </section>

            <section class="panel panel-productos">
                <section class="encabezado-productos">
                    <h3 class="titulo-seccion">Productos vendidos</h3>
                    <section class="carousel-nav">
                        <button class="carousel-btn prev" data-target="lista-vendidos">&#10094;</button>
                        <button class="carousel-btn next" data-target="lista-vendidos">&#10095;</button>
                    </section>
                </section>

                <section class="lista-productos" id="lista-vendidos">
                    <?php if (empty($vendidas)): ?>
                        <p class="mensaje-vacio">Aún no has vendido ningún producto.</p>
                    <?php else: ?>
                        <?php foreach ($vendidas as $obra): ?>
                            <a href="detalles.php?id=<?php echo (int)$obra['id']; ?>" class="link-productos">
                                <article class="tarjeta-producto">
                                    <section class="producto-imagen">
                                        <img class="pinturas" src="<?php echo htmlspecialchars($obra['ruta_imagen']); ?>" alt="<?php echo htmlspecialchars($obra['titulo']); ?>">
                                    </section>
                                    <section class="producto-info">
                                        <h4><?php echo htmlspecialchars($obra['titulo']); ?></h4>
                                        <p>Precio final: $<?php echo number_format((float)$obra['precio_actual'], 2); ?></p>
                                        <p>Comprador: <?php echo htmlspecialchars($obra['comprador_nombre']); ?></p>
                                        <p>Finalizó: <?php echo htmlspecialchars($obra['fecha_fin']); ?></p>
                                    </section>
                                </article>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </section>

            <section class="panel panel-productos">
                <section class="encabezado-productos">
                    <h3 class="titulo-seccion">Productos comprados</h3>
                    <section class="carousel-nav">
                        <button class="carousel-btn prev" data-target="lista-comprados">&#10094;</button>
                        <button class="carousel-btn next" data-target="lista-comprados">&#10095;</button>
                    </section>
                </section>

                <section class="lista-productos" id="lista-comprados">
                    <?php if (empty($compradas)): ?>
                        <p class="mensaje-vacio">Aún no has comprado ningún producto.</p>
                    <?php else: ?>
                        <?php foreach ($compradas as $obra): ?>
                            <a href="detalles.php?id=<?php echo (int)$obra['id']; ?>" class="link-productos">
                                <article class="tarjeta-producto">
                                    <section class="producto-imagen">
                                        <img class="pinturas" src="<?php echo htmlspecialchars($obra['ruta_imagen']); ?>" alt="<?php echo htmlspecialchars($obra['titulo']); ?>">
                                    </section>
                                    <section class="producto-info">
                                        <h4><?php echo htmlspecialchars($obra['titulo']); ?></h4>
                                        <p>Precio pagado: $<?php echo number_format((float)$obra['precio_actual'], 2); ?></p>
                                        <p>Adquirido: <?php echo htmlspecialchars($obra['fecha_fin']); ?></p>
                                    </section>
                                </article>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </section>
            
        </section>
    </main>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const listaContadores = document.querySelectorAll('.contador[fecha-fin]');
        let contadoresActivos = [];

        listaContadores.forEach(parrafoDOM => {
            const fechaFinDb = parrafoDOM.getAttribute('fecha-fin');
            if (fechaFinDb) {
                const fechaFinJsMs = Date.parse(fechaFinDb.replace(' ', 'T'));
                if (!isNaN(fechaFinJsMs)) {
                    const fechaFinJs = Math.floor(fechaFinJsMs / 1000);
                    contadoresActivos.push({ dom: parrafoDOM, fin: fechaFinJs });
                }
            }
        });

        function actualizarContadores() {
            const ahora = Math.floor(Date.now() / 1000);

            contadoresActivos = contadoresActivos.filter(item => {
                const restante = item.fin - ahora;
                const el = item.dom;

                if (restante > 0) {
                    const dias = Math.floor(restante / 86400);
                    const horas = Math.floor((restante % 86400) / 3600);
                    const minutos = Math.floor((restante % 3600) / 60);
                    const segundos = restante % 60;

                    const d = dias.toString().padStart(2, '0');
                    const h = horas.toString().padStart(2, '0');
                    const m = minutos.toString().padStart(2, '0');
                    const s = segundos.toString().padStart(2, '0');

                    el.textContent = `Termina: ${d}:${h}:${m}:${s}`;
                    return true;
                } else {
                    el.style.color = 'red';
                    el.textContent = 'Subasta terminada';
                    return false;
                }
            });

            if (contadoresActivos.length === 0) {
                clearInterval(intervalo);
            }
        }

        if (contadoresActivos.length > 0) {
            const intervalo = setInterval(actualizarContadores, 1000);
            actualizarContadores();
        }
    });
    </script>
<?php
include ('includes/footer.php');
?>