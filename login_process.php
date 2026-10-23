<?php
// Configurar parámetros seguros de cookies antes de session_start()
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,      // Solo viaja por HTTPS 
    'httponly' => true,    // Inaccesible para JavaScript
    'samesite' => 'Lax'    // Protección contra CSRF en navegación cruzada
]);

session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correo = trim($_POST['correo'] ?? '');
    $password_ingresada = $_POST['password'] ?? $_POST['contrasena'] ?? '';
    
    if (empty($correo) || empty($password_ingresada)) {
        mostrarError("Faltan datos. Por favor ingrese su correo y contraseña.");
    }

    // Consultar usuario incluyendo intentos y bloqueo numérico
    $sql = "SELECT usuario_id, nombre, correo, contrasena, rol, colegio_id, intentos_fallidos, bloqueado_hasta FROM usuarios WHERE correo = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $correo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    if ($user) {
        $tiempo_actual = time();

        // Verificar si la cuenta está bloqueada temporalmente
        if ($user['bloqueado_hasta'] && $tiempo_actual < $user['bloqueado_hasta']) {
            $segundos_restantes = $user['bloqueado_hasta'] - $tiempo_actual;
            $minutos_restantes = ceil($segundos_restantes / 60);
            mostrarError("Demasiados intentos fallidos. Cuenta bloqueada temporalmente. Intente de nuevo en {$minutos_restantes} minuto(s).");
        }

        // Verificación de contraseña con hash
        if (password_verify($password_ingresada, $user['contrasena'])) {
            
            // Éxito: Resetear contador de intentos fallidos y limpiar bloqueo
            $sql_reset = "UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE usuario_id = ?";
            $stmt_reset = mysqli_prepare($conexion, $sql_reset);
            mysqli_stmt_bind_param($stmt_reset, "i", $user['usuario_id']);
            mysqli_stmt_execute($stmt_reset);
            mysqli_stmt_close($stmt_reset);

            // Prevenir fijación de sesión
            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $user['usuario_id'];
            $_SESSION['nombre']     = $user['nombre'];
            $_SESSION['rol']        = $user['rol'];
            $_SESSION['colegio_id'] = $user['colegio_id'];

            mysqli_close($conexion);

            if ($user['rol'] === 'soporte') {
                header("Location: admin_tickets.php");
            } else {
                header("Location: mis_tickets.php");
            }
            exit();

        } else {
            // Incrementar intentos fallidos
            $intentos = intval($user['intentos_fallidos']) + 1;

            if ($intentos >= 5) {
                // Bloquear por exactamente 15 minutos (900 segundos a partir de ahora)
                $bloqueado_hasta = $tiempo_actual + 900; 
                $sql_fail = "UPDATE usuarios SET intentos_fallidos = ?, bloqueado_hasta = ? WHERE usuario_id = ?";
                $stmt_fail = mysqli_prepare($conexion, $sql_fail);
                mysqli_stmt_bind_param($stmt_fail, "iii", $intentos, $bloqueado_hasta, $user['usuario_id']);
            } else {
                $sql_fail = "UPDATE usuarios SET intentos_fallidos = ? WHERE usuario_id = ?";
                $stmt_fail = mysqli_prepare($conexion, $sql_fail);
                mysqli_stmt_bind_param($stmt_fail, "ii", $intentos, $user['usuario_id']);
            }

            mysqli_stmt_execute($stmt_fail);
            mysqli_stmt_close($stmt_fail);
        }
    }

    mysqli_close($conexion);
    // Mensaje genérico por seguridad
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
        <link rel="stylesheet" href="app.css">
        <style>
            html, body {
                min-height: 100vh;
                margin: 0;
            }
        </style>
    </head>
    <body class="d-flex align-items-center justify-content-center min-vh-100 app-background">
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