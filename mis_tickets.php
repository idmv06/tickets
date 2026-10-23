<?php
session_start();
include 'conexion.php';

// Validar que el usuario haya iniciado sesión (profesor o administrativo)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Consultar los tickets creados por este usuario, trayendo también el nombre del agente asignado
$sql = "SELECT t.*, c.nombre AS nombre_colegio, us.nombre AS nombre_asignado 
        FROM tickets t
        INNER JOIN colegios c ON t.colegio_id = c.colegio_id
        LEFT JOIN usuarios us ON t.asignado_a = us.usuario_id
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
    <title>Mis Tickets - Panel de Usuario</title>
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
<body class="app-background"> 

    <nav class="navbar navbar-dark mb-4 p-3 border-bottom border-white border-opacity-10">
        <div class="container d-flex justify-content-between">
            <span class="navbar-brand fw-bold"><i class="bi bi-person-workspace me-2"></i>Portal de Usuario / Profesor</span>
            <div>
                <span class="me-3 text-white-50">Hola, <?php echo e($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm fw-bold"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row mb-4">
            <div class="col-md-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3 class="fw-bold mb-0"><i class="bi bi-ticket-detailed-fill me-2"></i>Mis Reportes y Solicitudes</h3>
                <a href="nuevo_ticket.php" class="btn btn-morado px-4">
                    <i class="bi bi-plus-circle-fill me-1"></i> Crear Nuevo Ticket
                </a>
            </div>
        </div>

        <div class="card card-glass shadow-sm">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-glass align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Asunto</th>
                                <th>Colegio</th>
                                <th>Estado</th>
                                <th>Atendido por (Soporte)</th>
                                <th>Fecha</th>
                                <th class="text-end">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($resultado) > 0): ?>
                                <?php while($t = mysqli_fetch_assoc($resultado)): ?>
                                    <tr>
                                        <td class="fw-bold">#<?php echo $t['ticket_id']; ?></td>
                                        <td><?php echo e($t['asunto']); ?></td>
                                        <td><?php echo e($t['nombre_colegio']); ?></td>
                                        <td>
                                            <?php 
                                                $bgBadge = 'bg-secondary';
                                                if($t['estado'] == 'Pendiente') $bgBadge = 'bg-warning text-dark';
                                                if($t['estado'] == 'En Proceso') $bgBadge = 'bg-info text-dark';
                                                if($t['estado'] == 'Resuelto') $bgBadge = 'bg-success';
                                                if($t['estado'] == 'Cerrado') $bgBadge = 'bg-dark';
                                            ?>
                                            <span class="badge <?php echo $bgBadge; ?>"><?php echo e($t['estado']); ?></span>
                                        </td>
                                        <td>
                                            <?php if(!empty($t['nombre_asignado'])): ?>
                                                <span class="badge bg-light text-dark"><i class="bi bi-headset me-1"></i><?php echo e($t['nombre_asignado']); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary text-white">Pendiente de asignación</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted-glass"><?php echo e($t['fecha_creacion']); ?></td>
                                        <td class="text-end">
                                            <a href="ver_ticket_usuario.php?id=<?php echo $t['ticket_id']; ?>" class="btn btn-morado btn-sm">
                                                <i class="bi bi-eye-fill me-1"></i> Ver Caso
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted-glass">Aún no has creado ningún ticket.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>