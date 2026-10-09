<?php
// seguridad: Solo aplicar si la sesión NO ha iniciado todavía
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Strict');

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
}

// Función global contra ataques XSS
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

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