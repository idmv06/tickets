<?php
session_start();
include 'conexion.php';

// Validar que el usuario esté logueado y NO sea soporte
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] === 'soporte') {
    header("Location: login.html");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: mis_tickets.php");
    exit();
}

$ticket_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'];

// Verificar que este ticket pertenezca al usuario logueado
$sql = "SELECT t.*, c.nombre AS nombre_colegio, u.nombre AS nombre_usuario 
        FROM tickets t
        INNER JOIN colegios c ON t.colegio_id = c.colegio_id
        INNER JOIN usuarios u ON t.usuario_id = u.usuario_id
        WHERE t.ticket_id = ? AND t.usuario_id = ?";
        
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "ii", $ticket_id, $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$ticket = mysqli_fetch_assoc($resultado);

if (!$ticket) {
    die("Acceso denegado o el ticket no existe.");
}

// Procesar si el usuario envía una respuesta
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty(trim($_POST['mensaje']))) {
    $mensaje = trim($_POST['mensaje']);
    $es_interno = 0; // Los usuarios nunca pueden mandar notas internas

    $sql_com = "INSERT INTO comentarios_tickets (ticket_id, usuario_id, mensaje, es_interno) VALUES (?, ?, ?, ?)";
    $stmt_com = mysqli_prepare($conexion, $sql_com);
    mysqli_stmt_bind_param($stmt_com, "iisi", $ticket_id, $usuario_id, $mensaje, $es_interno);
    mysqli_stmt_execute($stmt_com);
    mysqli_stmt_close($stmt_com);

    header("Location: ver_ticket_usuario.php?id=" . $ticket_id);
    exit();
}

// Consultar el historial de comentarios omitiendo las notas internas
$sql_comentarios = "SELECT c.*, u.nombre AS nombre_autor, u.rol AS rol_autor 
                    FROM comentarios_tickets c
                    INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
                    WHERE c.ticket_id = ? AND c.es_interno = 0
                    ORDER BY c.fecha ASC";
$stmt_c = mysqli_prepare($conexion, $sql_comentarios);
mysqli_stmt_bind_param($stmt_c, "i", $ticket_id);
mysqli_stmt_execute($stmt_c);
$res_comentarios = mysqli_stmt_get_result($stmt_c);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Ticket #<?php echo $ticket['ticket_id']; ?></title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos -->
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

        .card-glass .card-header {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .text-muted-glass {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .comentario-soporte {
            background-color: rgba(255, 255, 255, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
        }
        .comentario-usuario {
            background-color: rgba(0, 0, 0, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border: none;
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

        /* --- estilos de la impresión --- */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .card-glass {
                background-color: #ffffff !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                border: 1px solid #dee2e6 !important;
                color: #000000 !important;
                box-shadow: none !important;
            }
            .text-muted-glass, .text-white-50 {
                color: #6c757d !important;
            }
            .text-white {
                color: #000000 !important;
            }
            .ocultar-al-imprimir {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar (Se oculta al imprimir) -->
    <nav class="navbar navbar-dark mb-4 ocultar-al-imprimir">
        <div class="container d-flex justify-content-between">
            <a class="btn btn-outline-light btn-sm fw-bold" href="mis_tickets.php">
                <i class="bi bi-arrow-left"></i> Volver a Mis Tickets
            </a>
            
            <button onclick="window.print()" class="btn btn-outline-light btn-sm fw-bold">
                <i class="bi bi-printer-fill me-1"></i> Imprimir / PDF
            </button>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                
                <!-- Datos del Ticket -->
                <div class="card card-glass shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold mb-0">Ticket #<?php echo $ticket['ticket_id']; ?>: <?php echo htmlspecialchars($ticket['asunto']); ?></h4>
                            <span class="badge bg-info text-dark fs-6"><?php echo $ticket['estado']; ?></span>
                        </div>
                        <hr style="border-color: rgba(255,255,255,0.2);">
                        <div class="mb-3">
                            <p class="mb-1 text-muted-glass fw-bold">Descripción Inicial de su Reporte:</p>
                            <div class="p-3 rounded border" style="background-color: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.2) !important;">
                                <?php echo nl2br(htmlspecialchars($ticket['descripcion'])); ?>
                            </div>
                        </div>
                        <p class="text-muted-glass small mb-0">Fecha de envío: <?php echo $ticket['fecha_creacion']; ?></p>
                    </div>
                </div>
                
                <!-- Visualización de Archivo Adjunto (si existe) -->
                 <?php if (!empty($ticket['archivo_adjunto'])): ?>
                    <?php 
                        $ruta_archivo = 'uploads/' . htmlspecialchars($ticket['archivo_adjunto']);
                        $extension = strtolower(pathinfo($ticket['archivo_adjunto'], PATHINFO_EXTENSION));
                        $es_imagen = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                    ?>
                    <div class="mt-3">
                    <p class="mb-1 text-muted-glass fw-bold"><i class="bi bi-paperclip me-1"></i> Archivo Adjunto:</p>
                    <?php if ($es_imagen): ?>
                    <!-- Vista previa de imagen -->
                 <div class="mb-2">
                        <a href="<?php echo $ruta_archivo; ?>" target="_blank">
                        <img src="<?php echo $ruta_archivo; ?>" class="img-fluid rounded border border-white-50 shadow-sm" style="max-height: 250px;" alt="Captura adjunta">
                        </a>
                 </div>
                    <?php endif; ?>
                    <a href="<?php echo $ruta_archivo; ?>" download class="btn btn-sm btn-outline-light">
                    <i class="bi bi-download me-1"></i> Descargar <?php echo htmlspecialchars($ticket['archivo_adjunto']); ?>
                    </a>
                 </div>
                <?php endif; ?>

                <!-- Historial de Conversación -->
                <div class="card card-glass shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-chat-dots-fill me-2"></i>Conversación con Soporte</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php
                        if (mysqli_num_rows($res_comentarios) > 0) {
                            while ($com = mysqli_fetch_assoc($res_comentarios)) {
                                $esSoporte = ($com['rol_autor'] === 'soporte');
                                $claseCard = $esSoporte ? 'comentario-soporte' : 'comentario-usuario';
                                $badgeRol = $esSoporte ? '<span class="badge bg-light text-dark">Soporte Técnico</span>' : '<span class="badge bg-secondary">Usted</span>';
                                
                                echo "<div class='card mb-3 {$claseCard} shadow-sm rounded-4'>
                                    <div class='card-body'>
                                        <div class='d-flex justify-content-between align-items-center mb-2'>
                                            <strong>" . htmlspecialchars($com['nombre_autor']) . " {$badgeRol}</strong>
                                            <small class='text-white-50'>" . $com['fecha'] . "</small>
                                        </div>
                                        <p class='mb-0'>" . nl2br(htmlspecialchars($com['mensaje'])) . "</p>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<p class='text-muted-glass text-center py-3'>Aún no hay respuestas de soporte para este ticket. Le responderemos pronto.</p>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Formulario para responder al admin (Se oculta al imprimir) -->
                <div class="card card-glass shadow-sm ocultar-al-imprimir">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-reply-fill me-2"></i>Enviar Mensaje a Soporte</h5>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="mensaje" class="form-label fw-bold">Su respuesta:</label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="3" placeholder="Escriba aquí si tiene más información o dudas..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-morado fw-bold px-4 py-2">
                                <i class="bi bi-send-fill me-1"></i> Enviar Mensaje
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
