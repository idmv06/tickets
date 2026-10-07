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
$usuario_id =$_SESSION['usuario_id'];

// Verificar que este ticket pertenezca al usuario logueado
$sql = "SELECT t.*, c.nombre AS nombre_colegio, u.nombre AS nombre_usuario 
        FROM tickets t
        INNER JOIN colegios c ON t.colegio_id = c.colegio_id
        INNER JOIN usuarios u ON t.usuario_id = u.usuario_id
        WHERE t.ticket_id = ? AND t.usuario_id = ?";
        
$stmt = mysqli_prepare($conexion,$sql);
mysqli_stmt_bind_param($stmt, "ii", $ticket_id,$usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$ticket = mysqli_fetch_assoc($resultado);

if (!$ticket) {
    die("Acceso denegado o el ticket no existe.");
}

// Procesar si el usuario envía una respuesta
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty(trim($_POST['mensaje']))) {$mensaje = trim($_POST['mensaje']);$es_interno = 0; // Los usuarios nunca pueden mandar notas internas

    $sql_com = "INSERT INTO comentarios_tickets (ticket_id, usuario_id, mensaje, es_interno) VALUES (?, ?, ?, ?)";
    $stmt_com = mysqli_prepare($conexion,$sql_com);
    mysqli_stmt_bind_param($stmt_com, "iisi", $ticket_id,$usuario_id, $mensaje,$es_interno);
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
$stmt_c = mysqli_prepare($conexion,$sql_comentarios);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="mis_tickets.php">
                <i class="bi bi-arrow-left"></i> Volver a Mis Tickets
            </a>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                
                <!-- Datos del Ticket -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold text-primary mb-0">Ticket #<?php echo $ticket['ticket_id']; ?>: <?php echo htmlspecialchars($ticket['asunto']); ?></h4>
                            <span class="badge bg-info text-dark fs-6"><?php echo $ticket['estado']; ?></span>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <p class="mb-1 text-muted fw-bold">Descripción Inicial de su Reporte:</p>
                            <div class="p-3 bg-light rounded border">
                                <?php echo nl2br(htmlspecialchars($ticket['descripcion'])); ?>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Fecha de envío: <?php echo $ticket['fecha_creacion']; ?></p>
                    </div>
                </div>

                <!-- Historial de Conversación -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-chat-dots-fill me-2 text-primary"></i>Conversación con Soporte</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php
                        if (mysqli_num_rows($res_comentarios) > 0) {
                            while ($com = mysqli_fetch_assoc($res_comentarios)) {
                                $esSoporte = ($com['rol_autor'] === 'soporte');
                                $claseCard =$esSoporte ? 'border-primary bg-light' : 'border-secondary';
                                $badgeRol =$esSoporte ? '<span class="badge bg-dark">Soporte Técnico</span>' : '<span class="badge bg-secondary">Usted</span>';
                                
                                echo "<div class='card mb-3 {$claseCard} shadow-sm'>
                                    <div class='card-body'>
                                        <div class='d-flex justify-content-between align-items-center mb-2'>
                                            <strong>" . htmlspecialchars($com['nombre_autor']) . " {$badgeRol}</strong>
                                            <small class='text-muted'>" . $com['fecha'] . "</small>
                                        </div>
                                        <p class='mb-0'>" . nl2br(htmlspecialchars($com['mensaje'])) . "</p>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<p class='text-muted text-center py-3'>Aún no hay respuestas de soporte para este ticket. Le responderemos pronto.</p>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Formulario para responder al admin -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-reply-fill me-2"></i>Enviar Mensaje a Soporte</h5>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="mensaje" class="form-label fw-bold">Su respuesta:</label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="3" placeholder="Escriba aquí si tiene más información o dudas..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary fw-bold">
                                <i class="bi bi-send-fill me-1"></i> Enviar Mensaje
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>