<?php
session_start();
include 'conexion.php';

// Validar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y limpiar los datos del formulario
    $asunto = trim($_POST['asunto']);
    $categoria = $_POST['categoria'];
    $prioridad = $_POST['prioridad'];
    $descripcion = trim($_POST['descripcion']);
    
    // datos de la sesión actual
    $usuario_id = $_SESSION['usuario_id'];
    $colegio_id = $_SESSION['colegio_id'];

    // Validación básica por si llegan vacíos
    if (empty($asunto) || empty($descripcion)) {
        die("Error: El asunto y la descripción son obligatorios.");
    }

    // Consulta SQL preparada para insertar el ticket
    $sql = "INSERT INTO tickets (colegio_id, usuario_id, categoria, prioridad, estado, asunto, descripcion) 
            VALUES (?, ?, ?, ?, 'Pendiente', ?, ?)";
    
    $stmt = mysqli_prepare($conexion, $sql);
    
    // "iissis" significa: integer, integer, string, string, string, string
    mysqli_stmt_bind_param($stmt, "iissss", $colegio_id, $usuario_id, $categoria, $prioridad, $asunto, $descripcion);

    if (mysqli_stmt_execute($stmt)) {
        // Redirigir con éxito o mostrar una pantalla de confirmación
        mostrarExito();
    } else {
        echo "Error al guardar el ticket en la base de datos: " . mysqli_error($conexion);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
}

// mensaje de éxito
function mostrarExito() {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Ticket Enviado</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light d-flex align-items-center vh-100">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 p-4">
                        <div class="card-body">
                            <h3 class="text-success fw-bold mb-3">¡Ticket enviado con éxito!</h3>
                            <p class="text-muted mb-4">Su solicitud ha sido registrada correctamente. El equipo de soporte técnico la revisará pronto.</p>
                            <a href="nuevo_ticket.php" class="btn btn-primary me-2">Crear otro ticket</a>
                            <a href="logout.php" class="btn btn-outline-secondary">Cerrar Sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
}
?>