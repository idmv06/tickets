<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $password_ingresada = $_POST['password'];

    // anti SQL Injection
    $sql = "SELECT usuario_id, nombre, password, rol, colegio_id FROM usuarios WHERE correo = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $correo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($resultado)) {
        // password hash
        if (password_verify($password_ingresada, $user['password'])) {
            
            
            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $user['usuario_id'];
            $_SESSION['nombre']     = $user['nombre'];
            $_SESSION['rol']        = $user['rol'];
            $_SESSION['colegio_id'] = $user['colegio_id'];

            if ($user['rol'] === 'soporte') {
                header("Location: admin_tickets.php");
            } else {
                header("Location: mis_tickets.php");
            }
            exit();
        }
    }

    $error = "Credenciales incorrectas.";
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