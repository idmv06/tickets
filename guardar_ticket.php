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

    // Validación por si llegan vacíos
    if (empty($asunto) || empty($descripcion)) {
        die("Error: El asunto y la descripción son obligatorios.");
    }

    // Consulta SQL para insertar el ticket
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
                <div class="col-md-6">
                    <div class="card card-glass shadow-lg border-0 p-4 rounded-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-check-circle-fill text-success display-4"></i>
                            </div>
                            <h3 class="fw-bold mb-3 text-white">¡Ticket enviado con éxito!</h3>
                            <p class="text-muted-glass mb-4">Su solicitud ha sido registrada correctamente. El equipo de soporte técnico la revisará pronto.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="nuevo_ticket.php" class="btn btn-morado px-4">Crear otro ticket</a>
                                <a href="mis_tickets.php" class="btn btn-outline-light px-4">Ver mis tickets</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
}
?>
