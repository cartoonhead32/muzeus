<?php
    require_once __DIR__ . '/../vendor/autoload.php';
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad();

    try {
        $dotenv->load();
    } catch (Exception $e) {
        die("Error cargando .env: " . $e->getMessage()); 
    }

    $host = $_ENV['DB_HOST'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    $db = $_ENV['DB_NAME'];

    date_default_timezone_set('UTC');
    try {
        $conexion = new mysqli($host, $user, $pass, $db);
    } catch (mysqli_sql_exception $e) {
        echo $e->getMessage();
        exit;
    }
    $conexion->query("SET time_zone = '+00:00'");
    $conexion->set_charset("utf8mb4");
?>