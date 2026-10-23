<?php
session_start();
include 'conexion.php';

// Validar que el usuario sea de soporte (admin)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: login.html");
    exit();
}

// Estadísticas Generales
$sql_totales = "SELECT 
    (SELECT COUNT(*) FROM tickets) AS total_tickets,
    (SELECT COUNT(*) FROM tickets WHERE estado = 'Pendiente') AS total_pendientes,
    (SELECT COUNT(*) FROM tickets WHERE estado = 'En Proceso') AS total_proceso,
    (SELECT COUNT(*) FROM tickets WHERE estado = 'Resuelto') AS total_resueltos,
    (SELECT COUNT(*) FROM tickets WHERE estado = 'Cerrado') AS total_cerrados";
$res_totales = mysqli_query($conexion, $sql_totales);
$totales = mysqli_fetch_assoc($res_totales);

// Colegios con más solicitudes
$sql_colegios = "SELECT c.nombre AS colegio, COUNT(t.ticket_id) AS total 
                 FROM tickets t 
                 INNER JOIN colegios c ON t.colegio_id = c.colegio_id 
                 GROUP BY t.colegio_id, c.nombre 
                 ORDER BY total DESC LIMIT 5";
$res_colegios = mysqli_query($conexion, $sql_colegios);

// Categorías / Problemas más frecuentes
$sql_categorias = "SELECT categoria, COUNT(ticket_id) AS total 
                   FROM tickets 
                   GROUP BY categoria 
                   ORDER BY total DESC";
$res_categorias = mysqli_query($conexion, $sql_categorias);

// Carga de trabajo y rendimiento por Agente de Soporte
$sql_agentes = "SELECT us.nombre AS admin, COUNT(t.ticket_id) AS total_asignados,
                SUM(CASE WHEN t.estado = 'En Proceso' THEN 1 ELSE 0 END) AS en_proceso,
                SUM(CASE WHEN t.estado = 'Resuelto' THEN 1 ELSE 0 END) AS resueltos,
                SUM(CASE WHEN t.estado = 'Cerrado' THEN 1 ELSE 0 END) AS cerrados
                FROM tickets t 
                INNER JOIN usuarios us ON t.asignado_a = us.usuario_id 
                WHERE t.asignado_a IS NOT NULL 
                GROUP BY t.asignado_a, us.nombre 
                ORDER BY total_asignados DESC";
$res_agentes = mysqli_query($conexion, $sql_agentes);

// Usuarios / Profesores que más reportan
$sql_usuarios = "SELECT u.nombre AS usuario, c.nombre AS colegio, COUNT(t.ticket_id) AS total 
                 FROM tickets t 
                 INNER JOIN usuarios u ON t.usuario_id = u.usuario_id 
                 INNER JOIN colegios c ON t.colegio_id = c.colegio_id 
                 GROUP BY t.usuario_id, u.nombre, c.nombre 
                 ORDER BY total DESC LIMIT 5";
