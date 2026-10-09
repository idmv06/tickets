<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $asunto = trim($_POST['asunto']);
    $categoria = $_POST['categoria'];
    $prioridad = $_POST['prioridad'];
    $descripcion = trim($_POST['descripcion']);
    
    $usuario_id = $_SESSION['usuario_id'];
    $colegio_id = $_SESSION['colegio_id'];

    if (empty($asunto) || empty($descripcion)) {
        die("Error: El asunto y la descripción son obligatorios.");
    }

    $nombre_archivo_final = NULL;

    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $tmp_name  = $_FILES['archivo']['tmp_name'];
        $file_size = $_FILES['archivo']['size'];

        // 5MB limit
        if ($file_size <= 5242880) {
            
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $tmp_name);
            finfo_close($finfo);

            $allowed_mimes = [
                'image/jpeg'      => 'jpg',
                'image/png'       => 'png',
                'image/gif'       => 'gif',
                'application/pdf' => 'pdf',
                'application/zip' => 'zip'
            ];

            if (array_key_exists($mime, $allowed_mimes)) {
                $ext = $allowed_mimes[$mime];
                
                $random_name = bin2hex(random_bytes(16));
                $nombre_archivo_final = $random_name . '.' . $ext;
                
                $destino = 'uploads/' . $nombre_archivo_final;
                move_uploaded_file($tmp_name, $destino);
            }
        }
    }

    // Prepared SQL query
    $sql = "INSERT INTO tickets (colegio_id, usuario_id, categoria, prioridad, estado, asunto, descripcion, archivo_adjunto) 
            VALUES (?, ?, ?, ?, 'Pendiente', ?, ?, ?)";
    
    $stmt = mysqli_prepare($conexion, $sql);
    
    // Exactly 7 variables = "iisssss"
    mysqli_stmt_bind_param($stmt, "iisssss", $colegio_id, $usuario_id, $categoria, $prioridad, $asunto, $descripcion, $nombre_archivo_final);

    if (mysqli_stmt_execute($stmt)) {
        mostrarExito();
    } else {
        echo "Error: " . mysqli_error($conexion);
    }

    mysqli_stmt_close($stmt);
    } else {
        echo "Error al guardar el ticket en la base de datos: " . mysqli_error($conexion);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
}

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
