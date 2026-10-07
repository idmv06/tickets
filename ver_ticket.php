<?php
session_start();
include 'conexion.php';

// Validar que el usuario sea de soporte
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: login.html");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_tickets.php");
    exit();
}

$ticket_id = intval($_GET['id']);

// Procesar formulario si el admin envía una respuesta o cambia el estado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Cambiar estado si viene en el POST
    if (isset($_POST['nuevo_estado'])) {
        $nuevo_estado = $_POST['nuevo_estado'];
        $update_sql = "UPDATE tickets SET estado = ? WHERE ticket_id = ?";
        $stmt_up = mysqli_prepare($conexion, $update_sql);
        mysqli_stmt_bind_param($stmt_up, "si", $nuevo_estado, $ticket_id);
        mysqli_stmt_execute($stmt_up);
        mysqli_stmt_close($stmt_up);
    }
    
    // Guardar respuesta/comentario si escribieron algo
    if (!empty(trim($_POST['mensaje']))) {
        $mensaje = trim($_POST['mensaje']);
        $usuario_id = $_SESSION['usuario_id'];
        $es_interno = isset($_POST['es_interno']) ? 1 : 0;

        // Usando los nombres exactos de sus columnas
        $sql_com = "INSERT INTO comentarios_tickets (ticket_id, usuario_id, mensaje, es_interno) VALUES (?, ?, ?, ?)";
        $stmt_com = mysqli_prepare($conexion, $sql_com);
        mysqli_stmt_bind_param($stmt_com, "iisi", $ticket_id, $usuario_id, $mensaje, $es_interno);
        mysqli_stmt_execute($stmt_com);
        mysqli_stmt_close($stmt_com);
    }

    header("Location: ver_ticket.php?id=" . $ticket_id);
    exit();
}

// Consultar los datos del ticket, colegio y usuario remitente
$sql = "SELECT t.*, c.nombre AS nombre_colegio, u.nombre AS nombre_usuario, u.correo AS correo_usuario 
        FROM tickets t
        INNER JOIN colegios c ON t.colegio_id = c.colegio_id
        INNER JOIN usuarios u ON t.usuario_id = u.usuario_id
        WHERE t.ticket_id = ?";
        
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "i", $ticket_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$ticket = mysqli_fetch_assoc($resultado);

if (!$ticket) {
    die("El ticket solicitado no existe.");
}

// Consultar el historial de comentarios usando 'fecha'
$sql_comentarios = "SELECT c.*, u.nombre AS nombre_autor, u.rol AS rol_autor 
                    FROM comentarios_tickets c
                    INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
                    WHERE c.ticket_id = ?
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
    <title>Atender Ticket #<?php echo $ticket['ticket_id']; ?></title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Estilos de la temática Morada y Glassmorphism -->
    <style>
        html, body {
            min-height: 100vh;
            margin: 0;
            background-image: linear-gradient(to bottom, rgb(80, 4, 151), rgb(139, 20, 175)) !important;
            background-attachment: fixed;
            color: #ffffff; /* Texto por defecto en blanco */
        }

        /* Tarjetas con efecto Cristal (Glassmorphism) */
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

        /* Ajustes de texto para que resalte sobre el cristal */
        .text-muted-glass {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        /* Estilos de los comentarios */
        .comentario-interno {
            background-color: rgba(255, 193, 7, 0.2) !important; /* Tono amarillo semitransparente */
            border: 1px solid rgba(255, 193, 7, 0.5) !important;
        }
        .comentario-soporte {
            background-color: rgba(255, 255, 255, 0.2) !important; /* Tono blanco semitransparente */
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
        }
        .comentario-usuario {
            background-color: rgba(0, 0, 0, 0.2) !important; /* Tono oscuro semitransparente */
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
        }

        /* Inputs y textareas */
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border: none;
        }

        /* Botón estilo morado (Blanco que se vuelve morado al pasar el mouse) */
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

        <!--- ESTILOS EXCLUSIVOS PARA IMPRESIÓN (PDF) --->
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
            /* Ocultar elementos que no van en el PDF */
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
            <a class="btn btn-outline-light btn-sm fw-bold" href="admin_tickets.php">
                <i class="bi bi-arrow-left"></i> Volver a la Bandeja
            </a>
            
            <!-- botón de impresión -->
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
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted-glass fw-bold">Institución:</p>
                                <p class="fs-5 mb-0"><?php echo htmlspecialchars($ticket['nombre_colegio']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted-glass fw-bold">Reportado por:</p>
                                <p class="fs-5 mb-0"><?php echo htmlspecialchars($ticket['nombre_usuario']); ?></p>
                                <small class="text-muted-glass"><?php echo htmlspecialchars($ticket['correo_usuario']); ?></small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1 text-muted-glass fw-bold">Descripción Inicial:</p>
                            <!-- Caja de descripción  -->
                            <div class="p-3 rounded border" style="background-color: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.2) !important;">
                                <?php echo nl2br(htmlspecialchars($ticket['descripcion'])); ?>
                            </div>
                        </div>
                        <p class="text-muted-glass small mb-0">Fecha de envío: <?php echo $ticket['fecha_creacion']; ?></p>
                    </div>
                </div>

                <!-- Historial de Conversación / Comentarios -->
                <div class="card card-glass shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-chat-dots-fill me-2"></i>Historial de Mensajes y Notas</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php
                        if (mysqli_num_rows($res_comentarios) > 0) {
                            while ($com = mysqli_fetch_assoc($res_comentarios)) {
                                $esSoporte = ($com['rol_autor'] === 'soporte');
                                $esInterno = ($com['es_interno'] == 1);
                                
                                // Diseño de los comentarios
                                if ($esInterno) {
                                    $claseCard = 'comentario-interno';
                                    $badgeRol = '<span class="badge bg-warning text-dark">Nota Interna (Privada)</span>';
                                } else {
                                    $claseCard = $esSoporte ? 'comentario-soporte' : 'comentario-usuario';
                                    $badgeRol = $esSoporte ? '<span class="badge bg-light text-dark">Soporte Técnico</span>' : '<span class="badge bg-secondary">Profesor / Colegio</span>';
                                }
                                
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
                            echo "<p class='text-muted-glass text-center py-3'>Aún no hay comentarios en este ticket.</p>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Formulario (Se oculta al imprimir) -->
                <div class="card card-glass shadow-sm ocultar-al-imprimir">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-reply-fill me-2"></i>Responder o Agregar Nota</h5>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="mensaje" class="form-label fw-bold">Mensaje:</label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="3" placeholder="Escriba su mensaje aquí..."></textarea>
                            </div>

                            <!-- Checkbox -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="es_interno" name="es_interno" value="1">
                                <label class="form-check-label text-muted-glass" for="es_interno">Marcar como nota interna (solo visible para el equipo de soporte)</label>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="nuevo_estado" class="form-label fw-bold">Cambiar Estado del Ticket:</label>
                                    <select class="form-select" id="nuevo_estado" name="nuevo_estado">
                                        <option value="Pendiente" <?php if($ticket['estado']=='Pendiente') echo 'selected'; ?>>Pendiente</option>
                                        <option value="En Proceso" <?php if($ticket['estado']=='En Proceso') echo 'selected'; ?>>En Proceso</option>
                                        <option value="Resuelto" <?php if($ticket['estado']=='Resuelto') echo 'selected'; ?>>Resuelto</option>
                                        <option value="Cerrado" <?php if($ticket['estado']=='Cerrado') echo 'selected'; ?>>Cerrado</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-morado fw-bold px-4 py-2">
                                <i class="bi bi-send-fill me-1"></i> Enviar Mensaje y Actualizar
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