$res_usuarios = mysqli_query($conexion, $sql_usuarios);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes y Analíticas - Soporte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="app.css">
    <style>
        html, body {
            min-height: 100vh;
            margin: 0;
        }
        .stat-card {
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="app-background">

    <!-- Navbar -->
    <nav class="navbar navbar-dark mb-4 p-3 border-bottom border-white border-opacity-10">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="navbar-brand fw-bold"><i class="bi bi-graph-up-arrow me-2"></i>Módulo de Reportes Analíticos</span>
            <div>
                <a href="admin_tickets.php" class="btn btn-outline-light btn-sm fw-bold me-2">
                    <i class="bi bi-arrow-left me-1"></i> Volver a la Bandeja
                </a>
                <a href="logout.php" class="btn btn-outline-light btn-sm fw-bold"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        
        <!-- Tarjetas de Resumen General (5 Columnas) -->
        <div class="row g-3 mb-4">
            <div class="col-md-2-4 col-sm-6 col-12" style="flex: 0 0 auto; width: 20%;">
                <div class="card card-glass shadow-sm p-3 stat-card text-center">
                    <div class="text-muted-glass small fw-bold text-uppercase">Total</div>
                    <div class="display-6 fw-bold my-2"><?php echo $totales['total_tickets']; ?></div>
                    <i class="bi bi-ticket-detailed display-6 opacity-50"></i>
                </div>
            </div>
            <div class="col-md-2-4 col-sm-6 col-12" style="flex: 0 0 auto; width: 20%;">
                <div class="card card-glass shadow-sm p-3 stat-card text-center">
                    <div class="text-muted-glass small fw-bold text-uppercase">Pendientes</div>
                    <div class="display-6 fw-bold my-2 text-warning"><?php echo $totales['total_pendientes']; ?></div>
                    <i class="bi bi-clock-history display-6 opacity-50"></i>
                </div>
            </div>
            <div class="col-md-2-4 col-sm-6 col-12" style="flex: 0 0 auto; width: 20%;">
                <div class="card card-glass shadow-sm p-3 stat-card text-center">
                    <div class="text-muted-glass small fw-bold text-uppercase">En Proceso</div>
                    <div class="display-6 fw-bold my-2 text-info"><?php echo $totales['total_proceso']; ?></div>
                    <i class="bi bi-gear-fill display-6 opacity-50"></i>
                </div>
            </div>
            <div class="col-md-2-4 col-sm-6 col-12" style="flex: 0 0 auto; width: 20%;">
                <div class="card card-glass shadow-sm p-3 stat-card text-center">
                    <div class="text-muted-glass small fw-bold text-uppercase">Resueltos</div>
                    <div class="display-6 fw-bold my-2 text-success"><?php echo $totales['total_resueltos']; ?></div>
                    <i class="bi bi-check-circle-fill display-6 opacity-50"></i>
                </div>
            </div>
            <div class="col-md-2-4 col-sm-6 col-12" style="flex: 0 0 auto; width: 20%;">
                <div class="card card-glass shadow-sm p-3 stat-card text-center">
                    <div class="text-muted-glass small fw-bold text-uppercase">Cerrados</div>
                    <div class="display-6 fw-bold my-2 text-white-50"><?php echo $totales['total_cerrados']; ?></div>
                    <i class="bi bi-lock-fill display-6 opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- Rendimiento de Administradores / Agentes -->
            <div class="col-md-6">
                <div class="card card-glass shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-person-badge-fill me-2"></i>Carga de Trabajo por Administrador</h5>
                        <div class="table-responsive">
                            <table class="table table-glass align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Administrador</th>
                                        <th class="text-center">Asignados</th>
                                        <th class="text-center">En Proceso</th>
                                        <th class="text-center">Resueltos</th>
                                        <th class="text-center">Cerrados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($res_agentes) > 0): ?>
                                        <?php while($ag = mysqli_fetch_assoc($res_agentes)): ?>
                                            <tr>
                                                <td class="fw-bold"><?php echo e($ag['admin']); ?></td>
                                                <td class="text-center"><span class="badge bg-light text-dark"><?php echo $ag['total_asignados']; ?></span></td>
                                                <td class="text-center"><span class="badge bg-info text-dark"><?php echo $ag['en_proceso']; ?></span></td>
                                                <td class="text-center"><span class="badge bg-success"><?php echo $ag['resueltos']; ?></span></td>
                                                <td class="text-center"><span class="badge bg-dark text-white"><?php echo $ag['cerrados']; ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted-glass py-3">No hay tickets asignados a agentes aún.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categorías más frecuentes (Problemas comunes) -->
            <div class="col-md-6">
                <div class="card card-glass shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-tags-fill me-2"></i>Problemas Más Frecuentes (Categorías)</h5>
                        <div class="table-responsive">
                            <table class="table table-glass align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Categoría de Incidencia</th>
                                        <th class="text-end">Total de Casos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($res_categorias) > 0): ?>
                                        <?php while($cat = mysqli_fetch_assoc($res_categorias)): ?>
                                            <tr>
                                                <td class="fw-bold"><?php echo e($cat['categoria']); ?></td>
                                                <td class="text-end"><span class="badge bg-light text-dark px-3"><?php echo $cat['total']; ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="2" class="text-center text-muted-glass py-3">No hay datos de categorías.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colegios con más solicitudes -->
            <div class="col-md-6">
                <div class="card card-glass shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-building-fill me-2"></i>Colegios con Mayor Volumen de Soporte</h5>
                        <div class="table-responsive">
                            <table class="table table-glass align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Institución / Colegio</th>
                                        <th class="text-end">Tickets Registrados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($res_colegios) > 0): ?>
                                        <?php while($col = mysqli_fetch_assoc($res_colegios)): ?>
                                            <tr>
                                                <td class="fw-bold"><?php echo e($col['colegio']); ?></td>
                                                <td class="text-end"><span class="badge bg-light text-dark px-3"><?php echo $col['total']; ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="2" class="text-center text-muted-glass py-3">No hay registros de colegios.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuarios / Profesores más activos -->
            <div class="col-md-6">
                <div class="card card-glass shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-people-fill me-2"></i>Usuarios que Más Solicitan Asistencia</h5>
                        <div class="table-responsive">
                            <table class="table table-glass align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Colegio</th>
                                        <th class="text-end">Tickets</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($res_usuarios) > 0): ?>
                                        <?php while($usr = mysqli_fetch_assoc($res_usuarios)): ?>
                                            <tr>
                                                <td class="fw-bold"><?php echo e($usr['usuario']); ?></td>
                                                <td class="text-muted-glass small"><?php echo e($usr['colegio']); ?></td>
                                                <td class="text-end"><span class="badge bg-light text-dark px-3"><?php echo $usr['total']; ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="text-center text-muted-glass py-3">No hay registros de usuarios.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>