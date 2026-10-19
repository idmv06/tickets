<?php
session_start();
include 'conexion.php';
require_once 'csrf.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validar_csrf(); 
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena_plana = $_POST['contrasena'] ?? '';

    if ($contrasena_plana === '' || $nombre === '' || $correo === '') {
        $_SESSION['mensaje_error'] = "Todos los campos son obligatorios.";
        header("Location: admin_tickets.php");
        exit();
    }

    $contrasena = password_hash($contrasena_plana, PASSWORD_DEFAULT);
    $rol = 'soporte';
    $colegio_id = NULL; 

    // Verificar si el correo ya existe
    $sql_check = "SELECT usuario_id FROM usuarios WHERE correo = ?";
    $stmt_check = mysqli_prepare($conexion, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $correo);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $_SESSION['mensaje_error'] = "El correo electrónico ya está registrado.";
    } else {
        $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol, colegio_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $nombre, $correo, $contrasena, $rol, $colegio_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['mensaje_exito'] = "¡Administrador registrado correctamente!";
        } else {
            error_log("Error DB al registrar administrador: " . mysqli_error($conexion));
            $_SESSION['mensaje_error'] = "Ocurrió un error al registrar el administrador en el sistema.";
        }
        mysqli_stmt_close($stmt);
    }
    
    mysqli_stmt_close($stmt_check);
    mysqli_close($conexion);
    
    header("Location: admin_tickets.php");
    exit();
} else {
    header("Location: admin_tickets.php");
    exit();
}
?>