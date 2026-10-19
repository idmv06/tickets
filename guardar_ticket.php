<?php
session_start();
include 'conexion.php';

// Validar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $asunto = trim($_POST['asunto'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $prioridad = trim($_POST['prioridad'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $usuario_id = $_SESSION['usuario_id'];

    // 1. Whitelist Categoría y Prioridad
    $categorias_permitidas = [
        'Registro de Notas',
        'Control de Entradas y Salidas',
        'Módulo de Anuncios',
        'Problema General'
    ];
    $prioridades_permitidas = ['Baja', 'Media', 'Alta', 'Urgente'];

    if (!in_array($categoria, $categorias_permitidas, true) || !in_array($prioridad, $prioridades_permitidas, true)) {
        die("Error: Los datos enviados contienen una categoría o prioridad no válida.");
    }

    // 2. validación Colegio
    if (!isset($_SESSION['colegio_id']) || empty($_SESSION['colegio_id'])) {
        die("Error de seguridad: Su cuenta de usuario no tiene una institución asignada correctamente.");
    }
    $colegio_id = intval($_SESSION['colegio_id']);

    // Verificar en la BD que el colegio exista
    $sql_val_col = "SELECT colegio_id FROM colegios WHERE colegio_id = ?";
    $stmt_col = mysqli_prepare($conexion, $sql_val_col);
    mysqli_stmt_bind_param($stmt_col, "i", $colegio_id);
    mysqli_stmt_execute($stmt_col);
    mysqli_stmt_store_result($stmt_col);

    if (mysqli_stmt_num_rows($stmt_col) === 0) {
        mysqli_stmt_close($stmt_col);
        die("Error: La institución asignada a su cuenta no existe en la base de datos.");
    }
    mysqli_stmt_close($stmt_col);

    // 3. CAMPOS OBLIGATORIOS
    if ($asunto === '' || $descripcion === '') {
        die("Error: El asunto y la descripción son obligatorios.");
    }

    $nombre_archivo_final = NULL;

    // 4. PROCESAR ARCHIVO ADJUNTO
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $tmp_name  = $_FILES['archivo']['tmp_name'];
        $file_size = $_FILES['archivo']['size'];

        // Límite de 5MB
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
                
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0755, true);
                }

                $random_name = bin2hex(random_bytes(16));
                $nombre_archivo_final = $random_name . '.' . $ext;
                
                $destino = 'uploads/' . $nombre_archivo_final;
                move_uploaded_file($tmp_name, $destino);
            } else {
                die("Error: El tipo de archivo adjunto no está permitido.");
            }
        } else {
            die("Error: El archivo excede el tamaño máximo permitido de 5MB.");
        }
    }

    // INSERCIÓN SEGURA CON CONSULTA PREPARADA
    $sql = "INSERT INTO tickets (colegio_id, usuario_id, categoria, prioridad, estado, asunto, descripcion, archivo_adjunto) 
            VALUES (?, ?, ?, ?, 'Pendiente', ?, ?, ?)";
    
    $stmt = mysqli_prepare($conexion, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iisssss", $colegio_id, $usuario_id, $categoria, $prioridad, $asunto, $descripcion, $nombre_archivo_final);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conexion);
            mostrarExito();
            exit();
        } else {
            $error_db = mysqli_error($conexion);
            mysqli_stmt_close($stmt);
            mysqli_close($conexion);
            die("Error al ejecutar la consulta: " . $error_db);
        }
    } else {
        $error_prep = mysqli_error($conexion);
        mysqli_close($conexion);
        die("Error al preparar la consulta: " . $error_prep);
    }
}

function mostrarExito() {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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