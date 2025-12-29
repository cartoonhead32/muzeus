<?php
    /** 
     *    create table usuarios (
     *        id int not null auto_increment,
     *        nombre varchar(50) not null unique,
     *        correo varchar (50) not null unique,
     *        hash_contrasena varchar(255) not null,
     *        saldo decimal(10,2) default 1000.00,
     *        primary key (id)
     *    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
     *
     *    create table subastas (
     *        id int not null auto_increment,
     *        vendedor_id int not null,
     *        titulo varchar(50) not null,
     *        descripcion text,
     *        ruta_imagen varchar(255) not null, 
     *        precio_inicial decimal(10,2) not null, 
     *        fecha_inicio datetime not null default current_timestamp,
     *        fecha_fin datetime not null,
     *        estado tinyint default 1 not null,
     *        primary key (id),
     *        constraint fk_subasta_vendedor foreign key (vendedor_id) references usuarios(id) on delete cascade
     *    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
     * 
     *   create table ofertas ( 
     *        id int not null auto_increment,
     *        subasta_id int not null,
     *        usuario_id int not null, 
     *        monto_oferta decimal(10,2) not null, 
     *        fecha_oferta datetime not null default current_timestamp,
     *        primary key (id),
     *        constraint fk_oferta_usuario foreign key (usuario_id) references usuarios(id) on delete cascade,
     *        constraint fk_oferta_subasta foreign key (subasta_id) references usuarios(id) on delete cascade
     *    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    */
    session_start();
    require_once 'conexion.php';
    $id_sesion = (int)$_SESSION['user_id'];
    try {
        $sql = "SELECT nombre, saldo FROM usuarios WHERE id = '$id_sesion'";
        $query = mysqli_query($conexion, $sql);
        $respuesta = mysqli_fetch_assoc($query);
    } catch (mysqli_sql_exception $e) {
        die(var_dump($e->getMessage()));
    }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="img/logo.png">
        <title>Muzeus - Subastas online</title>
        <link rel="stylesheet" href="styles.css">
        <?php if (basename($_SERVER['SCRIPT_NAME']) === 'perfil.php'): ?>
        <link rel="stylesheet" href="perfil.css">
        <?php endif; ?>
        
    </head>
    <body>

        <header>
            <img src="img/marco_superior.png" id="foto_top">

            <nav id="navbar">
                <section id="logo-container">
                    <a href="index.php"><img src="img/logo.png" id="logo-img"></a>
                </section>

                <section id="titulo">
                    <h1>
                        <a href="index.php">MUZEUS</a>
                    </h1>
                </section>

                <button id="hamburguesa-btn">☰</button>
                <section id="derecha">
                <?php if (isset($_SESSION['user_id'])): ?>
                <strong id="nombre-usuario">Usuario: <?php echo $respuesta['nombre'];?> <br>Saldo: $<?php echo $respuesta['saldo'];?></strong>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id']) && ($_SERVER['SCRIPT_NAME'] == "/index.php") || isset($_SESSION['user_id']) && ($_SERVER['SCRIPT_NAME'] == "/detalles.php")): ?>
                    <a href="subir.php" class="btn-nav">Vender</a>
                    <a href="perfil.php" class="btn-nav">Mi Perfil</a>
                    <a href="actions/logout.php" class="btn-nav">Salir</a>
                <?php elseif ($_SERVER['SCRIPT_NAME'] == "/subir.php"): ?>
                    <a href="perfil.php" class="btn-nav">Mi Perfil</a>
                    <a href="actions/logout.php" class="btn-nav">Salir</a>
                <?php elseif ($_SERVER['SCRIPT_NAME'] == "/perfil.php"): ?>
                    <a href="subir.php" class="btn-nav">Vender</a>
                    <a href="actions/logout.php" class="btn-nav">Salir</a>
                <?php elseif ($_SERVER['SCRIPT_NAME'] == "/login.php"): ?>
                    <a href="registro.php" class="btn-nav">Registrarse</a>
                <?php elseif ($_SERVER['SCRIPT_NAME'] == "/registro.php"): ?>
                    <a href="login.php" class="btn-nav">Entrar</a>
                <?php else: ?>
                    <a href="login.php" class="btn-nav">Entrar</a>
                    <a href="registro.php" class="btn-nav">Registrarse</a>
                    <?php endif; ?>
                </section>
            </nav>
        </header>

        <aside id="sidebar">
            <?php if (isset($_SESSION['user_id'])): ?>
                <strong id="nombre-usuario">Usuario: <?php echo $respuesta['nombre'];?> <br>Saldo: $<?php echo $respuesta['saldo'];?></strong>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id']) && ($_SERVER['SCRIPT_NAME'] == "/index.php") || isset($_SESSION['user_id']) && ($_SERVER['SCRIPT_NAME'] == "/detalles.php")): ?>
                <a href="subir.php" class="btn-nav">Vender</a>
                <a href="perfil.php" class="btn-nav">Mi Perfil</a>
                <a href="actions/logout.php" class="btn-nav">Salir</a>
            <?php elseif ($_SERVER['SCRIPT_NAME'] == "/subir.php"): ?>
                <a href="perfil.php" class="btn-nav">Mi Perfil</a>
                <a href="actions/logout.php" class="btn-nav">Salir</a>
            <?php elseif ($_SERVER['SCRIPT_NAME'] == "/perfil.php"): ?>
                <a href="subir.php" class="btn-nav">Vender</a>
                <a href="actions/logout.php" class="btn-nav">Salir</a>
            <?php elseif ($_SERVER['SCRIPT_NAME'] == "/login.php"): ?>
                <a href="registro.php" class="btn-nav">Registrarse</a>
            <?php elseif ($_SERVER['SCRIPT_NAME'] == "/registro.php"): ?>
                <a href="login.php" class="btn-nav">Entrar</a>
            <?php else: ?>
                <a href="login.php" class="btn-nav">Entrar</a>
                <a href="registro.php" class="btn-nav">Registrarse</a>
                <?php endif; ?>
        </aside>

        <aside id="colum_L">
            <img src="img/columna_izq.png" class="foto_columna">
        </aside>
