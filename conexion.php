<?php
$servidor = "localhost";
$usuario = "root";       
$clave = "";             
$base_datos = "sistema_tickets";

$conexion = mysqli_connect($servidor, $usuario, $clave, $base_datos);

if (!$conexion) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");
?>