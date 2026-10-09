<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // El '??' evita los errores si el dato llega vacío o no existe
    $correo = trim($_POST['correo'] ?? '');
    
    $password_ingresada = $_POST['password'] ?? $_POST['contrasena'] ?? '';
    if (empty($correo) || empty($password_ingresada)) {
        mostrarError("Faltan datos. Por favor ingrese su correo y contraseña.");
    }

    // SQL Injection prevenido
    $sql = "SELECT usuario_id, nombre, contrasena, rol, colegio_id FROM usuarios WHERE correo = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $correo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($resultado)) {
        // Verificación con hash usando la columna $user['contrasena']
        if (password_verify($password_ingresada, $user['contrasena'])) {
            
            // Prevenir fijación de sesión
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

    // Si falló el correo o la contraseña, mostramos el error
    mostrarError("Correo o contraseña incorrectos. Por favor intente de nuevo.");
}

function mostrarError($mensaje) {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error de Autenticación</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <style>
            html, body {
                min-height: 100vh;
                margin: 0;
                background-image: linear-gradient(to bottom, rgb(80, 4, 151), rgb(139, 20, 175)) !important;
                background-attachment: fixed;
                color: #ffffff;
            }
            .card-glass {
                background-color: rgba(255, 255, 255, 0.15) !important;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3) !important;
                color: #ffffff;
            }
            .text-muted-glass {
                color: rgba(255, 255, 255, 0.8) !important;
            }
            .btn-morado {
                background-color: #ffffff !important;
                border-color: #ffffff !important;    
                color: rgb(139, 20, 175) !important;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            .btn-morado:hover {
                background-color: rgb(80, 4, 151) !important;
                border-color: rgb(80, 4, 151) !important;
                color: #ffffff !important;
            }
        </style>
    </head>
    <body class="d-flex align-items-center vh-100">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card card-glass shadow-lg border-0 p-4 rounded-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-exclamation-triangle-fill text-warning display-4"></i>
                            </div>
                            <h4 class="fw-bold mb-3 text-white">Error de Autenticación</h4>
                            <p class="text-muted-glass mb-4">'.$mensaje.'</p>
                            <a href="login.html" class="btn btn-morado px-4 py-2">Volver a intentar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
    exit();
}
?>