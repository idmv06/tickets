<?php
session_start();
include 'conexion.php';

// Validar que el usuario sea de soporte
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'soporte') {
    header("Location: login.html");
    exit();
}

// Obtener lista de agentes de soporte para el filtro
$sql_agentes = "SELECT usuario_id, nombre FROM usuarios WHERE rol = 'soporte'";
$res_agentes = mysqli_query($conexion, $sql_agentes);

// Capturar filtro si viene por GET
$filtro_asignado = isset($_GET['asignado_a']) ? $_GET['asignado_a'] : '';

// Construir consulta base con LEFT JOIN para el agente asignado
$sql = "SELECT t.*, c.nombre AS nombre_colegio, u.nombre AS nombre_usuario, us.nombre AS nombre_asignado 
        FROM tickets t
        INNER JOIN colegios c ON t.colegio_id = c.colegio_id
        INNER JOIN usuarios u ON t.usuario_id = u.usuario_id
        LEFT JOIN usuarios us ON t.asignado_a = us.usuario_id";

// Aplicar filtro si se seleccionó uno
$params = [];
$types = "";

if ($filtro_asignado !== '') {
    if ($filtro_asignado === 'sin_asignar') {
        $sql .= " WHERE t.asignado_a IS NULL";
    } else {
        $sql .= " WHERE t.asignado_a = ?";
        $params[] = intval($filtro_asignado);
        $types .= "i";
    }
}

$sql .= " ORDER BY t.fecha_creacion DESC";

$stmt = mysqli_prepare($conexion, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bandeja de Soporte - Admin</title>
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
        .table-glass {
            color: #ffffff;
            background-color: transparent !important;
        }
        .table-glass th, .table-glass td {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border: none;
        }
        .text-muted-glass {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        .btn-morado {
            background-color: #ffffff !important;
            color: rgb(139, 20, 175) !important;
            font-weight: 600;
        }
        .btn-morado:hover {
            background-color: rgb(80, 4, 151) !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark mb-4 p-3 border-bottom border-white border-opacity-10">
        <div class="container d-flex justify-content-between">
            <span class="navbar-brand fw-bold"><i class="bi bi-shield-lock-fill me-2"></i>Panel de Soporte Técnico</span>
            <div>
                <span class="me-3 text-white-50">Hola, <?php echo e($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm fw-bold"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-glass shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h3 class="fw-bold mb-0"><i class="bi bi-inbox-fill me-2"></i>Bandeja General de Tickets</h3>
                        
                        <!-- Formulario de Filtro por Asignación -->
                        <form method="GET" action="" class="d-flex align-items-center gap-2">
                            <label for="asignado_a" class="text-nowrap fw-bold text-muted-glass">Filtrar por:</label>
                            <select name="asignado_a" id="asignado_a" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">-- Todos los tickets --</option>
                                <option value="sin_asignar" <?php if($filtro_asignado === 'sin_asignar') echo 'selected'; ?>>Sin asignar</option>
                                <?php 
                                mysqli_data_seek($res_agentes, 0);
                                while($ag = mysqli_fetch_assoc($res_agentes)) {
                                    $sel = ($filtro_asignado == $ag['usuario_id']) ? 'selected' : '';
                                    echo '<option value="' . $ag['usuario_id'] . '" ' . $sel . '>Asignado a: ' . e($ag['nombre']) . '</option>';
                                }
                                ?>
                            </select>
                        </form>
                    </div>
                </div>
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
                                <th>Reportado por</th>
                                <th>Estado</th>
                                <th>Asignado a</th>
                                <th>Fecha</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($resultado) > 0): ?>
                                <?php while($t = mysqli_fetch_assoc($resultado)): ?>
                                    <tr>
                                        <td class="fw-bold">#<?php echo $t['ticket_id']; ?></td>
                                        <td><?php echo e($t['asunto']); ?></td>
                                        <td><?php echo e($t['nombre_colegio']); ?></td>
                                        <td><?php echo e($t['nombre_usuario']); ?></td>
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
                                                <span class="badge bg-light text-dark"><i class="bi bi-person-check-fill me-1"></i><?php echo e($t['nombre_asignado']); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger text-white">Sin asignar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted-glass"><?php echo e($t['fecha_creacion']); ?></td>
                                        <td class="text-end">
                                            <a href="ver_ticket.php?id=<?php echo $t['ticket_id']; ?>" class="btn btn-morado btn-sm">
                                                <i class="bi bi-eye-fill me-1"></i> Atender
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted-glass">No se encontraron tickets con este filtro.</td>
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