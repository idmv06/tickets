<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $correo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        
        if ($contrasena === $fila['contrasena']) {
            
            $_SESSION['usuario_id'] = $fila['usuario_id'];
            $_SESSION['nombre'] = $fila['nombre'];
            $_SESSION['rol'] = $fila['rol'];
            $_SESSION['colegio_id'] = $fila['colegio_id'];

            if ($fila['rol'] === 'soporte') {
                header("Location: admin_tickets.php");
                exit();
            } else {
                // REDIRECCIÓN CAMBIADA: Ahora los profesores van al historial por defecto
                header("Location: mis_tickets.php");
                exit();
            }

        } else {
            
            mostrarError("La contraseña ingresada es incorrecta. <a href='login.html' class='alert-link'>Intentar de nuevo</a>");
        }
    } else {
        
        mostrarError("El correo electrónico <strong>$correo</strong> no se encuentra registrado en el sistema. <a href='login.html' class='alert-link'>Verificar datos</a>");
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
}

function mostrarError($mensaje) {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Error de Autenticación</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light d-flex align-items-center vh-100">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="alert alert-danger shadow-sm p-4" role="alert">
                        <h4 class="alert-heading fw-bold">¡Atención!</h4>
                        <p class="mb-3">'.$mensaje.'</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
}
?>