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
    $contrasena_plana = $_POST['contrasena'] ?? '';

    if ($contrasena_plana === '') {
        $_SESSION['mensaje_error'] = "La contraseña es obligatoria.";
        header("Location: admin_tickets.php");
        exit();
    }

    // Contraseña encriptada de forma segura con Bcrypt
    $contrasena = password_hash($contrasena_plana, PASSWORD_DEFAULT);
    $rol = 'soporte';
    
    // Los administradores de soporte no pertenecen a un colegio específico (NULL)
    $colegio_id = NULL; 

    // Verificar si el correo ya existe en la base de datos
    $sql_check = "SELECT usuario_id FROM usuarios WHERE correo = ?";
    $stmt_check = mysqli_prepare($conexion, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $correo);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $_SESSION['mensaje_error'] = "El correo ya está registrado.";
    } else {
        // Insertar el nuevo administrador con consulta preparada
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
    
    // Redirigir de vuelta al panel de tickets
    header("Location: admin_tickets.php");
    exit();
}
?>