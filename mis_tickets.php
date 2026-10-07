<?php
session_start();
include 'conexion.php';

// Validar que el usuario esté logueado y NO sea admin de soporte
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] === 'soporte') {
    header("Location: login.html");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Consultar los tickets creados únicamente por este usuario
$sql = "SELECT t.*, c.nombre AS nombre_colegio 
        FROM tickets t
        INNER JOIN colegios c ON t.colegio_id = c.colegio_id
        WHERE t.usuario_id = ? 
        ORDER BY t.fecha_creacion DESC";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tickets de Soporte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    
    <style>
        html, body {
            min-height: 100vh;
            margin: 0;
            background-image: linear-gradient(to bottom, rgb(80, 4, 151), rgb(139, 20, 175)) !important;
            background-attachment: fixed;
        }
    </style>
    
</head>
<body class="bg-light">

    <!-- NAVBAR  -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">Panel de Soporte - Usuario</a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3"><i class="bi bi-person-fill"></i> Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                
                <a href="nuevo_ticket.php" class="btn btn-outline-light btn-sm fw-bold me-2"><i class="bi bi-plus-circle"></i> Nuevo Ticket</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4"><i class="bi bi-ticket-detailed-fill me-2"></i>Historial de Tickets Enviados</h4>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th># ID</th>
                                        <th>Asunto</th>
                                        <th>Institución</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($resultado) > 0) {
                                        while ($t = mysqli_fetch_assoc($resultado)) {
                                            // Colores según el estado
                                            $badgeBg = 'bg-secondary';
                                            if ($t['estado'] == 'Pendiente') $badgeBg = 'bg-warning text-dark';
                                            if ($t['estado'] == 'En Proceso') $badgeBg = 'bg-info text-dark';
                                            if ($t['estado'] == 'Resuelto') $badgeBg = 'bg-success';
                                            if ($t['estado'] == 'Cerrado') $badgeBg = 'bg-dark';

                                            echo "<tr>
                                                <td><strong>#{$t['ticket_id']}</strong></td>
                                                <td>" . htmlspecialchars($t['asunto']) . "</td>
                                                <td>" . htmlspecialchars($t['nombre_colegio']) . "</td>
                                                <td><span class='badge {$badgeBg}'>{$t['estado']}</span></td>
                                                <td><small class='text-muted'>{$t['fecha_creacion']}</small></td>
                                                <td class='text-center'>
                                                    <a href='ver_ticket_usuario.php?id={$t['ticket_id']}' class='btn btn-sm btn-outline-primary'>
                                                        <i class='bi bi-chat-left-text'></i> Ver / Responder
                                                    </a>
                                                </td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center text-muted py-4'>Aún no ha enviado ningún ticket de soporte.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>