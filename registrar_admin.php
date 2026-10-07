<?php
session_start();
include 'conexion.php';

// Validar que solo un usuario de soporte pueda acceder a este archivo
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']); 
    $rol = 'soporte';
    
    // Si los admins no pertenecen a un colegio, asume NULL. 
    $colegio_id = NULL; 

    // Verificar si el correo ya existe
    $sql_check = "SELECT usuario_id FROM usuarios WHERE correo = ?";
    $stmt_check = mysqli_prepare($conexion, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $correo);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        // El correo ya existe
        $_SESSION['mensaje_error'] = "El correo ya está registrado.";
    } else {
        // Insertar el nuevo admin
        $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol, colegio_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $nombre, $correo, $contrasena, $rol, $colegio_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['mensaje_exito'] = "Administrador registrado correctamente.";
        } else {
            $_SESSION['mensaje_error'] = "Error al registrar: " . mysqli_error($conexion);
        }
        mysqli_stmt_close($stmt);
    }
    
    mysqli_stmt_close($stmt_check);
    mysqli_close($conexion);
    
    // Redirigir de vuelta al panel
    header("Location: admin_tickets.php");
    exit();
}
?>