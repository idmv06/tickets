<?php

$servidor = getenv('DB_HOST') ?: 'localhost';
$puerto = getenv('DB_PORT') ?: '3306';
$usuario = getenv('DB_USER') ?: 'root';
$clave = getenv('DB_PASS') ?: '';
$base_datos = getenv('DB_NAME') ?: 'sistema_tickets';

$conexion = mysqli_connect(
    $servidor,
    $usuario,
    $clave,
    $base_datos,
    (int)$puerto
);

if (!$conexion) {
    die("Error de conexión a la base de datos.");
}

mysqli_set_charset($conexion, "utf8mb4");
?>
